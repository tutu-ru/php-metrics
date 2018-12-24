<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

interface MetricAwareInterface
{
    public function setStatsdExporterClient(StatsdExporterClientInterface $statsdExporterClient);
}
