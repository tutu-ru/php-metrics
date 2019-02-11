<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

trait StatsdExporterAwareTrait
{
    /** @var StatsdExporterClientInterface|null */
    protected $statsdExporterClient;


    public function setStatsdExporterClient(StatsdExporterClientInterface $statsdExporterClient)
    {
        $this->statsdExporterClient = $statsdExporterClient;
    }
}
