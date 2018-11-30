<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

class MetricBuilder
{
    const NAMESPACE_SEPARATOR = '.';
    const NAMESPACE_DOT_REPLACER = '_';

    const METRIC_TYPE_TIMERS = 'timers';
    const METRIC_TYPE_GAUGES = 'gauges';
    const METRIC_TYPE_COUNTERS = 'counters';

    /** @var MetricsConfig */
    private $config;

    public function __construct(MetricsConfig $config)
    {
        $this->config = $config;
    }

    private function metricsType2PrefixMapping($metricType)
    {
        $_metricsType2PrefixMapping = [
            self::METRIC_TYPE_TIMERS   => self::METRIC_TYPE_TIMERS,
            self::METRIC_TYPE_GAUGES   => self::METRIC_TYPE_GAUGES,
            self::METRIC_TYPE_COUNTERS => self::METRIC_TYPE_COUNTERS,
        ];

        return array_key_exists($metricType, $_metricsType2PrefixMapping) ?
            $_metricsType2PrefixMapping[$metricType] :
            null;
    }

    /**
     * @param      $keyFromApp
     * @param null $metricsType
     * @param bool $isStatsdExporter
     * @return string
     */
    public function prepareKey($keyFromApp, $metricsType = null, bool $isStatsdExporter = false)
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
        $parts['app'] = $this->config->getAppName();
        return array_merge($tags, $parts);
    }

    /**
     * @return string
     */
    private function getPreparedHostname()
    {
        return $this->prepareNamespace($this->config->getServerHostname());
    }

    /**
     * @param  string $namespace
     * @return string
     */
    private function prepareNamespace($namespace)
    {
        if (!$this->config->replaceDotInHostname()) {
            return $namespace;
        }

        return str_replace(self::NAMESPACE_SEPARATOR, self::NAMESPACE_DOT_REPLACER, $namespace);
    }
}
