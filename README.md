# Библиотека Metrics

## Версии 3.0.\* мертвы - используйте 2ю мажорную версию!

## Отправка метрик в statsd_exporter

```php
use TutuRu\Metrics\StatsdExporterClientFactory;

$statsdExporterClient = StatsdExporterClientFactory::create($config, $psrLogger);

$statsdExporterClient->count('counter', 10);
$statsdExporterClient->gauge('gauge', 50);

// в этот момент происходит реальная отправка данных
$statsdExporterClient->save();
```

### MetricCollector

Объект позволяющей инкапсулировать логику замеров в одном месте.

```php
use TutuRu\Metrics\StatsdExporterClientFactory;
use TutuRu\Metrics\MetricCollector;

$statsdExporterClient = StatsdExporterClientFactory::create($config, $psrLogger);

class MyDataCollector extends MetricCollector
{
    protected function getTimersMetricName(): string
    {
        return 'my_metrics';
    }


    protected function getTimersMetricTags(): array
    {
        return ['env' => 'test'];
    }


    protected function onSave(): void
    {
        $this->increment('additional_data');
    }
}

$collector = new MyDataCollector();
$collector->startTiming();
// code
$collector->endTiming();
$collector->sendToStatsdExporter($statsdExporterClient);

$statsdExporterClient->save();
```

### Передача клиента другим объектам

Для того, чтобы клиент декларировал возможность передать ему stats_exporter клиент нужен интерфейс `MetricAwareInterface`.

Пример:
```php
use TutuRu\Metrics\StatsdExporterClientFactory;
use TutuRu\Metrics\StatsdExporterAwareInterface;
use TutuRu\Metrics\StatsdExporterAwareTrait;

$statsdExporterClient = StatsdExporterClientFactory::create($config, $psrLogger);

class MyObject implements StatsdExporterAwareInterface
{
    use StatsdExporterAwareTrait;

    public function doSomething()
    {
        if (!is_null($this->statsdExporterClient)) {
            $this->statsdExporterClient->summary('summary_metric', 500);
        }
    }
}

$object = new MyObject();
$object->setStatsdExporterClient($statsdExporterClient);
$object->doSomething();

$statsdExporterClient->save();
```

## Использование в тестах других библиотек

```php
use PHPUnit\Framework\TestCase;
use TutuRu\Tests\Metrics\MemoryStatsdExporter\MemoryStatsdExporterClient;

class MyTest extends TestCase
{
    public function testMetrics()
    {
        $statsdExporterClient = new MemoryStatsdExporterClient($appName);

        $testObject = new TestObject();
        $testObject->setStatsdExporterClient($statsdExporterClient);
        $testObject->someCode();

        $statsdExporterClient->save();

        $metrics = $statsdExporterClient->getExportedMetrics();

        $firstMetric = current($metrics);
        $firstMetric->getName();
        $firstMetric->getUnit();
        $firstMetric->getValue();
        $firstMetric->getTags();
    }
}
```
