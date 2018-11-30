<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

class MetricNameUtils
{
    public static function validateMetricName(string $name): bool
    {
        return (bool)preg_match('/^[-\.a-zA-Z0-9_]+$/', $name);
    }


    public static function prepareMetricName(string $name): string
    {
        return preg_replace('/[^-\\.a-zA-Z0-9_]+/', '_', $name);
    }


    public static function prepareMetricNameForStatsdExporter(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_]+/', '_', $name);
    }


    public static function prepareHandlerNameForMetric(string $name): string
    {
        $replacePairs = ['\\' => '_', '.' => '-', '>' => ''];
        $replacedString = strtr(trim($name, '\\'), $replacePairs);
        return preg_replace('/_+/', '_', $replacedString);
    }
}
