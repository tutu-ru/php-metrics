<?php
declare(strict_types=1);

namespace TutuRu\Metrics\MetricsSession;

interface MetricsSessionInterface
{
    public function count(string $key, int $value, array $tags = []): MetricsSessionInterface;

    public function increment(string $key, array $tags = []): MetricsSessionInterface;

    public function decrement(string $key, array $tags = []): MetricsSessionInterface;

    public function timing(string $key, float $seconds, array $tags = []): MetricsSessionInterface;

    /*
     * Иногда так хочется померить среднее число попугаев, 90й процентиль попугаев и т.д.
     * А такая возможность (несколько разных интересных агрегаций) в statsd есть только у таймеров.
     */
    public function measureAsTiming(string $key, int $ms, array $tags = []): MetricsSessionInterface;

    public function gauge(string $key, int $value, array $tags = []): MetricsSessionInterface;

    public function send(): void;
}
