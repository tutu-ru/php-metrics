<?php
declare(strict_types=1);

namespace TutuRu\Metrics\MetricsExporter;

use TutuRu\Metrics\MetricsCollector;
use TutuRu\Metrics\MetricsExporterInterface;

class NullMetricsExporter implements MetricsExporterInterface
{
    public function count(string $key, int $value, array $tags = []): MetricsSessionInterface
    {
        return $this;
    }

    public function increment(string $key, array $tags = []): MetricsSessionInterface
    {
        return $this;
    }

    public function decrement(string $key, array $tags = []): MetricsSessionInterface
    {
        return $this;
    }

    public function timing(string $key, float $seconds, array $tags = []): MetricsSessionInterface
    {
        return $this;
    }

    public function measureAsTiming(string $key, int $ms, array $tags = []): MetricsSessionInterface
    {
        return $this;
    }

    public function gauge(string $key, int $value, array $tags = []): MetricsSessionInterface
    {
        return $this;
    }

    public function send(): void
    {
    }

    public function collect(MetricsCollector $collector): MetricsExporterInterface
    {
        // TODO: Implement collect() method.
    }

    public function export(): void
    {
        // TODO: Implement export() method.
    }
}
