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
                return new StatsdExporterClient(
                    $metricConfig->getAppName(),
                    $metricConfig->getStatsdExporterHost(),
                    $metricConfig->getStatsdExporterPort(),
                    $metricConfig->getStatsdExporterTimeoutSec()
                );
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
}
