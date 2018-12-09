<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetrics;

use Psr\Log\LoggerInterface;
use TutuRu\Config\ConfigContainer;
use TutuRu\Metrics\SessionRegistry;

class MemoryMetrics
{
    public static function createSessionRegistry(
        ConfigContainer $config,
        ?LoggerInterface $logger = null,
        $testCase = null
    ) {
        $sessionFactory = new MemoryMetricsSessionFactory($testCase);
        return new SessionRegistry($config, $sessionFactory, $logger);
    }
}
