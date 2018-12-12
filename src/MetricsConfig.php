<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Config\ConfigContainer;

class MetricsConfig
{
    /** @var ConfigContainer */
    private $config;


    public function __construct(ConfigContainer $config)
    {
        $this->config = $config;
    }


    public function getExporterParameters(): ExporterParams
    {
        $host = $this->config->getValue('metrics.statsd_exporter.host', null, true);
        $port = $this->config->getValue('metrics.statsd_exporter.port', null, true);
        $timeout = $this->config->getValue('metrics.statsd_exporter.timeout', 0);
        return new ExporterParams((string)$host, (int)$port, (float)$timeout);
    }


    public function isEnabled(): bool
    {
        return (bool)$this->config->getValue('metrics.statsd_exporter.enabled', false);
    }


    public function getAppName(): string
    {
        return (string)$this->config->getValue('project.name')
            ?? (string)$this->config->getValue('name')
            ?? 'unknown';
    }
}
