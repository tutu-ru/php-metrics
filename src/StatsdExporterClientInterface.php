<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

interface StatsdExporterClientInterface
{
    public function count(string $key, float $value, array $tags = []): StatsdExporterClientInterface;

    public function increment(string $key, array $tags = []): StatsdExporterClientInterface;

    public function decrement(string $key, array $tags = []): StatsdExporterClientInterface;

    public function timing(string $key, float $seconds, array $tags = []): StatsdExporterClientInterface;

    public function gauge(string $key, float $value, array $tags = []): StatsdExporterClientInterface;

    /**
     * Метод добавлен для решения проблемы с gauge метриками уровня сервиса.
     * Проблема в том, что разные значения метрики записываются с разных бэкендов и остаются в их экспортерах.
     * В итоге в прометее мы видим значения A и B, и не имеем возможности понять, какое из них актуальное.
     * Дополнительно добавляем к gauge счетчик - "сколько раз я записал этот gauge".
     * При постоении графиков можно пересечь увеличение счетчика с gauge'м и получить только актуальные значения.
     */
    public function gaugeServiceLayer(string $key, float $value, array $tags = []): StatsdExporterClientInterface;
    
    /**
     * Смысловая добавка для метода gaugeServiceLayer()
     * Необходимо использовать в том случае, когда нам важно получать метрикку с конкретного инстанса
     * По сути реализует обычный метод gauge()
     */
    public function gaugeInstanceLayer(string $key, float $value, array $tags = []): StatsdExporterClientInterface;

    public function summary(string $key, float $value, array $tags = []): StatsdExporterClientInterface;

    public function save(): void;
}
