<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\MetricNameUtils;

class MetricNameUtilsTest extends BaseTest
{
    public function testPrepareMetricNameForStatsdExporter()
    {
        $this->assertEquals(
            'Metric_name_with_unsupportedSymbols',
            MetricNameUtils::prepareMetricNameForStatsdExporter('Metric-name.with_unsupportedSymbols')
        );
        $this->assertEquals(
            'valid_metric_name',
            MetricNameUtils::prepareMetricNameForStatsdExporter('valid_metric_name')
        );
    }
}
