<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use Psr\Log\Test\TestLogger;
use TutuRu\Metrics\NullStatsdExporterClient;
use TutuRu\Metrics\StatsdExporterClient;
use TutuRu\Metrics\StatsdExporterClientFactory;

class StatsdExporterClientFactoryTest extends BaseTest
{
    public function testCreate()
    {
        $statsdExporterClient = StatsdExporterClientFactory::create($this->config, new TestLogger());
        $this->assertInstanceOf(StatsdExporterClient::class, $statsdExporterClient);
    }


    public function testCreateDisabled()
    {
        $this->config->setValue('metrics.enabled', false);
        $statsdExporterClient = StatsdExporterClientFactory::create($this->config, new TestLogger());
        $this->assertInstanceOf(NullStatsdExporterClient::class, $statsdExporterClient);
    }


    public function testCreateWithoutConnectionData()
    {
        $this->config->setValue('metrics.statsd_exporter', []);
        $statsdExporterClient = StatsdExporterClientFactory::create($this->config, new TestLogger());
        $this->assertInstanceOf(NullStatsdExporterClient::class, $statsdExporterClient);
    }
}
