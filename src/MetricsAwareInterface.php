<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Metrics\Exceptions\MetricsException;

interface MetricsAwareInterface
{
    public function setMetricsExporter(MetricsExporterInterface $metricsExporter);

    /**
     * @return MetricsExporterInterface
     * @throws MetricsException
     */
    public function getMetricsExporter(): MetricsExporterInterface;
}
