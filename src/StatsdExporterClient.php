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


    public function count(string $key, int $value, array $tags = []): StatsdExporterClientInterface
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
        $ms = (int)($seconds * 1000);
        $this->statsdClient()->timing($this->prepareKey($key), $ms, $sampleRate = 1, $this->prepareTags($tags));
        return $this;
    }


    public function gauge(string $key, int $value, array $tags = []): StatsdExporterClientInterface
    {
        $this->statsdClient()->gauge($this->prepareKey($key), $value, $this->prepareTags($tags));
        return $this;
    }


    public function summary(string $key, int $value, array $tags = []): StatsdExporterClientInterface
    {
        $this->statsdClient()->timing($this->prepareKey($key), $value, $sampleRate = 1, $this->prepareTags($tags));
        return $this;
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
