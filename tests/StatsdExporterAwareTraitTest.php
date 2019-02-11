<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Tests\Metrics\MemoryMetricExporter\MemoryMetricExporterFactory;

class StatsdExporterAwareTraitTest extends BaseTest
{
    public function testSetMetricsExporter()
    {
        $statsdExporterClient = MemoryMetricExporterFactory::create($this->config);

        $object = new StatsdExporterAwareObject();
        $this->assertNull($object->getStatsdExporterClient());

        $object->setStatsdExporterClient($statsdExporterClient);
        $this->assertSame($statsdExporterClient, $object->getStatsdExporterClient());
    }
}
