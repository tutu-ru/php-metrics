<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

abstract class MetricCollector
{
    private $collectedMetrics = [];

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

    /** @var bool */
    private $wasSaved = false;


    abstract protected function getTimersMetricName(): string;


    abstract protected function getTimersMetricTags(): array;


    final public function getMetrics(): array
    {
        return $this->collectedMetrics;
    }


    public function sendToStatsdExporter(StatsdExporterClientInterface $statsdExporterClient)
    {
        $this->save();
        foreach ($this->getMetrics() as $metric) {
            $action = key($metric);
            $params = current($metric);
            call_user_func_array([$statsdExporterClient, $action], $params);
        }
    }


    private function save()
    {
        if ($this->wasSaved) {
            return;
        }

        if (!is_null($this->time)) {
            $this->timing($this->getTimersMetricName(), $this->time, $this->getTimersMetricTags());
        }
        $this->onSave();

        $this->wasSaved = true;
    }


    protected function onSave(): void
    {
    }


    final protected function count(string $key, float $value, array $tags = [])
    {
        $this->collectedMetrics[] = ['count' => [$key, $value, $tags]];
    }


    final protected function increment(string $key, array $tags = [])
    {
        $this->collectedMetrics[] = ['increment' => [$key, $tags]];
    }


    final protected function decrement(string $key, array $tags = [])
    {
        $this->collectedMetrics[] = ['decrement' => [$key, $tags]];
    }


    final protected function timing(string $key, float $seconds, array $tags = [])
    {
        $this->collectedMetrics[] = ['timing' => [$key, $seconds, $tags]];
    }


    final protected function gauge(string $key, float $value, array $tags = [])
    {
        $this->collectedMetrics[] = ['gauge' => [$key, $value, $tags]];
    }


    final protected function gaugeServiceLayer(string $key, float $value, array $tags = [])
    {
        $this->collectedMetrics[] = ['gaugeServiceLayer' => [$key, $value, $tags]];
    }


    final protected function gaugeInstanceLayer(string $key, float $value, array $tags = [])
    {
        $this->collectedMetrics[] = ['gaugeInstanceLayer' => [$key, $value, $tags]];
    }


    final protected function summary(string $key, float $value, array $tags = [])
    {
        $this->collectedMetrics[] = ['summary' => [$key, $value, $tags]];
    }


    final protected function histogram( string $key, float $value, string $bucketSetup, array $tags = [])
    {
        $this->collectedMetrics[] = ['histogram' => [$key, $value, $bucketSetup, $tags]];
    }


    public function startTiming(?float $timeSeconds = null): void
    {
        $this->startTime = is_null($timeSeconds) ? microtime(true) : $timeSeconds;
        $this->time = null;
    }


    public function endTiming(): void
    {
        if (is_null($this->startTime)) {
            $this->time = null;
        } else {
            $this->time = microtime(true) - $this->startTime;
        }
    }


    public function getTiming(): ?float
    {
        return $this->time;
    }


    public function addTiming(float $seconds): void
    {
        $this->time = ($this->time ?? 0) + $seconds;
    }
}
