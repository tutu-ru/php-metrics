<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\StatsdExporterClient;
use TutuRu\Metrics\StatsdExporterClientFactory;

class StatsdExporterClientTest extends BaseTest
{
    public function testExporterInterface()
    {
        $exporter = StatsdExporterClientFactory::create($this->config, new NullLogger());

        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->gauge('g', 1));
        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->count('c', 1));
        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->increment('c'));
        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->decrement('c'));
        $this->assertInstanceOf(StatsdExporterClient::class, $exporter->timing('t', 1));

        $exporter->save();
    }
}
