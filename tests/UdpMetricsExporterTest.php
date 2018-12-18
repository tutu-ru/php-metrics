<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\UdpMetricsExporter;
use TutuRu\Metrics\UdpMetricsExporterFactory;

class UdpMetricsExporterTest extends BaseTest
{
    public function testExporterInterface()
    {
        $exporter = UdpMetricsExporterFactory::create($this->config, new NullLogger());

        $this->assertInstanceOf(UdpMetricsExporter::class, $exporter->gauge('g', 1));
        $this->assertInstanceOf(UdpMetricsExporter::class, $exporter->count('c', 1));
        $this->assertInstanceOf(UdpMetricsExporter::class, $exporter->increment('c'));
        $this->assertInstanceOf(UdpMetricsExporter::class, $exporter->decrement('c'));
        $this->assertInstanceOf(UdpMetricsExporter::class, $exporter->timing('t', 1));

        $exporter->export();
    }
}
