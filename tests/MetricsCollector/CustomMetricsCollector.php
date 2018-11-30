<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

use TutuRu\Metrics\MetricsCollector;

class CustomMetricsCollector extends MetricsCollector
{
    protected function saveCustomMetrics(): void
    {
        $this->getSession()->gauge($this->glueNamespaces(['simple', 'metrics', 'custom']), 50);
    }

    protected function getTimingKey(): string
    {
        return $this->glueNamespaces(['simple', 'metrics', 'collector']);
    }
}
