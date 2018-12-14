<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Tests\Metrics\MetricsCollector\BrokenCustomMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenNameMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\SimpleMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\CustomMetricsCollector;

class MetricsCollectorTest extends BaseTest
{
    public function testTimingMetrics()
    {
        $collector = new SimpleMetricsCollector();
        $collector->startTiming();
        $collector->endTiming();
        $collector->save();

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
        $collector->save();

        $this->assertEquals([], $collector->getMetrics());
    }


    public function testAddTiming()
    {
        $collector = new SimpleMetricsCollector();
        $collector->addTiming(500);
        $collector->save();

        $this->assertEquals(
            [
                ['timing' => [SimpleMetricsCollector::class, 500, ['debug' => 1]]]
            ],
            $collector->getMetrics()
        );
    }


    public function testCustomMetrics()
    {
        $collector = new CustomMetricsCollector();
        $collector->addTiming(500);
        $collector->save();

        $this->assertEquals(
            [
                ['timing' => ['metrics_main', 500, ['env' => 'test']]],
                ['count' => ['metrics_custom_count', 50, []]],
                ['increment' => ['metrics_custom_inc', []]],
                ['decrement' => ['metrics_custom_dec', []]],
                ['timing' => ['metrics_custom_timing', 500, []]],
                ['gauge' => ['metrics_custom_gauge', 2, []]],
            ],
            $collector->getMetrics()
        );
    }


    public function testExceptionInOnSave()
    {
        $this->expectException(\Exception::class);
        $collector = new BrokenCustomMetricsCollector();
        $collector->addTiming(500);
        $collector->save();
    }


    public function testExceptionInGetTimingKey()
    {
        $this->expectException(\Exception::class);
        $collector = new BrokenNameMetricsCollector();
        $collector->addTiming(500);
        $collector->save();
    }


    public function testSecondSave()
    {
        $collector = new SimpleMetricsCollector();
        $collector->startTiming();
        $collector->endTiming();
        $collector->save();
        $collector->save();

        $this->assertCount(1, $collector->getMetrics());
    }
}
