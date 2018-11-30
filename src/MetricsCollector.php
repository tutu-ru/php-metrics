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
    protected $statsdExporterTimersMetricName;

    /**
     * Теги для prometheus statsd_exporter
     *
     * @var array
     */
    protected $statsdExporterTimersTags = [];


    abstract protected function saveCustomMetrics();

    abstract protected function getTimingKey();


    /**
     * @param float|null $timeSeconds
     */
    public function startTiming($timeSeconds = null)
    {
        $this->startTime = is_null($timeSeconds) ? microtime(true) : $timeSeconds;
    }

    public function endTiming()
    {
        $this->time = $this->getCurrentTiming();
    }

    public function addTiming($valueInSecs)
    {
        $this->time = ($this->time ?? 0) + $valueInSecs;
    }

    public function getTiming()
    {
        return $this->time;
    }

    protected function setStatsdExporterTimersMetricName(string $metricName): void
    {
        $this->statsdExporterTimersMetricName = MetricNameUtils::prepareMetricNameForStatsdExporter($metricName);
    }

    /**
     * Возвращает текущую отсечку с начала отсчета времени
     *
     * @return float
     */
    public function getCurrentTiming()
    {
        if (!is_null($this->startTime)) {
            return microtime(true) - $this->startTime;
        }

        return null;
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

    /**
     * Склеивает несколько частей имени метрики в валидное имя
     * @param array $namespaces
     *
     * @return string
     */
    protected function glueNamespaces(array $namespaces)
    {
        return MetricNameUtils::prepareMetricName(implode('.', str_replace('.', '_', $namespaces)));
    }

    /**
     * Возвращает сессию StatsD для отправки метрик
     * На этапе выверки метрик нужно переопределить метод для возвращения garbage-сессии
     *
     * @return MetricsSessionInterface
     * @throws Exceptions\MetricsException
     */
    protected function getSession(): MetricsSessionInterface
    {
        return $this->getMetrics()->getRequestedSessionOrDefault(SessionNames::NAME_WORK);
    }

    /**
     * @return MetricsSessionInterface[]
     * @throws Exceptions\MetricsException
     */
    protected function getSessions(): array
    {
        return [$this->getSession()];
    }

    /**
     * @throws Exceptions\MetricsException
     */
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

    /**
     * @return MetricsSessionInterface
     * @throws Exceptions\MetricsException
     */
    private function getStatsdExporterSession(): MetricsSessionInterface
    {
        return $this->getMetrics()->getRequestedSessionOrDefault(SessionNames::NAME_STATSD_EXPORTER);
    }
}
