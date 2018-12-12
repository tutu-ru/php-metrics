<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics;

use PHPUnit\Framework\TestCase;
use TutuRu\Config\ConfigContainer;
use TutuRu\Tests\Config\JsonConfig\JsonConfigFactory;

abstract class BaseTest extends TestCase
{
    /** @var ConfigContainer */
    protected $config;

    public function setUp()
    {
        parent::setUp();
        $this->config = JsonConfigFactory::createConfig(__DIR__ . '/config/application.json');
    }
}
