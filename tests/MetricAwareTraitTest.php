<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Tests\Metrics\MemoryMetricExporter\MemoryMetricExporterFactory;

class MetricAwareTraitTest extends BaseTest
{
    public function testSetMetricsExporter()
    {
        $statsdExporterClient = MemoryMetricExporterFactory::create($this->config);

        $object = new MetricAwareObject();
        $this->assertNull($object->getStatsdExporterClient());

        $object->setStatsdExporterClient($statsdExporterClient);
        $this->assertSame($statsdExporterClient, $object->getStatsdExporterClient());
    }
}
