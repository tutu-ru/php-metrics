<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetrics;

use Domnikl\Statsd\Connection;
use Domnikl\Statsd\Connection\InMemory;
use TutuRu\Metrics\MetricsSession\UdpMetricsSession;
use TutuRu\Metrics\SessionParams;

class MemoryMetricsSession extends UdpMetricsSession
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
    public function getParams(): SessionParams
    {
        return parent::getParams();
    }
}
