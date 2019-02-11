<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetricExporter;

use Domnikl\Statsd\Connection;
use Domnikl\Statsd\Connection\InMemory;
use TutuRu\Metrics\StatsdExporterClient;

class MemoryMetricExporter extends StatsdExporterClient
{
    /** @var InMemory */
    private $lastCreatedConnection;


    public function __construct(string $appName)
    {
        parent::__construct($appName, '', 0, 0);
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
