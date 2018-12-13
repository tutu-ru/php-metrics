<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetricsExporter;

use Domnikl\Statsd\Connection;
use Domnikl\Statsd\Connection\InMemory;
use TutuRu\Config\ConfigContainer;
use TutuRu\Metrics\MetricsConfig;
use TutuRu\Metrics\UdpMetricsExporter;
use TutuRu\Metrics\UdpMetricsExporterParams;

class MemoryMetricsExporter extends UdpMetricsExporter
{
    /** @var InMemory */
    private $lastCreatedConnection;


    public function __construct(ConfigContainer $config)
    {
        $metricsConfig = new MetricsConfig($config);
        $fakeUdpMetricsExporterParams = new UdpMetricsExporterParams('', 0, 0);
        parent::__construct($metricsConfig->getAppName(), $fakeUdpMetricsExporterParams);
    }


    public function getRawExportedMetrics(): array
    {
        return $this->lastCreatedConnection->getMessages();
    }


    public function getLastCreatedConnection(): ?InMemory
    {
        return $this->lastCreatedConnection;
    }

    protected function createStatsdConnection(): Connection
    {
        $this->lastCreatedConnection = new InMemory();
        return $this->lastCreatedConnection;
    }
}
