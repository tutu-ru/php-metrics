<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

interface StatsdExporterClientInterface
{
    public function count(string $key, float $value, array $tags = []): StatsdExporterClientInterface;

    public function increment(string $key, array $tags = []): StatsdExporterClientInterface;

    public function decrement(string $key, array $tags = []): StatsdExporterClientInterface;

    public function timing(string $key, float $seconds, array $tags = []): StatsdExporterClientInterface;

    public function gauge(string $key, float $value, array $tags = []): StatsdExporterClientInterface;

    public function summary(string $key, float $value, array $tags = []): StatsdExporterClientInterface;

    public function save(): void;
}
