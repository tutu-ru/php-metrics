<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

class BrokenCustomMetricsCollector extends BaseMetricsCollector
{
    protected function onSave(): void
    {
        throw new \Exception();
    }


    protected function getTimersMetricName(): string
    {
        return self::class;
    }


    protected function getTimersMetricTags(): array
    {
        return [];
    }
}
