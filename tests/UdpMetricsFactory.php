<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\Metrics;
use TutuRu\Metrics\MetricsSession\UdpMetricsSession;
use TutuRu\Metrics\SessionNames;
use TutuRu\Metrics\UdpMetricsFactory;

class UdpMetricsFactoryTest extends BaseTest
{
    public function testCreate()
    {
        $metrics = UdpMetricsFactory::create($this->config);
        $this->assertInstanceOf(Metrics::class, $metrics);
        $this->assertInstanceOf(UdpMetricsSession::class, $metrics->getSession(SessionNames::NAME_DEFAULT));
    }
}
