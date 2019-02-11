<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Config\ConfigInterface;

class MetricConfig
{
    /** @var ConfigInterface */
    private $config;


    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }


    public function isEnabled(): bool
    {
        return (bool)$this->config->getValue('metrics.enabled', false, false);
    }


    public function getAppName(): string
    {
        $appName = $this->config->getValue('project.name') ?? $this->config->getValue('name') ?? 'unknown';
        return (string)$appName;
    }


    public function getStatsdExporterHost(): string
    {
        return (string)$this->config->getValue('metrics.statsd_exporter.host', true);
    }


    public function getStatsdExporterPort(): int
    {
        return (int)$this->config->getValue('metrics.statsd_exporter.port', true);
    }


    public function getStatsdExporterTimeoutSec(): float
    {
        return (float)($this->config->getValue('metrics.statsd_exporter.timeout') ?? 0);
    }
}
