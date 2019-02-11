<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Tests\Metrics\MemoryMetricExporter\MemoryMetricExporter;

class StatsdExporterAwareTraitTest extends BaseTest
{
    public function testSetMetricsExporter()
    {
        $statsdExporterClient = new MemoryMetricExporter("unittest");

        $object = new StatsdExporterAwareObject();
        $this->assertNull($object->getStatsdExporterClient());

        $object->setStatsdExporterClient($statsdExporterClient);
        $this->assertSame($statsdExporterClient, $object->getStatsdExporterClient());
    }
}
