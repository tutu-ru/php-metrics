<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

trait MetricsAwareTrait
{
    /** @var MetricsExporterInterface|null */
    protected $metricsExporter;


    public function setMetricsExporter(MetricsExporterInterface $metricsExporter)
    {
        $this->metricsExporter = $metricsExporter;
    }
}
