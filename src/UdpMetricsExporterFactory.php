<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use Psr\Log\LoggerInterface;
use TutuRu\Config\ConfigContainer;

class UdpMetricsExporterFactory
{
    public static function create(ConfigContainer $config, ?LoggerInterface $logger = null): MetricsExporterInterface
    {
        try {
            $metricsConfig = new MetricsConfig($config);
            if ($metricsConfig->isEnabled()) {
                $exporter = new UdpMetricsExporter($metricsConfig, self::getExporterParameters($config));
                if (!is_null($logger)) {
                    $exporter->setLogger($logger);
                }
                return $exporter;
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


    private static function getExporterParameters(ConfigContainer $config): UdpMetricsExporterParams
    {
        $host = $config->getValue('metrics.exporter.host', null, true);
        $port = $config->getValue('metrics.exporter.port', null, true);
        $timeout = $config->getValue('metrics.exporter.timeout', 0);
        return new UdpMetricsExporterParams((string)$host, (int)$port, (float)$timeout);
    }
}
