<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Tests\Metrics\MemoryMetricsExporter\MemoryMetricsExporterFactory;

class MetricsAwareTraitTest extends BaseTest
{
    public function testSetMetricsExporter()
    {
        $exporter = MemoryMetricsExporterFactory::create($this->config, new NullLogger());

        $object = new MetricsAwareObject();
        $this->assertNull($object->getMetricsExporter());

        $object->setMetricsExporter($exporter);
        $this->assertSame($exporter, $object->getMetricsExporter());
    }
}
