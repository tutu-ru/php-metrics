<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Config\Config;

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


    /** @var Config */
    private $config;


    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $sessionName
     * @return SessionParams|null
     */
    public function getSessionParameters(string $sessionName): ?SessionParams
    {
        if (is_null($this->config->getValue('statsd.sessions.' . $sessionName))) {
            return null;
        }

        return new SessionParams(
            $this->config->getValue('statsd.sessions.' . $sessionName . '.host', '', true),
            (int)$this->config->getValue('statsd.sessions.' . $sessionName . '.port', 8125),
            $this->config->getValue('statsd.sessions.' . $sessionName . '.namespace', null),
            (float)$this->config->getValue('statsd.sessions.' . $sessionName . '.timeout', 0),
            $this->config->getValue('statsd.sessions.' . $sessionName . '.enabled', null),
            (bool)$this->config->getValue('statsd.sessions.' . $sessionName . '.is_statsd_exporter', 0)
        );
    }

    public function isEnabled(): bool
    {
        return self::isGloballyEnabled() || (bool)$this->config->getValue('statsd.enabled', false);
    }


    public function replaceDotInHostname(): bool
    {
        return (bool)$this->config->getValue('statsd.replace_dot_in_hostname', true);
    }

    /*
     * пока предполагается, что для проектов будет false, для сервисов - true
     */
    public function prependHostnameFromApp(): bool
    {
        return (bool)$this->config->getApplicationValue('statsd.prepend_hostname', false);
    }

    public function getAppName(): string
    {
        return (string)($this->config->getValue('project.name') ?? $this->config->getValue('name') ?? 'unknown');
    }

    public function getServerHostname(): string
    {
        return $this->config->getServerHostname();
    }
}
