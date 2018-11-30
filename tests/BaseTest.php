<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use PHPUnit\Framework\TestCase;
use TutuRu\Config\Config;
use TutuRu\Metrics\Metrics;

abstract class BaseTest extends TestCase
{
    /** @var Config */
    protected $config;

    public function setUp()
    {
        parent::setUp();
        $this->config = new Config();
        $this->config->setApplicationConfig(new TestConfig(__DIR__ . '/config/application.json'));
    }


    protected function getMemoryMetrics(): Metrics
    {
        $sessionFactory = new MemoryMetricsSessionFactory($this);
        return new Metrics($this->config, $sessionFactory, new NullLogger());
    }
}
