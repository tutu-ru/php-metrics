<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TutuRu\Metrics\MetricsConfig;
use TutuRu\Metrics\MetricsSession\MetricsSessionInterface;
use TutuRu\Metrics\MetricsSession\UdpMetricsSessionFactory;
use TutuRu\Metrics\SessionParams;

class MemoryMetricsSessionFactory extends UdpMetricsSessionFactory
{
    private $testCase;

    public function __construct(?TestCase $testCase = null)
    {
        $this->testCase = $testCase;
    }

    /**
     * @param SessionParams $params
     * @param MetricsConfig $config
     * @return MetricsSessionInterface|MockObject
     */
    public function createSession(SessionParams $params, MetricsConfig $config): MetricsSessionInterface
    {
        if (!is_null($this->testCase)) {
            return $this->testCase->getMockBuilder(MemoryMetricsSession::class)
                ->setConstructorArgs([$config, $params])
                ->enableProxyingToOriginalMethods()
                ->getMock();
        } else {
            return new MemoryMetricsSession($config, $params);
        }
    }
}
