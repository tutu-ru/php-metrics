<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Metrics\Exceptions\MetricsException;

interface MetricsAwareInterface
{
    public function setMetricsSessionRegistry(SessionRegistryInterface $metricsSessions);

    /**
     * @return SessionRegistryInterface
     * @throws MetricsException
     */
    public function getMetricsSessionRegistry(): SessionRegistryInterface;
}
