<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Formatting\ValueFormat;
use Elqora\Chart\Support\SerializableValue;

final readonly class CategoryValueData implements ChartData
{
    /**
     * @param list<array<string, mixed>> $rows
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $categoryField,
        public string $valueField,
        public array $rows,
        public ?ValueFormat $format = null,
        public array $meta = [],
    ) {
    }

    public function kind(): string
    {
        return 'category_value';
    }

    public function family(): ChartFamily
    {
        return ChartFamily::CATEGORICAL;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            categoryField: (string) ($data['category_field'] ?? ''),
            valueField: (string) ($data['value_field'] ?? ''),
            rows: is_array($data['rows'] ?? null) ? array_values($data['rows']) : [],
            format: isset($data['format']) && is_array($data['format']) ? ValueFormat::fromArray($data['format']) : null,
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'kind' => $this->kind(),
            'category_field' => $this->categoryField,
            'value_field' => $this->valueField,
            'format' => $this->format,
            'rows' => $this->rows,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
