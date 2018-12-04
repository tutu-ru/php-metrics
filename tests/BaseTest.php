<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use PHPUnit\Framework\TestCase;
use TutuRu\Config\ConfigContainer;
use TutuRu\Metrics\SessionRegistry;

abstract class BaseTest extends TestCase
{
    /** @var ConfigContainer */
    protected $config;

    public function setUp()
    {
        parent::setUp();
        $this->config = new ConfigContainer();
        $this->config->setApplicationConfig(new TestConfig(__DIR__ . '/config/application.json'));
    }


    protected function getMemoryMetrics(): SessionRegistry
    {
        $sessionFactory = new MemoryMetricsSessionFactory($this);
        return new SessionRegistry($this->config, $sessionFactory, new NullLogger());
    }
}
