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


    public function getSessionParameters(string $sessionName): ?SessionParams
    {
        if (is_null($this->config->getValue('statsd.sessions.' . $sessionName))) {
            return null;
        }

        $host = $this->config->getValue('statsd.sessions.' . $sessionName . '.host', '', true);
        $port = (int)$this->config->getValue('statsd.sessions.' . $sessionName . '.port', 8125);
        $ns = $this->config->getValue('statsd.sessions.' . $sessionName . '.namespace', null);
        if (!is_null($ns)) {
            $ns = (string)$ns;
        }
        $timeout = (float)$this->config->getValue('statsd.sessions.' . $sessionName . '.timeout', 0);
        $enabled = $this->config->getValue('statsd.sessions.' . $sessionName . '.enabled', null);
        if (!is_null($enabled)) {
            $enabled = (bool)$enabled;
        }
        $isExporter = (bool)$this->config->getValue('statsd.sessions.' . $sessionName . '.is_statsd_exporter', 0);

        return new SessionParams($host, $port, $ns, $timeout, $enabled, $isExporter);
    }


    public function isEnabled(): bool
    {
        return self::isGloballyEnabled() || (bool)$this->config->getValue('statsd.enabled', false);
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


    public function getServerHostname(): string
    {
        return $this->config->getServerHostname();
    }
}
