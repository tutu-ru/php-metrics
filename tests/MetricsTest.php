<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use PHPUnit\Framework\MockObject\MockObject;
use TutuRu\Metrics\Exceptions\UnknownSessionException;
use TutuRu\Metrics\MetricsSession\MetricsSessionInterface;
use TutuRu\Metrics\MetricsSession\NullMetricsSession;
use TutuRu\Metrics\SessionNames;

class MetricsTest extends BaseTest
{
    private const PORT_DEFAULT = 3434;
    private const PORT_WORK = 3456;


    public function testGetSessionDefault()
    {
        $metrics = $this->getMemoryMetrics();

        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_DEFAULT);
        $this->assertEquals(self::PORT_DEFAULT, $session->getParams()->getPort());
    }


    public function testGetSessionCache()
    {
        $metrics = $this->getMemoryMetrics();

        $this->assertSame($metrics->getSession(SessionNames::NAME_WORK), $metrics->getSession(SessionNames::NAME_WORK));
    }


    public function testGetSessionUnknown()
    {
        $metrics = $this->getMemoryMetrics();

        $this->expectException(UnknownSessionException::class);
        $metrics->getSession('unknown');
    }


    public function testGetNullSession()
    {
        $metrics = $this->getMemoryMetrics();

        $this->assertInstanceOf(NullMetricsSession::class, $metrics->getNullSession());
    }


    public function testGetNullSessionSameObject()
    {
        $metrics = $this->getMemoryMetrics();

        $this->assertSame($metrics->getNullSession(), $metrics->getNullSession());
    }


    public function testGetRequestedSessionOrDefault()
    {
        $metrics = $this->getMemoryMetrics();

        /** @var MemoryMetricsSession $session */
        $session = $metrics->getRequestedSessionOrDefault(SessionNames::NAME_WORK);
        $this->assertEquals(self::PORT_WORK, $session->getParams()->getPort());
    }


    public function testGetRequestedSessionOrDefaultWithUnknown()
    {
        $metrics = $this->getMemoryMetrics();

        /** @var MemoryMetricsSession $session */
        $session = $metrics->getRequestedSessionOrDefault('unknown');
        $this->assertEquals(self::PORT_DEFAULT, $session->getParams()->getPort());
    }


    public function testGetRequestedSessionOrDefaultWithoutDefault()
    {
        $this->config->setApplicationConfig(new TestConfig(__DIR__ . '/config/without_default.json'));
        $metrics = $this->getMemoryMetrics();

        $this->assertInstanceOf(NullMetricsSession::class, $metrics->getRequestedSessionOrDefault('unknown'));
    }


    public function testGetAllSessions()
    {
        $metrics = $this->getMemoryMetrics();

        $this->assertCount(0, $metrics->getAllSessions());

        $metrics->getSession(SessionNames::NAME_DEFAULT);
        $this->assertEquals(
            [
                SessionNames::NAME_DEFAULT => $metrics->getSession(SessionNames::NAME_DEFAULT)
            ],
            $metrics->getAllSessions()
        );

        $metrics->getSession(SessionNames::NAME_WORK);
        $this->assertEquals(
            [
                SessionNames::NAME_DEFAULT => $metrics->getSession(SessionNames::NAME_DEFAULT),
                SessionNames::NAME_WORK    => $metrics->getSession(SessionNames::NAME_WORK),
            ],
            $metrics->getAllSessions()
        );
    }


    public function testGetAllSessionsNotContainsNullSession()
    {
        $metrics = $this->getMemoryMetrics();

        $metrics->getNullSession();
        $this->assertCount(0, $metrics->getAllSessions());
    }


    public function testSend()
    {
        $metrics = $this->getMemoryMetrics();

        /** @var MetricsSessionInterface|MockObject $default */
        $default = $metrics->getSession(SessionNames::NAME_DEFAULT);
        /** @var MetricsSessionInterface|MockObject $work */
        $work = $metrics->getSession(SessionNames::NAME_WORK);

        $default->expects($this->exactly(1))->method('send');
        $work->expects($this->exactly(1))->method('send');
        $metrics->send();
    }
}
