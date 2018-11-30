<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use TutuRu\Metrics\MetricNameUtils;

class MetricNameUtilsTest extends BaseTest
{
    /**
     * @param $metricName
     * @param $expectedResult
     * @param $comment
     *
     * @dataProvider validateMetricNameDataProvider
     */
    public function testValidateMetricName($metricName, $expectedResult, $comment)
    {
        $this->assertEquals($expectedResult, MetricNameUtils::validateMetricName($metricName), $comment);
    }

    public function validateMetricNameDataProvider()
    {
        return [
            ['just_plain_word_without_dots', true, 'Word without dots should be treated valid'],
            ['just.plain.phrase.with.dots', true, 'Dot-separated phrase should be treated valid'],
            ['word_with-_underscores_-and-dashes', true, 'Underscores and dashes are acceptable'],
            ['2-much_4_w8ing', true, 'Digits are also allowed'],
            ['just plain phrase with whitespace', false, 'Whitespace isn\'t allowed'],
            ['some_long,long_phrase_with_punctuation', false, 'No commas are allowed'],
            ['some_more_punctuation;', false, 'Semicolons aren\'t allowed'],
            ['some_more_punctuation:', false, 'Colons aren\'t allowed'],
        ];
    }

    public function testPrepareHandlerNameForMetric()
    {
        $this->assertEquals(
            'Handler_name_with-unsupported-signs',
            MetricNameUtils::prepareHandlerNameForMetric('Handler\name\\\with.unsupported->signs')
        );
        $this->assertEquals(
            'Valid_handler-name',
            MetricNameUtils::prepareHandlerNameForMetric('Valid_handler-name')
        );
    }

    public function testPrepareMetricName()
    {
        $this->assertEquals(
            'Metric-name.with_unsupportedSymbols',
            MetricNameUtils::prepareMetricName('Metric-name.with_unsupportedSymbols')
        );
        $this->assertEquals(
            'valid_metric_name',
            MetricNameUtils::prepareMetricName('valid_metric_name')
        );
    }

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
