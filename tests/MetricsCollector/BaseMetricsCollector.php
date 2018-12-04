<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MetricsCollector;

use TutuRu\Metrics\MetricsCollector;
use TutuRu\Tests\Metrics\NullLogger;

abstract class BaseMetricsCollector extends MetricsCollector
{
    public function __construct()
    {
        $this->setLogger(new NullLogger());
    }
}
