<?php
declare(strict_types=1);

namespace TutuRu\Metrics;

class MetricType
{
    /**
     * Метрика характеризует низкоуровневый механизм, с которым общается php-код, например БД
     */
    public const TYPE_LOW_LEVEL = 'low_level';

    /**
     * Метрика касается одного из базовых, системных механизмов, который реализован в нашем коде
     * и который может использовать для своей работы низкоуровневые механизмы.
     */
    public const TYPE_CORE = 'core';

    /**
     * Метрика отражает высокоуровневый "бизнес"-показатель
     */
    public const TYPE_BUSINESS = 'business';
}
