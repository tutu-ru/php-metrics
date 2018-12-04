<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Metrics\Exceptions\UnknownSessionException;
use TutuRu\Metrics\MetricsSession\MetricsSessionInterface;

interface SessionRegistryInterface
{
    /**
     * @param string $name
     * @return MetricsSessionInterface
     * @throws UnknownSessionException
     */
    public function getSession(string $name): MetricsSessionInterface;

    public function getNullSession(): MetricsSessionInterface;

    public function getRequestedSessionOrDefault(string $name): MetricsSessionInterface;

    public function getRequestedSessionOrNull(string $name): MetricsSessionInterface;

    /**
     * @return MetricsSessionInterface[]
     */
    public function getSessions(): array;

    public function flushSessions(): void;
}