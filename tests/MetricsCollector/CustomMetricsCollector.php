<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

class CustomMetricsCollector extends BaseMetricsCollector
{
    protected function onSave(): void
    {
        $this->count('metrics_custom_count', 50);
        $this->increment('metrics_custom_inc');
        $this->decrement('metrics_custom_dec');
        $this->timing('metrics_custom_timing', 500);
        $this->measureAsTiming('metrics_custom_as_timing', 5);
        $this->gauge('metrics_custom_gauge', 2);
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
