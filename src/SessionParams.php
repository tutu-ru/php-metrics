<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

class SessionParams
{
    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $namespace;

    /** @var float */
    private $timeoutInSec;

    /** @var bool */
    private $isEnabled;

    /** @var bool */
    private $isExporter;

    public function __construct(
        string $host,
        int $port,
        ?string $namespace,
        float $timeoutInSec,
        ?bool $isSessionEnabled,
        bool $isStatsdExporter
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->namespace = $namespace;
        $this->timeoutInSec = $timeoutInSec;
        $this->isEnabled = $isSessionEnabled;
        $this->isExporter = $isStatsdExporter;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function getTimeoutInSec(): float
    {
        return $this->timeoutInSec;
    }

    public function isSessionEnabled(): ?bool
    {
        return is_null($this->isEnabled) ? null : (bool)$this->isEnabled;
    }

    public function isExporter(): bool
    {
        return $this->isExporter;
    }
}
