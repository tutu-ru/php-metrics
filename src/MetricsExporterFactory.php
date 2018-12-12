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
        try {
            $metricsConfig = new MetricsConfig($config);
            if ($metricsConfig->isEnabled()) {
                return new UdpMetricsExporter($metricsConfig, $metricsConfig->getExporterParameters());
            } else {
                return new NullMetricsExporter();
            }
        } catch (\Throwable $e) {
            if (!is_null($logger)) {
                $logger->error("Can't create metrics exporter: {$e}", ['lib' => 'metrics']);
            }
            return new NullMetricsExporter();
        }
    }
}
