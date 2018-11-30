<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

class BrokenTimingKeyMetricsCollector extends BaseMetricsCollector
{
    protected function saveCustomMetrics(): void
    {
    }

    protected function getTimingKey(): string
    {
        throw new \Exception();
    }
}
