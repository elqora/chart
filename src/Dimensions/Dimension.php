<?php

declare(strict_types=1);

namespace Elqora\Chart\Dimensions;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Enums\DimensionRole;
use Elqora\Chart\Enums\ValueType;
use Elqora\Chart\Support\SerializableValue;

final readonly class Dimension implements ArraySerializable
{
    /**
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $field,
        public DimensionRole $role = DimensionRole::CATEGORY,
        public ValueType $type = ValueType::CATEGORY,
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
            role: DimensionRole::from((string) ($data['role'] ?? DimensionRole::CATEGORY->value)),
            type: ValueType::from((string) ($data['type'] ?? ValueType::CATEGORY->value)),
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'key' => $this->key,
            'label' => $this->label,
            'field' => $this->field,
            'role' => $this->role,
            'type' => $this->type,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
