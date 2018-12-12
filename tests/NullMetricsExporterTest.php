<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\NullMetricsExporter;
use TutuRu\Tests\Metrics\MetricsCollector\SimpleMetricsCollector;

class NullMetricsExporterTest extends BaseTest
{
    public function testExporterInterface()
    {
        $exporter = new NullMetricsExporter();

        $this->assertInstanceOf(NullMetricsExporter::class, $exporter->gauge('g', 1));
        $this->assertInstanceOf(NullMetricsExporter::class, $exporter->count('c', 1));
        $this->assertInstanceOf(NullMetricsExporter::class, $exporter->increment('c'));
        $this->assertInstanceOf(NullMetricsExporter::class, $exporter->decrement('c'));
        $this->assertInstanceOf(NullMetricsExporter::class, $exporter->timing('t', 1));
        $this->assertInstanceOf(NullMetricsExporter::class, $exporter->measureAsTiming('a', 1));
        $this->assertInstanceOf(NullMetricsExporter::class, $exporter->saveCollector(new SimpleMetricsCollector()));

        $exporter->export();
    }
}
