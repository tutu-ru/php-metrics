<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\Metrics;
use TutuRu\Metrics\SessionNames;
use TutuRu\Tests\Metrics\MetricsCollector\ExporterMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\SimpleMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\CustomMetricsCollector;

class MetricsCollectorTest extends BaseTest
{
    public function testDefaultSession()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);

        $collector = new SimpleMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->startTiming();
        $collector->endTiming();
        $this->assertNull($session->getLastCreatedConnection());

        $collector->save();
        $this->assertCount(0, $session->getLastCreatedConnection()->getMessages());

        $metrics->send();
        $this->assertCount(1, $session->getLastCreatedConnection()->getMessages());
        $this->assertRegExp(
            '/simple\.metrics\.collector:\d+|ms/',
            $session->getLastCreatedConnection()->getMessages()[0]
        );
    }


    public function testEndTimingWithoutStart()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);

        $collector = new SimpleMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->endTiming();
        $collector->save();
        $metrics->send();
        $this->assertCount(0, $session->getLastCreatedConnection()->getMessages());
    }


    public function testAddTiming()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);

        $collector = new SimpleMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->addTiming(500);
        $collector->save();
        $metrics->send();
        $this->assertCount(1, $session->getLastCreatedConnection()->getMessages());
        $this->assertEquals(
            'simple.metrics.collector:500000|ms',
            $session->getLastCreatedConnection()->getMessages()[0]
        );
    }

    public function testCustomMetrics()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);

        $collector = new CustomMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->addTiming(500);
        $collector->save();
        $metrics->send();
        $this->assertCount(2, $session->getLastCreatedConnection()->getMessages());
        $this->assertEquals(
            'simple.metrics.collector:500000|ms',
            $session->getLastCreatedConnection()->getMessages()[0]
        );
        $this->assertEquals(
            'simple.metrics.custom:50|g',
            $session->getLastCreatedConnection()->getMessages()[1]
        );
    }


    public function testStatsdExporter()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);
        /** @var MemoryMetricsSession $sessionExporter */
        $sessionExporter = $metrics->getSession(SessionNames::NAME_STATSD_EXPORTER);

        $collector = new ExporterMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->addTiming(500);
        $collector->save();
        $metrics->send();
        $this->assertCount(1, $session->getLastCreatedConnection()->getMessages());
        $this->assertEquals(
            'simple.metrics.collector:500000|ms',
            $session->getLastCreatedConnection()->getMessages()[0]
        );
        $this->assertCount(1, $sessionExporter->getLastCreatedConnection()->getMessages());
        $this->assertEquals(
            'test.exporter:500000|ms|#app:unknown',
            $sessionExporter->getLastCreatedConnection()->getMessages()[0]
        );
    }
}
