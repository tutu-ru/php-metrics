<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class MetricsCollector implements LoggerAwareInterface
{
    use LoggerAwareTrait;


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


    abstract protected function getTimersMetricName(): string;


    abstract protected function getTimersMetricTags(): array;


    final public function getMetrics(): array
    {
        return $this->collectedMetrics;
    }


    final public function save()
    {
        if (!is_null($this->time)) {
            $this->timing($this->getTimersMetricName(), $this->time, $this->getTimersMetricTags());
            $this->onSave();
        }
    }


    protected function onSave(): void
    {
    }


    final protected function count(string $key, int $value, array $tags = [])
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


    final protected function gauge(string $key, int $value, array $tags = [])
    {
        $this->collectedMetrics[] = ['gauge' => [$key, $value, $tags]];
    }


    public function startTiming(?float $timeSeconds = null): void
    {
        $this->startTime = is_null($timeSeconds) ? microtime(true) : $timeSeconds;
        $this->time = null;
    }


    public function export(MetricsExporterInterface $exporter)
    {
        try {
            $this->save();
            foreach ($this->getMetrics() as $metric) {
                $action = key($metric);
                $params = current($metric);
                call_user_func_array([$exporter, $action], $params);
            }
        } catch (\Throwable $e) {
            if (!is_null($this->logger)) {
                $this->logger->error("Can't save collector " . get_class($this) . ": {$e}");
            }
        }
        return $this;
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
}
