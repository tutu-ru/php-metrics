<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetrics;

use Domnikl\Statsd\Connection;
use Domnikl\Statsd\Connection\InMemory;
use TutuRu\Metrics\MetricsExporter\UdpMetricsExporter;
use TutuRu\Metrics\ExporterParams;

class MemoryMetricsSession extends UdpMetricsExporter
{
    /** @var InMemory */
    private $lastCreatedConnection;

    protected function createStatsdConnection(): Connection
    {
        $this->lastCreatedConnection = new InMemory();
        return $this->lastCreatedConnection;
    }

    public function getLastCreatedConnection(): ?InMemory
    {
        return $this->lastCreatedConnection;
    }

    // увеличение видимости для тестов
    public function getParams(): ExporterParams
    {
        return parent::getParams();
    }
}
