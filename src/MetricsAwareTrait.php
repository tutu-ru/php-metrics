<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Metrics\Exceptions\MetricsException;

trait MetricsAwareTrait
{
    /** @var MetricsInterface */
    private $metrics;


    public function setMetrics(MetricsInterface $metricsSessions)
    {
        $this->metrics = $metricsSessions;
    }


    /**
     * @return MetricsInterface
     * @throws MetricsException
     */
    public function getMetrics(): MetricsInterface
    {
        if (is_null($this->metrics)) {
            throw new MetricsException("Metrics sessions not configured. Use setMetricsSessions method");
        }
        return $this->metrics;
    }
}
