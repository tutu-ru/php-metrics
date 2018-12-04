<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\SessionNames;

class MetricsSessionTest extends BaseTest
{
    private function getSession($name = SessionNames::NAME_DEFAULT): MemoryMetricsSession
    {
        return $this->getMemoryMetrics()->getSession($name);
    }

    public function testSend()
    {
        $session = $this->getSession();
        $this->assertNull($session->getLastCreatedConnection());

        $session->count('counter', 10);
        $this->assertCount(0, $session->getLastCreatedConnection()->getMessages());

        $session->send();
        $this->assertCount(1, $session->getLastCreatedConnection()->getMessages());
    }

    public function testCount()
    {
        $session = $this->getSession();
        $session->count('counter', 10);
        $session->count('counter', 20);
        $session->send();

        $this->assertEquals(
            ['default.counter:10|c', 'default.counter:20|c'],
            $session->getLastCreatedConnection()->getMessages()
        );
    }

    public function testIncrement()
    {
        $session = $this->getSession();
        $session->increment('counter');
        $session->increment('counter');
        $session->send();

        $this->assertEquals(
            ['default.counter:1|c', 'default.counter:1|c'],
            $session->getLastCreatedConnection()->getMessages()
        );
    }

    public function testDecrement()
    {
        $session = $this->getSession();
        $session->decrement('counter');
        $session->decrement('counter');
        $session->send();

        $this->assertEquals(
            ['default.counter:-1|c', 'default.counter:-1|c'],
            $session->getLastCreatedConnection()->getMessages()
        );
    }

    public function testTiming()
    {
        $session = $this->getSession();
        $session->timing('test', 25);
        $session->timing('test', 40);
        $session->send();

        $this->assertEquals(
            ['default.test:25000|ms', 'default.test:40000|ms'],
            $session->getLastCreatedConnection()->getMessages()
        );
    }

    public function testMeasureAsTiming()
    {
        $session = $this->getSession();
        $session->measureAsTiming('test', 20000);
        $session->measureAsTiming('test', 45000);
        $session->send();

        $this->assertEquals(
            ['default.test:20000|ms', 'default.test:45000|ms'],
            $session->getLastCreatedConnection()->getMessages()
        );
    }


    public function testGauge()
    {
        $session = $this->getSession();
        $session->gauge('gauge', 2);
        $session->gauge('gauge', 4);
        $session->send();

        $this->assertEquals(
            ['default.gauge:2|g', 'default.gauge:4|g'],
            $session->getLastCreatedConnection()->getMessages()
        );
    }

    public function testTags()
    {
        $session = $this->getSession(SessionNames::NAME_STATSD_EXPORTER);
        $session
            ->count('counter', 10, ['test' => 'phpunit'])
            ->decrement('decrement', ['test' => 'phpunit'])
            ->increment('increment', ['test' => 'phpunit'])
            ->timing('timing', 10, ['test' => 'phpunit'])
            ->gauge('gauge', 20, ['test' => 'phpunit'])
            ->send();

        foreach ($session->getLastCreatedConnection()->getMessages() as $message) {
            $this->assertStringEndsWith("#test:phpunit,app:unknown", $message);
        }
    }
}
