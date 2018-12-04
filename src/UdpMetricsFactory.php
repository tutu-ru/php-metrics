<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use Psr\Log\LoggerInterface;
use TutuRu\Config\ConfigContainer;
use TutuRu\Metrics\MetricsSession\UdpMetricsSessionFactory;

class UdpMetricsFactory
{
    public static function createSessionRegistry(
        ConfigContainer $config,
        LoggerInterface $logger = null
    ): SessionRegistry {
        return new SessionRegistry($config, new UdpMetricsSessionFactory(), $logger);
    }
}
