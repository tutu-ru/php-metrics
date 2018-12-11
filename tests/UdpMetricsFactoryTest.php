<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\SessionRegistry;
use TutuRu\Metrics\MetricsExporter\UdpMetricsExporter;
use TutuRu\Metrics\SessionNames;
use TutuRu\Metrics\UdpMetricsFactory;

class UdpMetricsFactoryTest extends BaseTest
{
    public function testCreate()
    {
        $metrics = UdpMetricsFactory::createSessionRegistry($this->config);
        $this->assertInstanceOf(SessionRegistry::class, $metrics);
        $this->assertInstanceOf(UdpMetricsExporter::class, $metrics->getSession(SessionNames::NAME_DEFAULT));
    }
}
