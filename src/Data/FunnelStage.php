<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Support\SerializableValue;

final readonly class FunnelStage implements ArraySerializable
{
    /**
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $key,
        public string $label,
        public int|float $value,
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
            value: is_numeric($data['value'] ?? null) ? $data['value'] + 0 : 0,
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'key' => $this->key,
            'label' => $this->label,
            'value' => $this->value,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
