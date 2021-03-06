<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\NullStatsdExporterClient;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenCustomMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenNameMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\SimpleMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\CustomMetricsCollector;

class MetricCollectorTest extends BaseTest
{
    public function testTimingMetrics()
    {
        $collector = new SimpleMetricsCollector();
        $collector->startTiming();
        $collector->endTiming();
        $collector->sendToStatsdExporter(new NullStatsdExporterClient());

        $metrics = $collector->getMetrics();
        $this->assertCount(1, $metrics);
        $this->assertEquals(SimpleMetricsCollector::class, $metrics[0]['timing'][0]);
        $this->assertGreaterThan(0, $metrics[0]['timing'][1]);
        $this->assertEquals(['debug' => 1], $metrics[0]['timing'][2]);
    }


    public function testEndTimingWithoutStart()
    {
        $collector = new SimpleMetricsCollector();
        $collector->endTiming();
        $collector->sendToStatsdExporter(new NullStatsdExporterClient());

        $this->assertEquals([], $collector->getMetrics());
    }


    public function testAddTiming()
    {
        $collector = new SimpleMetricsCollector();
        $collector->addTiming(500);
        $collector->sendToStatsdExporter(new NullStatsdExporterClient());

        $this->assertEquals(
            [
                ['timing' => [SimpleMetricsCollector::class, 500, ['debug' => 1]]]
            ],
            $collector->getMetrics()
        );
    }

    public function testGetTiming()
    {
        $collector = new SimpleMetricsCollector();
        $collector->startTiming();
        usleep(10);
        $collector->endTiming();
        $this->assertGreaterThan(0, $collector->getTiming());
    }


    public function testGetTimingIsNullBeforeEndTiming()
    {
        $collector = new SimpleMetricsCollector();
        $this->assertNull($collector->getTiming());
        $collector->startTiming();
        $this->assertNull($collector->getTiming());
    }


    public function testGetTimingNotNullAfterAddTiming()
    {
        $collector = new SimpleMetricsCollector();
        $collector->addTiming(100);
        $this->assertEquals(100, $collector->getTiming());
    }


    public function testGetTimingIsNullAfterStartTiming()
    {
        $collector = new SimpleMetricsCollector();
        $collector->addTiming(100);
        $collector->startTiming();
        $this->assertNull($collector->getTiming());
    }


    public function testCustomMetrics()
    {
        $collector = new CustomMetricsCollector();
        $collector->addTiming(500);
        $collector->sendToStatsdExporter(new NullStatsdExporterClient());

        $this->assertEquals(
            [
                ['timing' => ['metrics_main', 500, ['env' => 'test']]],
                ['count' => ['metrics_custom_count', 50, []]],
                ['increment' => ['metrics_custom_inc', []]],
                ['decrement' => ['metrics_custom_dec', []]],
                ['timing' => ['metrics_custom_timing', 500, []]],
                ['gauge' => ['metrics_custom_gauge', 2, []]],
                ['summary' => ['metrics_custom_summary', 500, []]],
            ],
            $collector->getMetrics()
        );
    }


    public function testExceptionInOnSave()
    {
        $this->expectException(\Exception::class);
        $collector = new BrokenCustomMetricsCollector();
        $collector->addTiming(500);
        $collector->sendToStatsdExporter(new NullStatsdExporterClient());
    }


    public function testExceptionInGetTimingKey()
    {
        $this->expectException(\Exception::class);
        $collector = new BrokenNameMetricsCollector();
        $collector->addTiming(500);
        $collector->sendToStatsdExporter(new NullStatsdExporterClient());
    }


    public function testSecondSave()
    {
        $collector = new SimpleMetricsCollector();
        $collector->startTiming();
        $collector->endTiming();
        $collector->sendToStatsdExporter(new NullStatsdExporterClient());
        $collector->sendToStatsdExporter(new NullStatsdExporterClient());

        $this->assertCount(1, $collector->getMetrics());
    }
}
