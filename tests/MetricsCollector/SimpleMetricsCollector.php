<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

class SimpleMetricsCollector extends BaseMetricsCollector
{
    protected function getTimersMetricName(): string
    {
        return self::class;
    }

    protected function getTimersMetricTags(): array
    {
        return ['debug' => 1];
    }
}
