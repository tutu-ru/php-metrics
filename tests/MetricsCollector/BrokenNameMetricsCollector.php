<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

class BrokenNameMetricsCollector extends BaseMetricsCollector
{
    protected function getTimersMetricName(): string
    {
        throw new \Exception("getTimersMetricName exception");
    }

    protected function getTimersMetricTags(): array
    {
        return [];
    }
}
