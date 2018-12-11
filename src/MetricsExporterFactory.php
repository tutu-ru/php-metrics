<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use Psr\Log\LoggerInterface;
use TutuRu\Config\ConfigContainer;
use TutuRu\Metrics\MetricsExporter\NullMetricsExporter;
use TutuRu\Metrics\MetricsExporter\UdpMetricsExporter;

class MetricsExporterFactory
{
    public static function createUdpExporter(
        ConfigContainer $config,
        LoggerInterface $logger = null
    ): MetricsExporterInterface {

        $metricsConfig = new MetricsConfig($config);
        $params = $metricsConfig->getExporterParameters();
        if ($params->isEnabled()) {
            return new UdpMetricsExporter($metricsConfig, $params);
        } else {
            return new NullMetricsExporter();
        }
    }
}
