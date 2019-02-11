<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use Psr\Log\Test\TestLogger;
use TutuRu\Metrics\StatsdExporterClient;
use TutuRu\Metrics\StatsdExporterClientFactory;
use TutuRu\Metrics\MetricCollector;
use TutuRu\Tests\Metrics\MemoryMetricExporter\MemoryMetric;
use TutuRu\Tests\Metrics\MemoryMetricExporter\MemoryMetricExporter;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenCustomMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenNameMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenTagsMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\CustomMetricsCollector;

class StatsdExporterClientTest extends BaseTest
{
    public function testExporterClientInterface()
    {
        $exporter = StatsdExporterClientFactory::create($this->config, new TestLogger());

        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->gauge('g', 1));
        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->count('c', 1));
        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->increment('c'));
        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->decrement('c'));
        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->timing('t', 1));
        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->summary('s', 1));

        $exporter->save();
    }


    public function testConnectionCreateOnExport()
    {
        $exporterClient = $this->getMetricsExporterClient();
        $this->assertNull($exporterClient->getLastCreatedConnection());

        $exporterClient->count('counter', 10);
        $this->assertCount(0, $exporterClient->getLastCreatedConnection()->getMessages());

        $exporterClient->save();
        $this->assertCount(1, $exporterClient->getLastCreatedConnection()->getMessages());
    }


    public function testConnectionRecreateAfterExport()
    {
        $exporterClient = $this->getMetricsExporterClient();
        $exporterClient->increment('c');
        $exporterClient->save();
        $connection = $exporterClient->getLastCreatedConnection();

        $exporterClient->decrement('c');
        $exporterClient->save();
        $this->assertNotSame($connection, $exporterClient->getLastCreatedConnection());
    }


    public function testCount()
    {
        $exporterClient = $this->getMetricsExporterClient();
        $exporterClient->count('counter', 10);
        $exporterClient->count('counter', 20);
        $exporterClient->save();

        $this->assertCount(2, $exporterClient->getExportedMetrics());
        $this->assertMetric($exporterClient->getExportedMetrics()[0], 'counter', 10, 'c', ['app' => 'unittest']);
        $this->assertMetric($exporterClient->getExportedMetrics()[1], 'counter', 20, 'c', ['app' => 'unittest']);
    }


    public function testIncrement()
    {
        $exporterClient = $this->getMetricsExporterClient();
        $exporterClient->increment('counter');
        $exporterClient->increment('counter');
        $exporterClient->save();

        $this->assertCount(2, $exporterClient->getExportedMetrics());
        $this->assertMetric($exporterClient->getExportedMetrics()[0], 'counter', 1, 'c', ['app' => 'unittest']);
        $this->assertMetric($exporterClient->getExportedMetrics()[1], 'counter', 1, 'c', ['app' => 'unittest']);
    }


    public function testDecrement()
    {
        $exporterClient = $this->getMetricsExporterClient();
        $exporterClient->decrement('counter');
        $exporterClient->decrement('counter');
        $exporterClient->save();

        $this->assertCount(2, $exporterClient->getExportedMetrics());
        $this->assertMetric($exporterClient->getExportedMetrics()[0], 'counter', -1, 'c', ['app' => 'unittest']);
        $this->assertMetric($exporterClient->getExportedMetrics()[1], 'counter', -1, 'c', ['app' => 'unittest']);
    }


    public function testTiming()
    {
        $exporterClient = $this->getMetricsExporterClient();
        $exporterClient->timing('test', 25);
        $exporterClient->timing('test', 40);
        $exporterClient->save();

        $this->assertCount(2, $exporterClient->getExportedMetrics());
        $this->assertMetric($exporterClient->getExportedMetrics()[0], 'test', 25000, 'ms', ['app' => 'unittest']);
        $this->assertMetric($exporterClient->getExportedMetrics()[1], 'test', 40000, 'ms', ['app' => 'unittest']);
    }


    public function testSummary()
    {
        $exporterClient = $this->getMetricsExporterClient();
        $exporterClient->summary('test', 25);
        $exporterClient->summary('test', 40);
        $exporterClient->save();

        $this->assertCount(2, $exporterClient->getExportedMetrics());
        $this->assertMetric($exporterClient->getExportedMetrics()[0], 'test', 25, 'ms', ['app' => 'unittest']);
        $this->assertMetric($exporterClient->getExportedMetrics()[1], 'test', 40, 'ms', ['app' => 'unittest']);
    }


    public function testGauge()
    {
        $exporterClient = $this->getMetricsExporterClient();
        $exporterClient->gauge('gauge', 2);
        $exporterClient->gauge('gauge', 4);
        $exporterClient->save();

        $this->assertCount(2, $exporterClient->getExportedMetrics());
        $this->assertMetric($exporterClient->getExportedMetrics()[0], 'gauge', 2, 'g', ['app' => 'unittest']);
        $this->assertMetric($exporterClient->getExportedMetrics()[1], 'gauge', 4, 'g', ['app' => 'unittest']);
    }


    public function testExportCollector()
    {
        $exporterClient = $this->getMetricsExporterClient();

        $collector = new CustomMetricsCollector();
        $collector->addTiming(500);
        $collector->sendToStatsdExporter($exporterClient);

        $exporterClient->save();
        $this->assertCount(7, $exporterClient->getExportedMetrics());
        $this->assertMetric(
            $exporterClient->getExportedMetrics('metrics_main')[0],
            'metrics_main',
            500000,
            'ms',
            ['app' => 'unittest', 'env' => 'test']
        );
        $this->assertMetric(
            $exporterClient->getExportedMetrics('metrics_custom_count')[0],
            'metrics_custom_count',
            50,
            'c',
            ['app' => 'unittest']
        );
        $this->assertMetric(
            $exporterClient->getExportedMetrics('metrics_custom_inc')[0],
            'metrics_custom_inc',
            1,
            'c',
            ['app' => 'unittest']
        );
        $this->assertMetric(
            $exporterClient->getExportedMetrics('metrics_custom_dec')[0],
            'metrics_custom_dec',
            -1,
            'c',
            ['app' => 'unittest']
        );
        $this->assertMetric(
            $exporterClient->getExportedMetrics('metrics_custom_timing')[0],
            'metrics_custom_timing',
            500000,
            'ms',
            ['app' => 'unittest']
        );
        $this->assertMetric(
            $exporterClient->getExportedMetrics('metrics_custom_gauge')[0],
            'metrics_custom_gauge',
            2,
            'g',
            ['app' => 'unittest']
        );
        $this->assertMetric(
            $exporterClient->getExportedMetrics('metrics_custom_summary')[0],
            'metrics_custom_summary',
            500,
            'ms',
            ['app' => 'unittest']
        );
    }


    /**
     * @dataProvider collectorWithExceptionDataProvider
     * @param MetricCollector $collector
     */
    public function testExportCollectorWithExceptionInTimers(MetricCollector $collector)
    {
        $this->expectException(\Exception::class);

        $exporterClient = $this->getMetricsExporterClient();

        $collector->addTiming(500);
        $collector->sendToStatsdExporter($exporterClient);

        $exporterClient->save();
        $this->assertEquals([], $exporterClient->getExportedMetrics());
    }


    public function collectorWithExceptionDataProvider()
    {
        return [
            [new BrokenNameMetricsCollector()],
            [new BrokenTagsMetricsCollector()],
            [new BrokenCustomMetricsCollector()],
        ];
    }


    public function testPrepareTags()
    {
        $exporterClient = $this->getMetricsExporterClient();
        $exporterClient
            ->count('counter', 10, ['test' => 'phpunit'])
            ->decrement('decrement', ['test' => 'phpunit'])
            ->increment('increment', ['test' => 'phpunit'])
            ->timing('timing', 10, ['test' => 'phpunit'])
            ->gauge('gauge', 20, ['test' => 'phpunit'])
            ->save();

        foreach ($exporterClient->getExportedMetrics() as $metric) {
            $this->assertEquals(['test' => 'phpunit', 'app' => 'unittest'], $metric->getTags());
        }
    }


    public function testPrepareKey()
    {
        $exporterClient = $this->getMetricsExporterClient();
        $exporterClient
            ->count('test-counter', 10)
            ->decrement('test-decrement')
            ->increment('test-increment')
            ->timing('test-timing', 10)
            ->gauge('test-gauge', 20)
            ->save();

        foreach ($exporterClient->getExportedMetrics() as $metric) {
            $this->assertStringStartsWith("test_", $metric->getName());
        }
    }


    private function assertMetric(MemoryMetric $metric, $name, $value, $unit, $tags)
    {
        $this->assertEquals($name, $metric->getName());
        $this->assertEquals($value, $metric->getValue());
        $this->assertEquals($unit, $metric->getUnit());
        $this->assertEquals($tags, $metric->getTags());
    }


    private function getMetricsExporterClient(): MemoryMetricExporter
    {
        return new MemoryMetricExporter('unittest');
    }
}
