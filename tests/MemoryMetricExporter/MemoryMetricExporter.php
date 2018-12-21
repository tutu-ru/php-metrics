<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetricExporter;

use Domnikl\Statsd\Connection;
use Domnikl\Statsd\Connection\InMemory;
use TutuRu\Config\ConfigContainer;
use TutuRu\Metrics\MetricConfig;
use TutuRu\Metrics\StatsdExporterClient;
use TutuRu\Metrics\StatsdExporterClientParams;

class MemoryMetricExporter extends StatsdExporterClient
{
    /** @var InMemory */
    private $lastCreatedConnection;


    public function __construct(ConfigContainer $config)
    {
        $metricsConfig = new MetricConfig($config);
        $fakeUdpMetricsExporterParams = new StatsdExporterClientParams('', 0, 0);
        parent::__construct($metricsConfig->getAppName(), $fakeUdpMetricsExporterParams);
    }


    public function getRawExportedMetrics(): array
    {
        return $this->lastCreatedConnection->getMessages();
    }


    /**
     * @param string|null $name
     * @return MemoryMetric[]
     */
    public function getExportedMetrics(?string $name = null): array
    {
        $metrics = [];
        foreach ($this->getRawExportedMetrics() as $rawExportedMetric) {
            $metric = MemoryMetric::createFromRawString($rawExportedMetric);
            if (is_null($name) || $name === $metric->getName()) {
                $metrics[] = $metric;
            }
        }
        return $metrics;
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
