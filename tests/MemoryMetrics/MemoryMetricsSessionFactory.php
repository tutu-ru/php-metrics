<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryMetrics;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TutuRu\Metrics\MetricsConfig;
use TutuRu\Metrics\MetricsExporter\MetricsSessionFactoryInterface;
use TutuRu\Metrics\MetricsExporter\MetricsSessionInterface;
use TutuRu\Metrics\MetricsExporter\NullMetricsSession;
use TutuRu\Metrics\ExporterParams;

class MemoryMetricsSessionFactory implements MetricsSessionFactoryInterface
{
    private $testCase;

    public function __construct($testCase = null)
    {
        if (!is_null($testCase)) {
            if (class_exists('PHPUnit_Framework_TestCase')) {
                // PHPUnit 5 support
                if (is_object($testCase) && $testCase instanceof \PHPUnit_Framework_TestCase) {
                    $this->testCase = $testCase;
                } else {
                    throw new \RuntimeException('$testCase parameter: expected \PHPUnit_Framework_TestCase object');
                }
            } else {
                if (is_object($testCase) && $testCase instanceof TestCase) {
                    $this->testCase = $testCase;
                } else {
                    throw new \RuntimeException('$testCase parameter: expected ' . TestCase::class . ' object');
                }
            }
        }
    }

    /**
     * @param ExporterParams $params
     * @param MetricsConfig  $config
     * @return MetricsSessionInterface|MockObject
     */
    public function createSession(ExporterParams $params, MetricsConfig $config): MetricsSessionInterface
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


    /**
     * @return NullMetricsSession|MockObject
     */
    public function createNullSession(): MetricsSessionInterface
    {
        if (!is_null($this->testCase)) {
            return $this->testCase->getMockBuilder(NullMetricsSession::class)
                ->enableProxyingToOriginalMethods()
                ->getMock();
        } else {
            return new NullMetricsSession();
        }
    }
}
