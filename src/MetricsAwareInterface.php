<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use TutuRu\Metrics\Exceptions\MetricsException;

interface MetricsAwareInterface
{
    public function setMetrics(MetricsInterface $metricsSessions);

    /**
     * @return MetricsInterface
     * @throws MetricsException
     */
    public function getMetrics(): MetricsInterface;
}
