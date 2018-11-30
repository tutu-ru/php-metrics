<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use PHPUnit\Framework\MockObject\MockObject;
use RM\StatsD\Session\NullSession;
use TutuRu\Metrics\SessionNames;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenCustomMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\BrokenTimingKeyMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\ExporterMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\SimpleMetricsCollector;
use TutuRu\Tests\Metrics\MetricsCollector\CustomMetricsCollector;

class MetricsCollectorTest extends BaseTest
{
    public function testDefaultSession()
    {
        $metrics = $this->getMemoryMetrics();
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);

        $collector = new SimpleMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->startTiming();
        $collector->endTiming();
        $this->assertNull($session->getLastCreatedConnection());

        $result = $collector->save();
        $this->assertTrue($result);
        $this->assertCount(0, $session->getLastCreatedConnection()->getMessages());

        $metrics->send();
        $this->assertCount(1, $session->getLastCreatedConnection()->getMessages());
        $this->assertRegExp(
            '/simple\.metrics\.collector:\d+|ms/',
            $session->getLastCreatedConnection()->getMessages()[0]
        );
    }


    public function testNotInitializedCollector()
    {
        $collector = new SimpleMetricsCollector();
        $collector->startTiming();
        $collector->endTiming();
        $this->assertFalse($collector->save());
    }


    public function testEndTimingWithoutStart()
    {
        $metrics = $this->getMemoryMetrics();
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);

        $collector = new SimpleMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->endTiming();
        $result = $collector->save();
        $this->assertTrue($result);

        $metrics->send();
        $this->assertCount(0, $session->getLastCreatedConnection()->getMessages());
    }


    public function testAddTiming()
    {
        $metrics = $this->getMemoryMetrics();
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);

        $collector = new SimpleMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->addTiming(500);
        $result = $collector->save();
        $this->assertTrue($result);

        $metrics->send();
        $this->assertCount(1, $session->getLastCreatedConnection()->getMessages());
        $this->assertEquals(
            'simple.metrics.collector:500000|ms',
            $session->getLastCreatedConnection()->getMessages()[0]
        );
    }

    public function testCustomMetrics()
    {
        $metrics = $this->getMemoryMetrics();
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);

        $collector = new CustomMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->addTiming(500);
        $result = $collector->save();
        $this->assertTrue($result);

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
        $metrics = $this->getMemoryMetrics();
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);
        /** @var MemoryMetricsSession $sessionExporter */
        $sessionExporter = $metrics->getSession(SessionNames::NAME_STATSD_EXPORTER);

        $collector = new ExporterMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->addTiming(500);
        $result = $collector->save();
        $this->assertTrue($result);

        $metrics->send();
        $this->assertCount(1, $session->getLastCreatedConnection()->getMessages());
        $this->assertEquals(
            'simple.metrics.collector:500000|ms',
            $session->getLastCreatedConnection()->getMessages()[0]
        );
        $this->assertCount(1, $sessionExporter->getLastCreatedConnection()->getMessages());
        $this->assertEquals(
            'test.exporter:500000|ms|#env:test,app:unknown',
            $sessionExporter->getLastCreatedConnection()->getMessages()[0]
        );
    }

    public function testStatsdExporterWithBrokenConfig()
    {
        $this->config->setApplicationConfig(new TestConfig(__DIR__ . '/config/without_exporter.json'));

        $metrics = $this->getMemoryMetrics();
        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_WORK);
        /** @var NullSession|MockObject $nullSession */
        $nullSession = $metrics->getNullSession();
        $nullSession->expects($this->exactly(1))->method('timing');

        $collector = new ExporterMetricsCollector();
        $collector->setMetrics($metrics);

        $collector->addTiming(500);
        $result = $collector->save();
        $this->assertTrue($result);

        $metrics->send();
        $this->assertCount(1, $session->getLastCreatedConnection()->getMessages());
        $this->assertEquals(
            'simple.metrics.collector:500000|ms',
            $session->getLastCreatedConnection()->getMessages()[0]
        );
    }

    public function testExceptionInSaveCustomMetrics()
    {
        $metrics = $this->getMemoryMetrics();
        $collector = new BrokenCustomMetricsCollector();
        $collector->setMetrics($metrics);

        $this->assertFalse($collector->save());
    }

    public function testExceptionInGetTimingKey()
    {
        $metrics = $this->getMemoryMetrics();
        $collector = new BrokenTimingKeyMetricsCollector();
        $collector->setMetrics($metrics);

        $this->assertTrue($collector->save());
    }
}
