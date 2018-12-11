<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

interface MetricsExporterInterface
{
    public function count(string $key, int $value, array $tags = []): MetricsExporterInterface;

    public function increment(string $key, array $tags = []): MetricsExporterInterface;

    public function decrement(string $key, array $tags = []): MetricsExporterInterface;

    public function timing(string $key, float $seconds, array $tags = []): MetricsExporterInterface;

    /*
     * Иногда так хочется померить среднее число попугаев, 90й процентиль попугаев и т.д.
     * А такая возможность (несколько разных интересных агрегаций) в statsd есть только у таймеров.
     */
    public function measureAsTiming(string $key, int $ms, array $tags = []): MetricsExporterInterface;

    public function gauge(string $key, int $value, array $tags = []): MetricsExporterInterface;

    public function saveCollector(MetricsCollector $collector): MetricsExporterInterface;

    public function export(): void;
}
