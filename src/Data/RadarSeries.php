<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Support\SerializableValue;

final readonly class RadarSeries implements ArraySerializable
{
    /**
     * @param list<int|float|null> $values
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $key,
        public string $label,
        public array $values,
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
            values: is_array($data['values'] ?? null) ? array_values($data['values']) : [],
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'key' => $this->key,
            'label' => $this->label,
            'values' => $this->values,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
