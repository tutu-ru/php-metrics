<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Metrics\Exceptions\MetricsException;

trait MetricsAwareTrait
{
    /** @var SessionRegistryInterface */
    private $metricsSessionRegistry;


    public function setMetricsSessionRegistry(SessionRegistryInterface $metricsSessions)
    {
        $this->metricsSessionRegistry = $metricsSessions;
    }


    /**
     * @return SessionRegistryInterface
     * @throws MetricsException
     */
    public function getMetricsSessionRegistry(): SessionRegistryInterface
    {
        if (is_null($this->metricsSessionRegistry)) {
            throw new MetricsException("Metrics sessions not configured. Use setMetricsSessions method");
        }
        return $this->metricsSessionRegistry;
    }
}
