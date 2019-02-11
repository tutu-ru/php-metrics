<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetricExporter;

use TutuRu\Config\ConfigInterface;
use TutuRu\Metrics\MetricConfig;

class MemoryMetricExporterFactory
{
    public static function create(ConfigInterface $config): MemoryMetricExporter
    {
        $metricsConfig = new MetricConfig($config);
        return new MemoryMetricExporter($metricsConfig->getAppName());
    }
}
