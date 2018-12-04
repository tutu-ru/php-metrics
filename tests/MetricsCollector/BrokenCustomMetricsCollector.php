<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

class BrokenCustomMetricsCollector extends BaseMetricsCollector
{
    protected function saveCustomMetrics(): void
    {
        throw new \Exception();
    }

    protected function getTimingKey(): string
    {
        return $this->glueNamespaces(['simple', 'metrics', 'broken']);
    }
}
