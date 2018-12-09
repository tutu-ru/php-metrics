<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\MetricBuilder;
use TutuRu\Metrics\MetricsConfig;

class MetricBuilderTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();
        putenv('HOSTNAME=statsd.phpunit');
    }

    public function testPrepareKeyWithDefaultConfig()
    {
        $builder = new MetricBuilder(new MetricsConfig($this->config));
        $this->assertEquals('test', $builder->prepareKey('test'));
    }

    public function testPrepareKeyWithEnabledConfig()
    {
        $this->config->setApplicationValue('statsd.prepend_hostname', 1);
        $builder = new MetricBuilder(new MetricsConfig($this->config));

        $this->assertEquals('statsd_phpunit.test', $builder->prepareKey('test'));
    }

    public function testPrepareKeyWithDisabledConfig()
    {
        $this->config->setApplicationValue('statsd.prepend_hostname', 0);
        $builder = new MetricBuilder(new MetricsConfig($this->config));

        $this->assertEquals('test', $builder->prepareKey('test'));
    }

    public function testPrepareKeyWithoutReplaceDotsInHostname()
    {
        $this->config->setApplicationValue('statsd.prepend_hostname', 1);
        $this->config->setApplicationValue('statsd.replace_dot_in_hostname', 0);
        $builder = new MetricBuilder(new MetricsConfig($this->config));

        $this->assertEquals('statsd.phpunit.test', $builder->prepareKey('test'));
    }

    public function metricPrefixesDataProvider()
    {
        return [
            ['timers'],
            ['counters'],
            ['gauges'],
        ];
    }

    /**
     * @dataProvider metricPrefixesDataProvider
     * @param string $metric
     */
    public function testPrepareKeyWithMetricType(string $metric)
    {
        $this->config->setApplicationValue('statsd.prepend_hostname', 1);
        $builder = new MetricBuilder(new MetricsConfig($this->config));

        $keyFromApp = 'test.another.metric';
        $this->assertEquals(
            'statsd_phpunit.' . $metric . '.' . $keyFromApp,
            $builder->prepareKey($keyFromApp, $metric)
        );
    }

    /**
     * @dataProvider metricPrefixesDataProvider
     * @param string $metric
     */
    public function testPrepareKeyForStatsdExporter(string $metric)
    {
        $this->config->setApplicationValue('statsd.prepend_hostname', 1);
        $builder = new MetricBuilder(new MetricsConfig($this->config));

        $keyFromApp = 'test.another.metric';
        $this->assertEquals($keyFromApp, $builder->prepareKey($keyFromApp, $metric, $isStatsdExporter = true));
    }

    public function testPrepareKeyWithNotAllowedMetricType()
    {
        $this->config->setApplicationValue('statsd.prepend_hostname', 1);
        $builder = new MetricBuilder(new MetricsConfig($this->config));

        $keyFromApp = 'test.another.metric';
        $this->assertEquals(
            'statsd_phpunit.' . $keyFromApp,
            $builder->prepareKey($keyFromApp, 'unknown')
        );
    }

    public function testPrepareTagsWithoutAppName()
    {
        $builder = new MetricBuilder(new MetricsConfig($this->config));
        $this->assertEquals(['app' => 'unknown', 'test' => 1], $builder->prepareTags(['test' => 1]));
    }

    public function testPrepareTags()
    {
        $builder = new MetricBuilder(new MetricsConfig($this->config));
        $this->config->setApplicationValue('name', 'testApp');
        $this->assertEquals(['app' => 'testApp', 'test' => 1], $builder->prepareTags(['test' => 1]));
    }
}
