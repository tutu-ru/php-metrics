<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use PHPUnit\Framework\MockObject\MockObject;
use TutuRu\Metrics\Exceptions\UnknownSessionException;
use TutuRu\Metrics\Metrics;
use TutuRu\Metrics\MetricsSession\MetricsSessionInterface;
use TutuRu\Metrics\MetricsSession\NullMetricsSession;
use TutuRu\Metrics\SessionNames;

class MetricsTest extends BaseTest
{
    private const PORT_DEFAULT = 3434;
    private const PORT_WORK = 3456;


    public function testGetSessionDefault()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);

        /** @var MemoryMetricsSession $session */
        $session = $metrics->getSession(SessionNames::NAME_DEFAULT);
        $this->assertEquals(self::PORT_DEFAULT, $session->getParams()->getPort());
    }


    public function testGetSessionCache()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);

        $this->assertSame($metrics->getSession(SessionNames::NAME_WORK), $metrics->getSession(SessionNames::NAME_WORK));
    }


    public function testGetSessionUnknown()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);

        $this->expectException(UnknownSessionException::class);
        $metrics->getSession('unknown');
    }


    public function testGetNullSession()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);

        $this->assertInstanceOf(NullMetricsSession::class, $metrics->getNullSession());
    }


    public function testGetNullSessionSameObject()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);

        $this->assertSame($metrics->getNullSession(), $metrics->getNullSession());
    }


    public function testGetRequestedSessionOrDefault()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);

        /** @var MemoryMetricsSession $session */
        $session = $metrics->getRequestedSessionOrDefault(SessionNames::NAME_WORK);
        $this->assertEquals(self::PORT_WORK, $session->getParams()->getPort());
    }


    public function testGetRequestedSessionOrDefaultWithUnknown()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);

        /** @var MemoryMetricsSession $session */
        $session = $metrics->getRequestedSessionOrDefault('unknown');
        $this->assertEquals(self::PORT_DEFAULT, $session->getParams()->getPort());
    }


    public function testGetRequestedSessionOrDefaultWithoutDefault()
    {
        $this->config->setApplicationConfig(new TestConfig(__DIR__ . '/config/without_default.json'));

        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);

        $this->assertInstanceOf(NullMetricsSession::class, $metrics->getRequestedSessionOrDefault('unknown'));
    }


    public function testGetAllSessions()
    {
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);

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
        $sessionFactory = new MemoryMetricsSessionFactory();
        $metrics = new Metrics($this->config, null, $sessionFactory);

        $metrics->getNullSession();
        $this->assertCount(0, $metrics->getAllSessions());
    }


    public function testSend()
    {
        $sessionFactory = new MemoryMetricsSessionFactory($this);
        $metrics = new Metrics($this->config, null, $sessionFactory);

        /** @var MetricsSessionInterface|MockObject $default */
        $default = $metrics->getSession(SessionNames::NAME_DEFAULT);
        /** @var MetricsSessionInterface|MockObject $work */
        $work = $metrics->getSession(SessionNames::NAME_WORK);

        $default->expects($this->exactly(1))->method('send');
        $work->expects($this->exactly(1))->method('send');
        $metrics->send();
    }
}
