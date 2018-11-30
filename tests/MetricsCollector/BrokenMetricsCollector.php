<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

use TutuRu\Metrics\MetricsCollector;

class BrokenMetricsCollector extends MetricsCollector
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
