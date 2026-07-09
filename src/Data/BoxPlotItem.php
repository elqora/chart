<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Support\SerializableValue;

final readonly class BoxPlotItem implements ArraySerializable
{
    /**
     * @param list<int|float> $outliers
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $category,
        public int|float $minimum,
        public int|float $lowerQuartile,
        public int|float $median,
        public int|float $upperQuartile,
        public int|float $maximum,
        public array $outliers = [],
        public array $meta = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            category: (string) ($data['category'] ?? ''),
            minimum: is_numeric($data['minimum'] ?? null) ? $data['minimum'] + 0 : 0,
            lowerQuartile: is_numeric($data['lower_quartile'] ?? null) ? $data['lower_quartile'] + 0 : 0,
            median: is_numeric($data['median'] ?? null) ? $data['median'] + 0 : 0,
            upperQuartile: is_numeric($data['upper_quartile'] ?? null) ? $data['upper_quartile'] + 0 : 0,
            maximum: is_numeric($data['maximum'] ?? null) ? $data['maximum'] + 0 : 0,
            outliers: is_array($data['outliers'] ?? null) ? array_values($data['outliers']) : [],
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'category' => $this->category,
            'minimum' => $this->minimum,
            'lower_quartile' => $this->lowerQuartile,
            'median' => $this->median,
            'upper_quartile' => $this->upperQuartile,
            'maximum' => $this->maximum,
            'outliers' => $this->outliers === [] ? null : $this->outliers,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
