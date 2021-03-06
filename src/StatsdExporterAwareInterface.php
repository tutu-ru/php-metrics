<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

interface StatsdExporterAwareInterface
{
    public function setStatsdExporterClient(StatsdExporterClientInterface $statsdExporterClient);
}
