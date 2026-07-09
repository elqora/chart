<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Formatting\ValueFormat;
use Elqora\Chart\Support\SerializableValue;

final readonly class BoxPlotData implements ChartData
{
    /**
     * @param list<BoxPlotItem> $items
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public array $items,
        public ?ValueFormat $format = null,
        public array $meta = [],
    ) {
    }

    public function kind(): string
    {
        return 'box_plot';
    }

    public function family(): ChartFamily
    {
        return ChartFamily::STATISTICAL;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            items: array_map(
                static fn (array $item): BoxPlotItem => BoxPlotItem::fromArray($item),
                is_array($data['items'] ?? null) ? array_values($data['items']) : [],
            ),
            format: isset($data['format']) && is_array($data['format']) ? ValueFormat::fromArray($data['format']) : null,
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'kind' => $this->kind(),
            'format' => $this->format,
            'items' => $this->items,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
