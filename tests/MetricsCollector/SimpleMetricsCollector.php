<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

use TutuRu\Metrics\MetricsCollector;

class SimpleMetricsCollector extends MetricsCollector
{
    protected function saveCustomMetrics(): void
    {
    }

    protected function getTimingKey(): string
    {
        return $this->glueNamespaces(['simple', 'metrics', 'collector']);
    }
}
