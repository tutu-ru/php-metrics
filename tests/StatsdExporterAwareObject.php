<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\StatsdExporterAwareInterface;
use TutuRu\Metrics\StatsdExporterAwareTrait;
use TutuRu\Metrics\StatsdExporterClientInterface;

class StatsdExporterAwareObject implements StatsdExporterAwareInterface
{
    use StatsdExporterAwareTrait;

    public function getStatsdExporterClient(): ?StatsdExporterClientInterface
    {
        return $this->statsdExporterClient;
    }
}
