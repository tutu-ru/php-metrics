<?php
declare(strict_types=1);

namespace TutuRu\Metrics\MetricsSession;

use TutuRu\Metrics\MetricsConfig;
use TutuRu\Metrics\SessionParams;

interface MetricsSessionFactoryInterface
{
    public function createSession(SessionParams $params, MetricsConfig $config): MetricsSessionInterface;

    public function createNullSession(): MetricsSessionInterface;
}
