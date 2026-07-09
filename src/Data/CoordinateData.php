<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Formatting\ValueFormat;
use Elqora\Chart\Support\SerializableValue;

final readonly class CoordinateData implements ChartData
{
    /**
     * @param list<array<string, mixed>> $rows
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $xField,
        public string $yField,
        public array $rows,
        public ?string $sizeField = null,
        public ?string $labelField = null,
        public ?string $groupField = null,
        public ?ValueFormat $xFormat = null,
        public ?ValueFormat $yFormat = null,
        public ?ValueFormat $sizeFormat = null,
        public array $meta = [],
    ) {
    }

    public function kind(): string
    {
        return 'coordinate';
    }

    public function family(): ChartFamily
    {
        return ChartFamily::COORDINATE;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            xField: (string) ($data['x_field'] ?? ''),
            yField: (string) ($data['y_field'] ?? ''),
            rows: is_array($data['rows'] ?? null) ? array_values($data['rows']) : [],
            sizeField: isset($data['size_field']) ? (string) $data['size_field'] : null,
            labelField: isset($data['label_field']) ? (string) $data['label_field'] : null,
            groupField: isset($data['group_field']) ? (string) $data['group_field'] : null,
            xFormat: isset($data['x_format']) && is_array($data['x_format']) ? ValueFormat::fromArray($data['x_format']) : null,
            yFormat: isset($data['y_format']) && is_array($data['y_format']) ? ValueFormat::fromArray($data['y_format']) : null,
            sizeFormat: isset($data['size_format']) && is_array($data['size_format'])
                ? ValueFormat::fromArray($data['size_format'])
                : null,
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'kind' => $this->kind(),
            'x_field' => $this->xField,
            'y_field' => $this->yField,
            'size_field' => $this->sizeField,
            'label_field' => $this->labelField,
            'group_field' => $this->groupField,
            'x_format' => $this->xFormat,
            'y_format' => $this->yFormat,
            'size_format' => $this->sizeFormat,
            'rows' => $this->rows,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
