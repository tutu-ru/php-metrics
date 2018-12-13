<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

class NullMetricsExporter implements MetricsExporterInterface
{
    public function count(string $key, int $value, array $tags = []): MetricsExporterInterface
    {
        return $this;
    }

    public function increment(string $key, array $tags = []): MetricsExporterInterface
    {
        return $this;
    }

    public function decrement(string $key, array $tags = []): MetricsExporterInterface
    {
        return $this;
    }

    public function timing(string $key, float $seconds, array $tags = []): MetricsExporterInterface
    {
        return $this;
    }

    public function gauge(string $key, int $value, array $tags = []): MetricsExporterInterface
    {
        return $this;
    }

    public function saveCollector(MetricsCollector $collector): MetricsExporterInterface
    {
        return $this;
    }

    public function export(): void
    {
    }
}
