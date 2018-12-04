<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

class CustomMetricsCollector extends BaseMetricsCollector
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
