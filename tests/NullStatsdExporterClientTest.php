<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\NullStatsdExporterClient;

class NullStatsdExporterClientTest extends BaseTest
{
    public function testExporterInterface()
    {
        $statsdExporterClient = new NullStatsdExporterClient();

        $this->assertInstanceOf(NullStatsdExporterClient::class, $statsdExporterClient->gauge('g', 1));
        $this->assertInstanceOf(NullStatsdExporterClient::class, $statsdExporterClient->count('c', 1));
        $this->assertInstanceOf(NullStatsdExporterClient::class, $statsdExporterClient->increment('c'));
        $this->assertInstanceOf(NullStatsdExporterClient::class, $statsdExporterClient->decrement('c'));
        $this->assertInstanceOf(NullStatsdExporterClient::class, $statsdExporterClient->timing('t', 1));
        $this->assertInstanceOf(NullStatsdExporterClient::class, $statsdExporterClient->summary('s', 1));

        $statsdExporterClient->save();
    }
}
