<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use Domnikl\Statsd\Client;
use Domnikl\Statsd\Connection;
use Domnikl\Statsd\Connection\UdpSocket;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class UdpMetricsExporter implements MetricsExporterInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var UdpMetricsExporterParams */
    private $params;

    /** @var MetricsConfig */
    private $config;

    /** @var Client */
    private $statsdClient;


    public function __construct(MetricsConfig $config, UdpMetricsExporterParams $params)
    {
        $this->params = $params;
        $this->config = $config;
    }


    public function count(string $key, int $value, array $tags = []): MetricsExporterInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->count($this->prepareKey($key), $value, $sampleRate = 1, $this->prepareTags($tags));
        }
        return $this;
    }


    public function increment(string $key, array $tags = []): MetricsExporterInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->increment($this->prepareKey($key), $sampleRate = 1, $this->prepareTags($tags));
        }
        return $this;
    }


    public function decrement(string $key, array $tags = []): MetricsExporterInterface
    {
        if ($this->isEnabled()) {
            $this->statsdClient()->decrement($this->prepareKey($key), $sampleRate = 1, $this->prepareTags($tags));
        }
        return $this;
    }


    public function timing(string $key, float $seconds, array $tags = []): MetricsExporterInterface
    {
        return $this->measureAsTiming($this->prepareKey($key), (int)($seconds * 1000), $this->prepareTags($tags));
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
            $this->statsdClient()->gauge($this->prepareKey($key), $value, $this->prepareTags($tags));
        }
        return $this;
    }


    public function saveCollector(MetricsCollector $collector): MetricsExporterInterface
    {
        try {
            $collector->save();
            foreach ($collector->getMetrics() as $metric) {
                $action = key($metric);
                $params = current($metric);
                call_user_func_array([$this, $action], $params);
            }
        } catch (\Throwable $e) {
            if (!is_null($this->logger)) {
                $this->logger->error("Can't save collector " . get_class($collector) . ": " . $e->__toString());
            }
        }
        return $this;
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


    private function statsdClient(): Client
    {
        if (is_null($this->statsdClient)) {
            $connection = $this->createStatsdConnection();
            $this->statsdClient = new Client($connection);
            $this->statsdClient->startBatch();
        }
        return $this->statsdClient;
    }


    private function resetClient(): void
    {
        $this->statsdClient = null;
    }


    private function prepareKey(string $key): string
    {
        return preg_replace('/[^a-zA-Z0-9_]+/', '_', $key);
    }


    private function prepareTags(array $tags): array
    {
        return array_merge($tags, ['app' => $this->config->getAppName()]);
    }


    private function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }
}
