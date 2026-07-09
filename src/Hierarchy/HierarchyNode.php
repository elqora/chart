<?php

declare(strict_types=1);

namespace Elqora\Chart\Hierarchy;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Support\SerializableValue;

final readonly class HierarchyNode implements ArraySerializable
{
    /**
     * @param list<HierarchyNode> $children
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $key,
        public string $label,
        public int|float|null $value = null,
        public array $children = [],
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
            value: is_numeric($data['value'] ?? null) ? $data['value'] + 0 : null,
            children: array_map(
                static fn (array $child): self => self::fromArray($child),
                is_array($data['children'] ?? null) ? array_values($data['children']) : [],
            ),
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'key' => $this->key,
            'label' => $this->label,
            'value' => $this->value,
            'children' => $this->children === [] ? null : $this->children,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
