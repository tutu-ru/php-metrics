<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetricsExporter;

use Psr\Log\LoggerInterface;
use TutuRu\Config\ConfigContainer;

class MemoryMetricsExporterFactory
{
    public static function create(ConfigContainer $config, ?LoggerInterface $logger = null): MemoryMetricsExporter
    {
        $exporter = new MemoryMetricsExporter($config);
        if (!is_null($logger)) {
            $exporter->setLogger($logger);
        }
        return $exporter;
    }
}
