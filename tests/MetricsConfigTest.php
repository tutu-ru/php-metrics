<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\MetricsConfig;

class MetricsConfigTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();
        MetricsConfig::disable();
    }

    public function tearDown()
    {
        MetricsConfig::disable();
        parent::tearDown();
    }

    public function testEnabledWithDefault()
    {
        $this->config->setApplicationConfig(new TestConfig(__DIR__ . '/config/without_default.json'));
        $config = new MetricsConfig($this->config);
        $this->assertFalse($config->isEnabled());
    }

    public function testEnabledWithConfig()
    {
        $config = new MetricsConfig($this->config);
        $this->config->setApplicationValue('statsd.enabled', 1);
        $this->assertTrue($config->isEnabled());
    }

    public function testEnabledWithRuntime()
    {
        $config = new MetricsConfig($this->config);
        $this->config->setApplicationValue('statsd.enabled', 0);
        MetricsConfig::enable();
        $this->assertTrue($config->isEnabled());
    }

    public function testGetSessionParameters()
    {
        $config = new MetricsConfig($this->config);

        $this->assertNull($config->getSessionParameters('unknown'));

        $params = $config->getSessionParameters('work');
        $this->assertEquals('localhost', $params->getHost());
        $this->assertEquals(3456, $params->getPort());
        $this->assertEquals(null, $params->getNamespace());
        $this->assertEquals(1, $params->getTimeoutInSec());
        $this->assertEquals(true, $params->isSessionEnabled());
        $this->assertEquals(false, $params->isExporter());

        $params = $config->getSessionParameters('statsd_exporter');
        $this->assertEquals('localhost', $params->getHost());
        $this->assertEquals(3457, $params->getPort());
        $this->assertEquals('test', $params->getNamespace());
        $this->assertEquals(2, $params->getTimeoutInSec());
        $this->assertEquals(true, $params->isSessionEnabled());
        $this->assertEquals(true, $params->isExporter());
    }
}
