<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\MetricsCollector;
use TutuRu\Tests\Metrics\MemoryMetricsExporter\MemoryMetric;
use TutuRu\Tests\Metrics\MemoryMetricsExporter\MemoryMetricsExporter;
use TutuRu\Tests\Metrics\MemoryMetricsExporter\MemoryMetricsExporterFactory;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenCustomMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenNameMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenTagsMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\CustomMetricsCollector;

class MetricsExporterTest extends BaseTest
{
    private function getMetricsExporter(): MemoryMetricsExporter
    {
        return MemoryMetricsExporterFactory::create($this->config, new NullLogger());
    }


    public function testConnectionCreateOnExport()
    {
        $exporter = $this->getMetricsExporter();
        $this->assertNull($exporter->getLastCreatedConnection());

        $exporter->count('counter', 10);
        $this->assertCount(0, $exporter->getLastCreatedConnection()->getMessages());

        $exporter->export();
        $this->assertCount(1, $exporter->getLastCreatedConnection()->getMessages());
    }


    public function testConnectionRecreateAfterExport()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->increment('c');
        $exporter->export();
        $connection = $exporter->getLastCreatedConnection();

        $exporter->decrement('c');
        $exporter->export();
        $this->assertNotSame($connection, $exporter->getLastCreatedConnection());
    }


    public function testCount()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->count('counter', 10);
        $exporter->count('counter', 20);
        $exporter->export();

        $this->assertCount(2, $exporter->getExportedMetrics());
        $this->assertMetric($exporter->getExportedMetrics()[0], 'counter', 10, 'c', ['app' => 'unittest']);
        $this->assertMetric($exporter->getExportedMetrics()[1], 'counter', 20, 'c', ['app' => 'unittest']);
    }


    public function testIncrement()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->increment('counter');
        $exporter->increment('counter');
        $exporter->export();

        $this->assertCount(2, $exporter->getExportedMetrics());
        $this->assertMetric($exporter->getExportedMetrics()[0], 'counter', 1, 'c', ['app' => 'unittest']);
        $this->assertMetric($exporter->getExportedMetrics()[1], 'counter', 1, 'c', ['app' => 'unittest']);
    }


    public function testDecrement()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->decrement('counter');
        $exporter->decrement('counter');
        $exporter->export();

        $this->assertCount(2, $exporter->getExportedMetrics());
        $this->assertMetric($exporter->getExportedMetrics()[0], 'counter', -1, 'c', ['app' => 'unittest']);
        $this->assertMetric($exporter->getExportedMetrics()[1], 'counter', -1, 'c', ['app' => 'unittest']);
    }


    public function testTiming()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->timing('test', 25);
        $exporter->timing('test', 40);
        $exporter->export();

        $this->assertCount(2, $exporter->getExportedMetrics());
        $this->assertMetric($exporter->getExportedMetrics()[0], 'test', 25000, 'ms', ['app' => 'unittest']);
        $this->assertMetric($exporter->getExportedMetrics()[1], 'test', 40000, 'ms', ['app' => 'unittest']);
    }


    public function testGauge()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->gauge('gauge', 2);
        $exporter->gauge('gauge', 4);
        $exporter->export();

        $this->assertCount(2, $exporter->getExportedMetrics());
        $this->assertMetric($exporter->getExportedMetrics()[0], 'gauge', 2, 'g', ['app' => 'unittest']);
        $this->assertMetric($exporter->getExportedMetrics()[1], 'gauge', 4, 'g', ['app' => 'unittest']);
    }


    public function testExportCollector()
    {
        $exporter = $this->getMetricsExporter();

        $collector = new CustomMetricsCollector();
        $collector->addTiming(500);
        $collector->sendTo($exporter);

        $exporter->export();
        $this->assertCount(6, $exporter->getExportedMetrics());
        $this->assertMetric(
            $exporter->getExportedMetrics('metrics_main')[0],
            'metrics_main',
            500000,
            'ms',
            ['app' => 'unittest', 'env' => 'test']
        );
        $this->assertMetric(
            $exporter->getExportedMetrics('metrics_custom_count')[0],
            'metrics_custom_count',
            50,
            'c',
            ['app' => 'unittest']
        );
        $this->assertMetric(
            $exporter->getExportedMetrics('metrics_custom_inc')[0],
            'metrics_custom_inc',
            1,
            'c',
            ['app' => 'unittest']
        );
        $this->assertMetric(
            $exporter->getExportedMetrics('metrics_custom_dec')[0],
            'metrics_custom_dec',
            -1,
            'c',
            ['app' => 'unittest']
        );
        $this->assertMetric(
            $exporter->getExportedMetrics('metrics_custom_timing')[0],
            'metrics_custom_timing',
            500000,
            'ms',
            ['app' => 'unittest']
        );
        $this->assertMetric(
            $exporter->getExportedMetrics('metrics_custom_gauge')[0],
            'metrics_custom_gauge',
            2,
            'g',
            ['app' => 'unittest']
        );
    }


    /**
     * @dataProvider collectorWithExceptionDataProvider
     * @param MetricsCollector $collector
     */
    public function testExportCollectorWithExceptionInTimers(MetricsCollector $collector)
    {
        $this->expectException(\Exception::class);

        $exporter = $this->getMetricsExporter();

        $collector->addTiming(500);
        $collector->sendTo($exporter);

        $exporter->export();
        $this->assertEquals([], $exporter->getExportedMetrics());
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
        $exporter = $this->getMetricsExporter();
        $exporter
            ->count('counter', 10, ['test' => 'phpunit'])
            ->decrement('decrement', ['test' => 'phpunit'])
            ->increment('increment', ['test' => 'phpunit'])
            ->timing('timing', 10, ['test' => 'phpunit'])
            ->gauge('gauge', 20, ['test' => 'phpunit'])
            ->export();

        foreach ($exporter->getExportedMetrics() as $metric) {
            $this->assertEquals(['test' => 'phpunit', 'app' => 'unittest'], $metric->getTags());
        }
    }


    public function testPrepareKey()
    {
        $exporter = $this->getMetricsExporter();
        $exporter
            ->count('test-counter', 10)
            ->decrement('test-decrement')
            ->increment('test-increment')
            ->timing('test-timing', 10)
            ->gauge('test-gauge', 20)
            ->export();

        foreach ($exporter->getExportedMetrics() as $metric) {
            $this->assertStringStartsWith("test_", $metric->getName());
        }
    }


    protected function assertMetric(MemoryMetric $metric, $name, $value, $unit, $tags)
    {
        $this->assertEquals($name, $metric->getName());
        $this->assertEquals($value, $metric->getValue());
        $this->assertEquals($unit, $metric->getUnit());
        $this->assertEquals($tags, $metric->getTags());
    }
}
