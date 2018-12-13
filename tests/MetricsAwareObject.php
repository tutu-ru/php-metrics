<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\MetricsAwareInterface;
use TutuRu\Metrics\MetricsAwareTrait;
use TutuRu\Metrics\MetricsExporterInterface;

class MetricsAwareObject implements MetricsAwareInterface
{
    use MetricsAwareTrait;

    public function getMetricsExporter(): ?MetricsExporterInterface
    {
        return $this->metricsExporter;
    }
}
