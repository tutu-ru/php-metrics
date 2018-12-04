<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\MetricsConfig;
use TutuRu\Metrics\MetricsSession\NullMetricsSession;
use TutuRu\Metrics\MetricsSession\UdpMetricsSession;
use TutuRu\Metrics\MetricsSession\UdpMetricsSessionFactory;
use TutuRu\Metrics\SessionNames;

class UdpMetricsSessionFactoryTest extends BaseTest
{
    public function testCreateSession()
    {
        $factory = new UdpMetricsSessionFactory();
        $metricsConfig = new MetricsConfig($this->config);
        $session = $factory->createSession(
            $metricsConfig->getSessionParameters(SessionNames::NAME_DEFAULT),
            $metricsConfig
        );
        $this->assertInstanceOf(UdpMetricsSession::class, $session);
    }


    public function testCreateNullSession()
    {
        $factory = new UdpMetricsSessionFactory();
        $session = $factory->createNullSession();
        $this->assertInstanceOf(NullMetricsSession::class, $session);
    }
}
