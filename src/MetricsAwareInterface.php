<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

interface MetricsAwareInterface
{
    public function setMetricsExporter(MetricsExporterInterface $metricsExporter);
}
