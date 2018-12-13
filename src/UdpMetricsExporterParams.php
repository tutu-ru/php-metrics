<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

class UdpMetricsExporterParams
{
    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var float */
    private $timeoutInSec;


    public function __construct(string $host, int $port, float $timeoutInSec)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeoutInSec = $timeoutInSec;
    }


    public function getHost(): string
    {
        return $this->host;
    }


    public function getPort(): int
    {
        return $this->port;
    }


    public function getTimeoutInSec(): float
    {
        return $this->timeoutInSec;
    }
}
