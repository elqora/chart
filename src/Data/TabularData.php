<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Dimensions\Dimension;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Series\Series;
use Elqora\Chart\Support\SerializableValue;

final readonly class TabularData implements ChartData
{
    /**
     * @param list<array<string, mixed>> $rows
     * @param list<Dimension> $dimensions
     * @param list<Series> $series
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $categoryField,
        public array $rows,
        public array $series,
        public array $dimensions = [],
        public ?PresentationHints $presentation = null,
        public array $meta = [],
    ) {
    }

    public function kind(): string
    {
        return 'tabular';
    }

    public function family(): ChartFamily
    {
        return ChartFamily::CARTESIAN;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            categoryField: (string) ($data['category_field'] ?? ''),
            rows: is_array($data['rows'] ?? null) ? array_values($data['rows']) : [],
            series: array_map(
                static fn (array $series): Series => Series::fromArray($series),
                is_array($data['series'] ?? null) ? array_values($data['series']) : [],
            ),
            dimensions: array_map(
                static fn (array $dimension): Dimension => Dimension::fromArray($dimension),
                is_array($data['dimensions'] ?? null) ? array_values($data['dimensions']) : [],
            ),
            presentation: isset($data['presentation']) && is_array($data['presentation'])
                ? PresentationHints::fromArray($data['presentation'])
                : null,
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'kind' => $this->kind(),
            'category_field' => $this->categoryField,
            'dimensions' => $this->dimensions,
            'series' => $this->series,
            'rows' => $this->rows,
            'presentation' => $this->presentation,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
