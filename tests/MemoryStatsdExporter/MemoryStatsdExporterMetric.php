<?php
declare(strict_types=1);

namespace TutuRu\Tests\Metrics\MemoryStatsdExporter;

class MemoryStatsdExporterMetric
{
    /** @var string */
    private $name;

    /** @var string */
    private $unit;

    /** @var float */
    private $value;

    /** @var string[] */
    private $tags;

    public static function createFromRawString(string $rawString): ?self
    {
        $result = preg_match('/^([a-zA-Z0-9_]+):(-?\d+(\.\d+)?)\|(\w+)\|\#(.*)$/', $rawString, $m);
        if (!$result) {
            throw new \Exception("Metric in unknown format: {$rawString}");
        }

        $metric = new self();
        $metric->name = (string)$m[1];
        $metric->value = (float)$m[2];
        $metric->unit = (string)$m[4];
        $metric->tags = [];
        foreach (explode(',', $m[5]) as $rawTag) {
            list($key, $value) = explode(':', $rawTag);
            $metric->tags[$key] = $value;
        }
        return $metric;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function getUnit(): string
    {
        return $this->unit;
    }


    public function getValue(): float
    {
        return $this->value;
    }


    public function getTags(): array
    {
        return $this->tags;
    }
}
