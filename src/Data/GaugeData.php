<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Formatting\ValueFormat;
use Elqora\Chart\Support\SerializableValue;

final readonly class GaugeData implements ChartData
{
    /**
     * @param list<Range> $ranges
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public int|float $value,
        public ?float $minimum = null,
        public ?float $maximum = null,
        public ?ValueFormat $format = null,
        public array $ranges = [],
        public array $meta = [],
    ) {
    }

    public function kind(): string
    {
        return 'gauge';
    }

    public function family(): ChartFamily
    {
        return ChartFamily::RADIAL;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: is_numeric($data['value'] ?? null) ? $data['value'] + 0 : 0,
            minimum: isset($data['minimum']) ? (float) $data['minimum'] : null,
            maximum: isset($data['maximum']) ? (float) $data['maximum'] : null,
            format: isset($data['format']) && is_array($data['format']) ? ValueFormat::fromArray($data['format']) : null,
            ranges: array_map(
                static fn (array $range): Range => Range::fromArray($range),
                is_array($data['ranges'] ?? null) ? array_values($data['ranges']) : [],
            ),
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'kind' => $this->kind(),
            'value' => $this->value,
            'minimum' => $this->minimum,
            'maximum' => $this->maximum,
            'format' => $this->format,
            'ranges' => $this->ranges === [] ? null : $this->ranges,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
