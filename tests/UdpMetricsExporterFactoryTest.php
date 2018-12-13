<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\NullMetricsExporter;
use TutuRu\Metrics\UdpMetricsExporter;
use TutuRu\Metrics\UdpMetricsExporterFactory;

class UdpMetricsExporterFactoryTest extends BaseTest
{
    public function testCreate()
    {
        $exporter = UdpMetricsExporterFactory::create($this->config, new NullLogger());
        $this->assertInstanceOf(UdpMetricsExporter::class, $exporter);
    }


    public function testCreateDisabled()
    {
        $this->config->setApplicationValue('metrics.enabled', false);
        $exporter = UdpMetricsExporterFactory::create($this->config, new NullLogger());
        $this->assertInstanceOf(NullMetricsExporter::class, $exporter);
    }


    public function testCreateWithoutConnectionData()
    {
        $this->config->setApplicationValue('metrics.exporter', []);
        $exporter = UdpMetricsExporterFactory::create($this->config, new NullLogger());
        $this->assertInstanceOf(NullMetricsExporter::class, $exporter);
    }
}
