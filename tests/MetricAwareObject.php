<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\MetricAwareInterface;
use TutuRu\Metrics\MetricAwareTrait;
use TutuRu\Metrics\StatsdExporterClientInterface;

class MetricAwareObject implements MetricAwareInterface
{
    use MetricAwareTrait;

    public function getStatsdExporterClient(): ?StatsdExporterClientInterface
    {
        return $this->statsdExporterClient;
    }
}
