<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

class ExporterParams
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

    public function __construct(
        string $host,
        int $port,
        ?string $namespace,
        float $timeoutInSec,
        bool $isEnabled
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->namespace = $namespace;
        $this->timeoutInSec = $timeoutInSec;
        $this->isEnabled = $isEnabled;
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

    public function isEnabled(): ?bool
    {
        return is_null($this->isEnabled) ? null : (bool)$this->isEnabled;
    }
}
