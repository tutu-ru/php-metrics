<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use Psr\Log\LoggerInterface;
use TutuRu\Config\Config;
use TutuRu\Metrics\MetricsSession\UdpMetricsSessionFactory;

class UdpMetricsFactory
{
    public static function create(Config $config, LoggerInterface $logger = null): Metrics
    {
        return new Metrics($config, new UdpMetricsSessionFactory(), $logger);
    }
}
