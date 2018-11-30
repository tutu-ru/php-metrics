<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

use TutuRu\Metrics\MetricsCollector;

class ExporterMetricsCollector extends MetricsCollector
{
    public function __construct()
    {
        $this->setStatsdExporterTimersMetricName('exporter');
    }

    protected function saveCustomMetrics(): void
    {
    }

    protected function getTimingKey(): string
    {
        return $this->glueNamespaces(['simple', 'metrics', 'collector']);
    }
}
