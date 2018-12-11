<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Metrics\Exceptions\MetricsException;

trait MetricsAwareTrait
{
    /** @var MetricsExporterInterface */
    private $metricsExporter;


    public function setMetricsExporter(MetricsExporterInterface $metricsExporter)
    {
        $this->metricsExporter = $metricsExporter;
    }


    /**
     * @return MetricsExporterInterface
     * @throws MetricsException
     */
    public function getMetricsExporter(): MetricsExporterInterface
    {
        if (is_null($this->metricsExporter)) {
            throw new MetricsException("Metrics exporter not configured. Use setMetricsExporter method");
        }
        return $this->metricsExporter;
    }
}
