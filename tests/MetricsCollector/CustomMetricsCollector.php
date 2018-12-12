<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

class CustomMetricsCollector extends BaseMetricsCollector
{
    protected function onSave(): void
    {
        $this->gauge('metrics_custom', 50);
    }

    protected function getTimersMetricName(): string
    {
        return 'metrics_main';
    }

    protected function getTimersMetricTags(): array
    {
        return ['env' => 'test'];
    }
}
