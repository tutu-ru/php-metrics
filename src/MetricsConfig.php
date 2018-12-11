<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Config\ConfigContainer;

class MetricsConfig
{
    private static $enabledInRuntime = false;

    public static function enable()
    {
        self::$enabledInRuntime = true;
    }


    public static function disable()
    {
        self::$enabledInRuntime = false;
    }

    public static function isGloballyEnabled(): bool
    {
        return self::$enabledInRuntime;
    }


    /** @var ConfigContainer */
    private $config;


    public function __construct(ConfigContainer $config)
    {
        $this->config = $config;
    }


    public function getExporterParameters(): ?ExporterParams
    {
        $host = $this->config->getValue('metrics.statsd_exporter.host', null, true);
        $port = $this->config->getValue('metrics.statsd_exporter.port', null, true);
        $enabled = (bool)$this->config->getValue('metrics.statsd_exporter.enabled', false);
        $ns = $this->config->getValue('metrics.statsd_exporter.namespace', null);
        if (!is_null($ns)) {
            $ns = (string)$ns;
        }
        $timeout = (float)$this->config->getValue('metrics.statsd_exporter.timeout', 0);
        return new ExporterParams($host, $port, $ns, $timeout, $enabled);
    }


    public function replaceDotInHostname(): bool
    {
        return (bool)$this->config->getValue('statsd.replace_dot_in_hostname', true);
    }


    public function prependHostnameFromApp(): bool
    {
        // пока предполагается, что для проектов будет false, для сервисов - true
        return (bool)$this->config->getApplicationValue('statsd.prepend_hostname', false);
    }


    public function getAppName(): string
    {
        return $this->config->getValue('project.name') ?? $this->config->getValue('name') ?? 'unknown';
    }
}
