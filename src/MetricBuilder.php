<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

class MetricBuilder
{
    public const METRIC_TYPE_TIMERS = 'timers';
    public const METRIC_TYPE_GAUGES = 'gauges';
    public const METRIC_TYPE_COUNTERS = 'counters';

    private const NAMESPACE_SEPARATOR = '.';
    private const NAMESPACE_DOT_REPLACER = '_';

    /** @var MetricsConfig */
    private $config;

    private $metricsType2PrefixMapping = [
        self::METRIC_TYPE_TIMERS   => self::METRIC_TYPE_TIMERS,
        self::METRIC_TYPE_GAUGES   => self::METRIC_TYPE_GAUGES,
        self::METRIC_TYPE_COUNTERS => self::METRIC_TYPE_COUNTERS,
    ];

    public function __construct(MetricsConfig $config)
    {
        $this->config = $config;
    }

    public function prepareKey(string $keyFromApp, ?string $metricsType = null, bool $isStatsdExporter = false): string
    {
        if ($this->config->prependHostnameFromApp() && !$isStatsdExporter) {
            $parts = [$this->getPreparedHostname()];

            if ($metricsType && $metricTypePart = $this->metricsType2PrefixMapping($metricsType)) {
                $parts[] = $metricTypePart;
            }
            $parts[] = $keyFromApp;

            return implode(self::NAMESPACE_SEPARATOR, $parts);
        } else {
            return $keyFromApp;
        }
    }

    public function prepareTags(array $tags): array
    {
        return array_merge($tags, ['app' => $this->config->getAppName()]);
    }

    private function metricsType2PrefixMapping(?string $metricType): ?string
    {
        return $this->metricsType2PrefixMapping[$metricType] ?? null;
    }


    private function getPreparedHostname(): string
    {
        return $this->prepareNamespace($this->config->getServerHostname());
    }


    private function prepareNamespace(string $namespace): string
    {
        if (!$this->config->replaceDotInHostname()) {
            return $namespace;
        }
        return str_replace(self::NAMESPACE_SEPARATOR, self::NAMESPACE_DOT_REPLACER, $namespace);
    }
}
