<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetricExporter;

use TutuRu\Config\ConfigContainer;

class MemoryMetricExporterFactory
{
    public static function create(ConfigContainer $config): MemoryMetricExporter
    {
        return new MemoryMetricExporter($config);
    }
}
