<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetricsExporter;

use TutuRu\Config\ConfigContainer;

class MemoryMetricsExporterFactory
{
    public static function create(ConfigContainer $config): MemoryMetricsExporter
    {
        return new MemoryMetricsExporter($config);
    }
}
