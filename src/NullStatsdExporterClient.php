<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

class NullStatsdExporterClient implements StatsdExporterClientInterface
{
    public function count(string $key, float $value, array $tags = []): StatsdExporterClientInterface
    {
        return $this;
    }

    public function increment(string $key, array $tags = []): StatsdExporterClientInterface
    {
        return $this;
    }

    public function decrement(string $key, array $tags = []): StatsdExporterClientInterface
    {
        return $this;
    }

    public function timing(string $key, float $seconds, array $tags = []): StatsdExporterClientInterface
    {
        return $this;
    }

    public function gauge(string $key, float $value, array $tags = []): StatsdExporterClientInterface
    {
        return $this;
    }

    public function gaugeInstanceLayer(string $key, float $value, array $tags = []): StatsdExporterClientInterface
    {
        return $this;
    }

    public function gaugeServiceLayer(string $key, float $value, array $tags = []): StatsdExporterClientInterface
    {
        return $this;
    }

    public function summary(string $key, float $value, array $tags = []): StatsdExporterClientInterface
    {
        return $this;
    }

    public function histogram(
        string $key,
        float $value,
        string $bucketSetup,
        array $tags = []
    ): StatsdExporterClientInterface
    {
        return $this;
    }

    public function save(): void
    {
    }
}
