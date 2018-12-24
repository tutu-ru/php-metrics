<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Tests\Metrics\MemoryMetricExporter\MemoryMetric;

class MemoryMetricTest extends BaseTest
{
    /**
     * @dataProvider rawStringDataProvider
     */
    public function testCreateFromRawString($rawString, $name, $value, $unit, $tags)
    {
        $metric = MemoryMetric::createFromRawString($rawString);
        $this->assertInstanceOf(MemoryMetric::class, $metric);
        $this->assertEquals($name, $metric->getName());
        $this->assertEquals($value, $metric->getValue());
        $this->assertEquals($unit, $metric->getUnit());
        $this->assertEquals($tags, $metric->getTags());
    }


    public function rawStringDataProvider()
    {
        return [
            [
                'metrics_main:500000|ms|#env:test,app:unittest,env:test',
                'metrics_main',
                500000,
                'ms',
                ['app' => 'unittest', 'env' => 'test']
            ],
            [
                'metrics_custom_count:50|c|#app:unittest',
                'metrics_custom_count',
                50,
                'c',
                ['app' => 'unittest']
            ],
            [
                'metrics_custom_inc:1|c|#app:unittest',
                'metrics_custom_inc',
                1,
                'c',
                ['app' => 'unittest']
            ],
            [
                'metrics_custom_dec:-1|c|#app:unittest',
                'metrics_custom_dec',
                -1,
                'c',
                ['app' => 'unittest']
            ],
            [
                'metrics_custom_timing:500000|ms|#app:unittest',
                'metrics_custom_timing',
                500000,
                'ms',
                ['app' => 'unittest']
            ],
            [
                'metrics_custom_gauge:2|g|#app:unittest',
                'metrics_custom_gauge',
                2,
                'g',
                ['app' => 'unittest']
            ],
        ];
    }


    public function testCreateWithUnknownFormat()
    {
        $this->expectException(\Exception::class);
        MemoryMetric::createFromRawString('metrics_main:500000|ms');
    }
}
