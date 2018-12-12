<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\MetricsCollector;
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

        $this->assertEquals(
            ['counter:10|c|#app:unittest', 'counter:20|c|#app:unittest'],
            $exporter->getRawExportedMetrics()
        );
    }


    public function testIncrement()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->increment('counter');
        $exporter->increment('counter');
        $exporter->export();

        $this->assertEquals(
            ['counter:1|c|#app:unittest', 'counter:1|c|#app:unittest'],
            $exporter->getRawExportedMetrics()
        );
    }


    public function testDecrement()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->decrement('counter');
        $exporter->decrement('counter');
        $exporter->export();

        $this->assertEquals(
            ['counter:-1|c|#app:unittest', 'counter:-1|c|#app:unittest'],
            $exporter->getRawExportedMetrics()
        );
    }


    public function testTiming()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->timing('test', 25);
        $exporter->timing('test', 40);
        $exporter->export();

        $this->assertEquals(
            ['test:25000|ms|#app:unittest', 'test:40000|ms|#app:unittest'],
            $exporter->getRawExportedMetrics()
        );
    }


    public function testMeasureAsTiming()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->measureAsTiming('test', 20000);
        $exporter->measureAsTiming('test', 45000);
        $exporter->export();

        $this->assertEquals(
            ['test:20000|ms|#app:unittest', 'test:45000|ms|#app:unittest'],
            $exporter->getRawExportedMetrics()
        );
    }


    public function testGauge()
    {
        $exporter = $this->getMetricsExporter();
        $exporter->gauge('gauge', 2);
        $exporter->gauge('gauge', 4);
        $exporter->export();

        $this->assertEquals(
            ['gauge:2|g|#app:unittest', 'gauge:4|g|#app:unittest'],
            $exporter->getRawExportedMetrics()
        );
    }


    public function testSaveCollector()
    {
        $collector = new CustomMetricsCollector();
        $collector->addTiming(500);

        $exporter = $this->getMetricsExporter();
        $exporter->saveCollector($collector);
        $exporter->export();

        $this->assertEquals(
            [
                'metrics_main:500000|ms|#env:test,app:unittest',
                'metrics_custom_count:50|c|#app:unittest',
                'metrics_custom_inc:1|c|#app:unittest',
                'metrics_custom_dec:-1|c|#app:unittest',
                'metrics_custom_timing:500000|ms|#app:unittest',
                'metrics_custom_as_timing:5|ms|#app:unittest',
                'metrics_custom_gauge:2|g|#app:unittest',
            ],
            $exporter->getRawExportedMetrics()
        );
    }


    /**
     * @dataProvider collectorWithExceptionDataProvider
     * @param MetricsCollector $collector
     */
    public function testSaveCollectorWithExceptionInTimers(MetricsCollector $collector)
    {
        $collector->addTiming(500);
        $exporter = $this->getMetricsExporter();
        $exporter->saveCollector($collector);
        $exporter->export();

        $this->assertEquals([], $exporter->getRawExportedMetrics());
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

        foreach ($exporter->getRawExportedMetrics() as $message) {
            $this->assertStringEndsWith("#test:phpunit,app:unittest", $message);
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

        foreach ($exporter->getRawExportedMetrics() as $message) {
            $this->assertStringStartsWith("test_", $message);
        }
    }
}
