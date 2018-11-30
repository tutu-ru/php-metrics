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

    /** @var int */
    private $timeoutInSec;

    /** @var bool */
    private $isEnabled;

    /** @var bool */
    private $isExporter;

    /**
     * SessionParams constructor.
     * @param      $host
     * @param      $port
     * @param      $namespace
     * @param      $timeoutInSec
     * @param      $isSessionEnabled
     * @param bool $isStatsdExporter
     */
    public function __construct($host, $port, $namespace, $timeoutInSec, $isSessionEnabled, bool $isStatsdExporter)
    {
        $this->host = $host;
        $this->port = $port;
        $this->namespace = $namespace;
        $this->timeoutInSec = $timeoutInSec;
        $this->isEnabled = $isSessionEnabled;
        $this->isExporter = $isStatsdExporter;
    }


    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }


    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }


    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }


    /**
     * @return int
     */
    public function getTimeoutInSec()
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
