<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TutuRu\Metrics\MetricsSession\MetricsSessionInterface;

abstract class MetricsCollector implements LoggerAwareInterface, MetricsAwareInterface
{
    use LoggerAwareTrait;
    use MetricsAwareTrait;

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

    /**
     * Короткое имя метрики для prometheus statsd_exporter
     * @var string
     */
    private $statsdExporterTimersMetricName;

    /**
     * Теги для prometheus statsd_exporter
     * @var string[]
     */
    private $statsdExporterTimersTags = [];


    abstract protected function saveCustomMetrics(): void;

    abstract protected function getTimingKey(): string;


    public function startTiming(?float $timeSeconds = null): void
    {
        $this->startTime = is_null($timeSeconds) ? microtime(true) : $timeSeconds;
    }


    public function endTiming(): void
    {
        if (is_null($this->startTime)) {
            if (!is_null($this->logger)) {
                $this->logger->error("unexpected endTiming call: no startTiming");
            }
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


    public function save(): bool
    {
        $result = true;
        try {
            if (!is_null($this->getTiming())) {
                $this->sendTimersToStatsdSessions();
                $this->sendTimersToStatsdExporterSession();
            }
            $this->saveCustomMetrics();
        } catch (\Throwable $e) {
            $result = false;
            if (!is_null($this->logger)) {
                $this->logger->error("{$e->getMessage()}");
            }
        }
        return $result;
    }


    protected function glueNamespaces(array $namespaces): string
    {
        return MetricNameUtils::prepareMetricName(implode('.', str_replace('.', '_', $namespaces)));
    }


    protected function setStatsdExporterTimersMetricName(string $metricName): void
    {
        $this->statsdExporterTimersMetricName = MetricNameUtils::prepareMetricNameForStatsdExporter($metricName);
    }


    protected function setStatsdExporterTimersTags(array $tags): void
    {
        $this->statsdExporterTimersTags = $tags;
    }


    protected function addStatsdExporterTimersTags(array $tags): void
    {
        $this->statsdExporterTimersTags = array_merge($this->statsdExporterTimersTags, $tags);
    }


    protected function getSession(): MetricsSessionInterface
    {
        return $this->getMetricsSessionRegistry()->getRequestedSessionOrDefault(SessionNames::NAME_WORK);
    }


    /**
     * @return MetricsSessionInterface[]
     */
    protected function getSessions(): array
    {
        return [$this->getSession()];
    }


    private function sendTimersToStatsdSessions(): void
    {
        foreach ($this->getSessions() as $session) {
            try {
                $session->timing($this->getTimingKey(), $this->getTiming());
            } catch (\Throwable $e) {
                if (!is_null($this->logger)) {
                    $this->logger->error("statsd send error: {$e->getMessage()}", ['lib' => 'metrics']);
                }
            }
        }
    }


    private function sendTimersToStatsdExporterSession(): void
    {
        try {
            if (!empty($this->statsdExporterTimersMetricName)) {
                $this->getStatsdExporterSession()->timing(
                    $this->statsdExporterTimersMetricName,
                    $this->getTiming(),
                    $this->statsdExporterTimersTags
                );
            //} else {
                // Не включаем логирование на время переходного периода
                //$this->_getLogger()->error(
                //    "Empty prometheus metric name for metric {$this->_getTimingKey()}",
                //    ['lib' => 'metrics']
                //);
            }
        } catch (\Throwable $e) {
            if (!is_null($this->logger)) {
                $this->logger->error("statsd_exporter send error: {$e->getMessage()}", ['lib' => 'metrics']);
            }
        }
    }


    private function getStatsdExporterSession(): MetricsSessionInterface
    {
        return $this->getMetricsSessionRegistry()->getRequestedSessionOrNull(SessionNames::NAME_STATSD_EXPORTER);
    }
}
