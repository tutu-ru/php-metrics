<?php
declare(strict_types=1);

namespace TutuRu\Metrics\MetricsSession;

use TutuRu\Metrics\MetricsConfig;
use TutuRu\Metrics\SessionParams;

class UdpMetricsSessionFactory implements MetricsSessionFactoryInterface
{
    public function createSession(SessionParams $params, MetricsConfig $config): MetricsSessionInterface
    {
        return new UdpMetricsSession($config, $params);
    }

    public function createNullSession(): MetricsSessionInterface
    {
        return new NullMetricsSession();
    }
}
