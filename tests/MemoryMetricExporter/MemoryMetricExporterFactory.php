<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetricExporter;

use TutuRu\Config\ConfigInterface;

class MemoryMetricExporterFactory
{
    public static function create(ConfigInterface $config): MemoryMetricExporter
    {
        return new MemoryMetricExporter($config);
    }
}
