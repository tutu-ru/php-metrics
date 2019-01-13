<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use PHPUnit\Framework\TestCase;
use TutuRu\Config\JsonConfig\MutableJsonConfig;

abstract class BaseTest extends TestCase
{
    /** @var MutableJsonConfig */
    protected $config;

    public function setUp()
    {
        parent::setUp();
        $this->config = new MutableJsonConfig(__DIR__ . '/config/app.json');
    }
}
