<?php
declare(strict_types=1);

namespace TutuRu\Metrics\MetricsExporter;

use Domnikl\Statsd\Client;
use Domnikl\Statsd\Connection;
use Domnikl\Statsd\Connection\UdpSocket;
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

    /** @var Client */
    private $statsdClient;

    /** @var bool|null */
    private $isEnabled;


    public function __construct(MetricsConfig $config, ExporterParams $params)
    {
        $this->params = $params;
        $this->config = $config;
    }


    public function count(string $key, int $value, array $tags = []): MetricsExporterInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->count($key, $value, $sampleRate = 1, $this->prepareTags($tags));
        }
        return $this;
    }


    public function increment(string $key, array $tags = []): MetricsExporterInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->increment($key, $sampleRate = 1, $this->prepareTags($tags));
        }
        return $this;
    }


    public function decrement(string $key, array $tags = []): MetricsExporterInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->decrement($key, $sampleRate = 1, $this->prepareTags($tags));
        }
        return $this;
    }


    public function timing(string $key, float $seconds, array $tags = []): MetricsExporterInterface
    {
        return $this->measureAsTiming($key, (int)($seconds * 1000), $this->prepareTags($tags));
    }


    public function measureAsTiming(string $key, int $ms, array $tags = []): MetricsExporterInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->timing($key, $ms, $sampleRate = 1, $this->prepareTags($tags));
        }
        return $this;
    }


    public function gauge(string $key, int $value, array $tags = []): MetricsExporterInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->gauge($key, $value, $this->prepareTags($tags));
        }
        return $this;
    }


    public function saveCollector(MetricsCollector $collector): MetricsExporterInterface
    {
        // use prepareMetricsName
        // TODO: Implement collect() method.
    }


    public function export(): void
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


    private function prepareMetricName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_]+/', '_', $name);
    }


    private function prepareTags(array $tags): array
    {
        return array_merge($tags, ['app' => $this->config->getAppName()]);
    }


    private function isEnabled(): bool
    {
        return $this->params->isEnabled();
    }
}
