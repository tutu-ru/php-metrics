<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Tests\Metrics\MemoryStatsdExporter\MemoryStatsdExporterClient;

class StatsdExporterAwareTraitTest extends BaseTest
{
    public function testSetMetricsExporter()
    {
        $statsdExporterClient = new MemoryStatsdExporterClient("unittest");

        $object = new StatsdExporterAwareObject();
        $this->assertNull($object->getStatsdExporterClient());

        $object->setStatsdExporterClient($statsdExporterClient);
        $this->assertSame($statsdExporterClient, $object->getStatsdExporterClient());
    }
}
