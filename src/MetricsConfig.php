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


    public function isEnabled(): bool
    {
        return (bool)$this->config->getValue('metrics.enabled', false);
    }


    public function getAppName(): string
    {
        $appName = $this->config->getValue('project.name') ?? $this->config->getValue('name') ?? 'unknown';
        return (string)$appName;
    }
}
