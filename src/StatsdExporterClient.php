<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use Domnikl\Statsd\Client;
use Domnikl\Statsd\Connection;
use Domnikl\Statsd\Connection\UdpSocket;

class StatsdExporterClient implements StatsdExporterClientInterface
{
    /** @var string */
    private $appName;

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var float */
    private $timeoutSec;

    /** @var Client */
    private $statsdClient;


    public function __construct(string $appName, string $host, int $port, float $timeoutSec)
    {
        $this->appName = $appName;
        $this->host = $host;
        $this->port = $port;
        $this->timeoutSec = $timeoutSec;
    }


    public function count(string $key, float $value, array $tags = []): StatsdExporterClientInterface
    {
        $this->statsdClient()->count($this->prepareKey($key), $value, $sampleRate = 1, $this->prepareTags($tags));
        return $this;
    }


    public function increment(string $key, array $tags = []): StatsdExporterClientInterface
    {
        $this->statsdClient()->increment($this->prepareKey($key), $sampleRate = 1, $this->prepareTags($tags));
        return $this;
    }


    public function decrement(string $key, array $tags = []): StatsdExporterClientInterface
    {
        $this->statsdClient()->decrement($this->prepareKey($key), $sampleRate = 1, $this->prepareTags($tags));
        return $this;
    }


    public function timing(string $key, float $seconds, array $tags = []): StatsdExporterClientInterface
    {
        $ms = (float)($seconds * 1000);
        $this->statsdClient()->timing($this->prepareKey($key), $ms, $sampleRate = 1, $this->prepareTags($tags));
        return $this;
    }


    public function gauge(string $key, float $value, array $tags = []): StatsdExporterClientInterface
    {
        $this->statsdClient()->gauge($this->prepareKey($key), $value, $this->prepareTags($tags));
        return $this;
    }


    public function gaugeServiceLayer(string $key, float $value, array $tags = []): StatsdExporterClientInterface
    {
        $counterKey = $key . '_service_layer_gauge_count';
        $this->increment($counterKey, $tags);
        return $this->gauge($key, $value, $tags);
    }


    public function gaugeInstanceLayer(string $key, float $value, array $tags = []): StatsdExporterClientInterface
    {
        return $this->gauge($key, $value, $tags);
    }


    public function summary(string $key, float $value, array $tags = []): StatsdExporterClientInterface
    {
        return $this->timing($key, $value, $tags);
    }

    /**
     * Метод для сбора метрики типа - Гистограма
     * Для корректной работы необходимо в конфиге statsd_exporter завести правило,
     * по которому будет определеяться тип метрики и набор buckets в которые будут "раскладываться" значения.
     *
     * statsd_exporter на совей стороне будет отрезать суффикс '_hg_' . $bucketSetup,
     * и в Prometheus будут метрики с чистыми именами
     * 
     * По умолчанию заведены следующие $bucketSetup:
     * 'ms' - 0.1мс, 0.5мс, 1мс, 3мс, 5мс, 10мс, 20мс, 30мс, 50мс, 70мс, 100мс, 150мс, 200мс, 300мс, 500мс, 1с, +Inf
     * 's' - 10мс, 50мс, 100мс, 300мс, 500мс, 1с, 2с, 3с, 5с, 7с, 10с, 15с, 20с, 30с, 50с, +Inf
     */
    public function histogram(
        string $key,
        float $value,
        string $bucketSetup,
        array $tags = []
    ): StatsdExporterClientInterface
    {
        return $this->timing($key . '_hg_' . $bucketSetup, $value, $tags);
    }


    public function save(): void
    {
        $this->statsdClient()->endBatch();
        $this->resetClient();
    }


    protected function createStatsdConnection(): Connection
    {
        return new UdpSocket($this->host, $this->port, $this->timeoutSec, true);
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
        $preparedTags = ['app' => $this->appName];
        foreach ($tags as $k => $v) {
            $preparedTags[$this->prepareKey($k)] = $v;
        }
        return $preparedTags;
    }
}
