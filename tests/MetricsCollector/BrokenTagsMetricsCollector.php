<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

class BrokenTagsMetricsCollector extends BaseMetricsCollector
{
    protected function getTimersMetricName(): string
    {
        return self::class;
    }

    protected function getTimersMetricTags(): array
    {
        throw new \Exception("getTimersMetricTags exception");
    }
}
