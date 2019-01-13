<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

use Psr\Log\LoggerInterface;
use TutuRu\Config\ConfigInterface;

class StatsdExporterClientFactory
{
    public static function create(ConfigInterface $config, ?LoggerInterface $logger): StatsdExporterClientInterface
    {
        try {
            $metricConfig = new MetricConfig($config);
            if ($metricConfig->isEnabled()) {
                return new StatsdExporterClient($metricConfig->getAppName(), self::getExporterParameters($config));
            } else {
                return new NullStatsdExporterClient();
            }
        } catch (\Throwable $e) {
            if (!is_null($logger)) {
                $logger->error("Can't create metrics exporter: {$e}", ['lib' => 'metrics']);
            }
            return new NullStatsdExporterClient();
        }
    }


    private static function getExporterParameters(ConfigInterface $config): StatsdExporterClientParams
    {
        $host = $config->getValue('metrics.statsd_exporter.host', true);
        $port = $config->getValue('metrics.statsd_exporter.port', true);
        $timeout = $config->getValue('metrics.statsd_exporter.timeout') ?? 0;
        return new StatsdExporterClientParams((string)$host, (int)$port, (float)$timeout);
    }
}
