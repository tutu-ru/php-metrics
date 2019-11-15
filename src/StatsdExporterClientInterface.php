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
     * При построении графиков можно пересечь увеличение счетчика с gauge'м и получить только актуальные значения.
     */
    public function gaugeServiceLayer(string $key, float $value, array $tags = []): StatsdExporterClientInterface;
    
    /**
     * Смысловая добавка для метода gaugeServiceLayer()
     * Необходимо использовать в том случае, когда метрика имеет смысл на уровне инстанса
     */
    public function gaugeInstanceLayer(string $key, float $value, array $tags = []): StatsdExporterClientInterface;

    public function summary(string $key, float $value, array $tags = []): StatsdExporterClientInterface;

    /**
     * Метод для сбора метрики типа - Гистограма
     * Для корректной работы необходимо в конфиге statsd_exporter завести правило,
     * по которому будет определеяться тип метрики и набор buckets в которые будут "раскладываться" значения.
     *
     * statsd_exporter на совей стороне будет отрезать суффикс '_hg_' . $bucketSetup,
     * и в Prometheus будут метрики с чистыми именами
     *
     * По умолчанию заведены следующие $bucketSetup:
     * 'ms' - 0.1мс, 0.5мс, 1мс, 3мс, 5мс, 10мс, 20мс, 30мс, 50мс, 70мс, 100мс, 150мс, 200мс, 300мс, 500мс, 1с, +Inf
     * 's' - 10мс, 50мс, 100мс, 300мс, 500мс, 1с, 2с, 3с, 5с, 7с, 10с, 15с, 20с, 30с, 50с, +Inf
     */
    public function histogram(
        string $key,
        float $value,
        string $bucketSetup,
        array $tags = []
    ): StatsdExporterClientInterface;

    public function save(): void;
}
