<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

interface MetricsExporterInterface
{
    public function count(string $key, int $value, array $tags = []): MetricsExporterInterface;

    public function increment(string $key, array $tags = []): MetricsExporterInterface;

    public function decrement(string $key, array $tags = []): MetricsExporterInterface;

    public function timing(string $key, float $seconds, array $tags = []): MetricsExporterInterface;

    public function gauge(string $key, int $value, array $tags = []): MetricsExporterInterface;

    public function export(): void;
}
