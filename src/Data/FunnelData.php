<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Formatting\ValueFormat;
use Elqora\Chart\Support\SerializableValue;

final readonly class FunnelData implements ChartData
{
    /**
     * @param list<FunnelStage> $stages
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public array $stages,
        public ?ValueFormat $format = null,
        public array $meta = [],
    ) {
    }

    public function kind(): string
    {
        return 'funnel';
    }

    public function family(): ChartFamily
    {
        return ChartFamily::FLOW;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            stages: array_map(
                static fn (array $stage): FunnelStage => FunnelStage::fromArray($stage),
                is_array($data['stages'] ?? null) ? array_values($data['stages']) : [],
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
            'stages' => $this->stages,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
