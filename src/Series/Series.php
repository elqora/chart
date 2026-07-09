<?php

declare(strict_types=1);

namespace Elqora\Chart\Series;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Enums\ValueType;
use Elqora\Chart\Formatting\ValueFormat;
use Elqora\Chart\Support\SerializableValue;

final readonly class Series implements ArraySerializable
{
    /**
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $field,
        public ValueType $valueType = ValueType::NUMBER,
        public ?ValueFormat $format = null,
        public ?string $group = null,
        public ?string $stack = null,
        public ?ChartType $chartType = null,
        public array $meta = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            key: (string) ($data['key'] ?? ''),
            label: (string) ($data['label'] ?? ''),
            field: (string) ($data['field'] ?? ''),
            valueType: ValueType::from((string) ($data['value_type'] ?? ValueType::NUMBER->value)),
            format: isset($data['format']) && is_array($data['format']) ? ValueFormat::fromArray($data['format']) : null,
            group: isset($data['group']) ? (string) $data['group'] : null,
            stack: isset($data['stack']) ? (string) $data['stack'] : null,
            chartType: isset($data['chart_type']) ? ChartType::from((string) $data['chart_type']) : null,
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'key' => $this->key,
            'label' => $this->label,
            'field' => $this->field,
            'value_type' => $this->valueType,
            'format' => $this->format,
            'group' => $this->group,
            'stack' => $this->stack,
            'chart_type' => $this->chartType,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
