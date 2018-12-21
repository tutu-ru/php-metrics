<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\NullStatsdExporterClient;
use TutuRu\Metrics\StatsdExporterClient;
use TutuRu\Metrics\StatsdExporterClientFactory;

class StatsdExporterClientFactoryTest extends BaseTest
{
    public function testCreate()
    {
        $statsdExporterClient = StatsdExporterClientFactory::create($this->config, new NullLogger());
        $this->assertInstanceOf(StatsdExporterClient::class, $statsdExporterClient);
    }


    public function testCreateDisabled()
    {
        $this->config->setApplicationValue('metrics.enabled', false);
        $statsdExporterClient = StatsdExporterClientFactory::create($this->config, new NullLogger());
        $this->assertInstanceOf(NullStatsdExporterClient::class, $statsdExporterClient);
    }


    public function testCreateWithoutConnectionData()
    {
        $this->config->setApplicationValue('metrics.statsd_exporter', []);
        $statsdExporterClient = StatsdExporterClientFactory::create($this->config, new NullLogger());
        $this->assertInstanceOf(NullStatsdExporterClient::class, $statsdExporterClient);
    }
}
