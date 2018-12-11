<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

abstract class MetricsCollector
{
    private $collectedMetrics;

    /**
     * microtime старта процесса
     * @var float
     */
    private $startTime;

    /**
     * Замеренная длительность процесса в секундах
     * @var float
     */
    private $time;


    protected function count(string $key, int $value, array $tags = [])
    {
        // save action with data in $this->>collectedMetrics
    }

    protected function increment(string $key, array $tags = [])
    {
        // save action with data in $this->>collectedMetrics
    }

    protected function decrement(string $key, array $tags = [])
    {
        // save action with data in $this->>collectedMetrics
    }

    protected function timing(string $key, float $seconds, array $tags = [])
    {
        // save action with data in $this->>collectedMetrics
    }

    protected function measureAsTiming(string $key, int $ms, array $tags = [])
    {
        // save action with data in $this->>collectedMetrics
    }

    protected function gauge(string $key, int $value, array $tags = [])
    {
        // save action with data in $this->>collectedMetrics
    }

    final public function getMetrics()
    {
        return $this->collectedMetrics;
    }

    // не факт что нужен, а если и нужен, то стоит переименовать так,
    // чтоб понятно было, что вызывается перед экспортом
    abstract protected function addCustomMetrics(): void;

    abstract protected function getTimersMetricName(string $metricName): string;

    abstract protected function getTimersMetricTags(string $metricName): string;


    public function startTiming(?float $timeSeconds = null): void
    {
        $this->startTime = is_null($timeSeconds) ? microtime(true) : $timeSeconds;
    }


    public function endTiming(): void
    {
        if (is_null($this->startTime)) {
            $this->time = null;
        } else {
            $this->time = microtime(true) - $this->startTime;
        }
    }


    public function addTiming(float $seconds): void
    {
        $this->time = ($this->time ?? 0) + $seconds;
    }


    public function getTiming(): ?float
    {
        return $this->time;
    }
}
