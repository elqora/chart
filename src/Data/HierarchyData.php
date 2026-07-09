<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Hierarchy\HierarchyNode;
use Elqora\Chart\Support\SerializableValue;

final readonly class HierarchyData implements ChartData
{
    /**
     * @param list<HierarchyNode> $roots
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public array $roots,
        public array $meta = [],
    ) {
    }

    public function kind(): string
    {
        return 'hierarchy';
    }

    public function family(): ChartFamily
    {
        return ChartFamily::HIERARCHICAL;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            roots: array_map(
                static fn (array $node): HierarchyNode => HierarchyNode::fromArray($node),
                is_array($data['roots'] ?? null) ? array_values($data['roots']) : [],
            ),
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'kind' => $this->kind(),
            'roots' => $this->roots,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
