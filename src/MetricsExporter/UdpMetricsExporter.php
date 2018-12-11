<?php
declare(strict_types=1);

namespace TutuRu\Metrics\MetricsExporter;

use Domnikl\Statsd\Client;
use Domnikl\Statsd\Connection;
use Domnikl\Statsd\Connection\UdpSocket;
use TutuRu\Metrics\MetricBuilder;
use TutuRu\Metrics\MetricsCollector;
use TutuRu\Metrics\MetricsConfig;
use TutuRu\Metrics\ExporterParams;
use TutuRu\Metrics\MetricsExporterInterface;

class UdpMetricsExporter implements MetricsExporterInterface
{
    /** @var ExporterParams */
    private $params;

    /** @var MetricsConfig */
    private $config;

    /** @var MetricBuilder */
    private $metricBuilder;

    /** @var Client */
    private $statsdClient;

    /** @var bool|null */
    private $isEnabled;


    public function __construct(MetricsConfig $config, ExporterParams $params)
    {
        $this->params = $params;
        $this->config = $config;
        $this->metricBuilder = new MetricBuilder($this->config);
        $this->isEnabled = $this->params->isSessionEnabled();
    }


    public function count(string $key, int $value, array $tags = []): MetricsSessionInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->count(
                $this->prepareKey($key, MetricBuilder::METRIC_TYPE_COUNTERS, $this->params->isExporter()),
                $value,
                $sampleRate = 1,
                $this->prepareTags($tags)
            );
        }

        return $this;
    }


    public function increment(string $key, array $tags = []): MetricsSessionInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->increment(
                $this->prepareKey($key, MetricBuilder::METRIC_TYPE_COUNTERS, $this->params->isExporter()),
                $sampleRate = 1,
                $this->prepareTags($tags)
            );
        }

        return $this;
    }


    public function decrement(string $key, array $tags = []): MetricsSessionInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->decrement(
                $this->prepareKey($key, MetricBuilder::METRIC_TYPE_COUNTERS, $this->params->isExporter()),
                $sampleRate = 1,
                $this->prepareTags($tags)
            );
        }

        return $this;
    }


    public function timing(string $key, float $seconds, array $tags = []): MetricsSessionInterface
    {
        return $this->measureAsTiming($key, (int)($seconds * 1000), $this->prepareTags($tags));
    }


    public function measureAsTiming(string $key, int $ms, array $tags = []): MetricsSessionInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->timing(
                $this->prepareKey($key, MetricBuilder::METRIC_TYPE_TIMERS, $this->params->isExporter()),
                $ms,
                $sampleRate = 1,
                $this->prepareTags($tags)
            );
        }

        return $this;
    }


    public function gauge(string $key, int $value, array $tags = []): MetricsSessionInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->gauge(
                $this->prepareKey($key, MetricBuilder::METRIC_TYPE_GAUGES, $this->params->isExporter()),
                $value,
                $this->prepareTags($tags)
            );
        }

        return $this;
    }


    public function send(): void
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->endBatch();
            $this->resetClient();
        }
    }


    protected function createStatsdConnection(): Connection
    {
        return new UdpSocket(
            $this->params->getHost(),
            $this->params->getPort(),
            $this->params->getTimeoutInSec(),
            true
        );
    }


    protected function getParams(): ExporterParams
    {
        return $this->params;
    }


    private function statsdClient(): Client
    {
        if (is_null($this->statsdClient)) {
            $connection = $this->createStatsdConnection();
            $this->statsdClient = new Client($connection);
            if (!is_null($this->params->getNamespace())) {
                $this->statsdClient->setNamespace($this->params->getNamespace());
            }
            $this->statsdClient->startBatch();
        }

        return $this->statsdClient;
    }


    private function resetClient(): void
    {
        $this->statsdClient = null;
    }


    private function prepareKey(string $key, ?string $metricsType = null, bool $isExporter = false): string
    {
        return $this->metricBuilder->prepareKey($key, $metricsType, $isExporter);
    }


    private function prepareTags(array $tags): array
    {
        if (!$this->params->isExporter()) {
            // Отправка тегов поддержана только для statsd_exporter
            return [];
        }

        return $this->metricBuilder->prepareTags($tags);
    }


    private function isEnabled(): bool
    {
        if (is_null($this->isEnabled)) {
            // Если флаг включения не указан - ориентируемся на общий. Иначе сессия - приоритетней
            return $this->config->isEnabled();
        }
        return $this->isEnabled;
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
