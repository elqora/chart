<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Support\SerializableValue;

final readonly class RadarData implements ChartData
{
    /**
     * @param list<RadarIndicator> $indicators
     * @param list<RadarSeries> $series
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public array $indicators,
        public array $series,
        public array $meta = [],
    ) {
    }

    public function kind(): string
    {
        return 'radar';
    }

    public function family(): ChartFamily
    {
        return ChartFamily::RADIAL;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            indicators: array_map(
                static fn (array $indicator): RadarIndicator => RadarIndicator::fromArray($indicator),
                is_array($data['indicators'] ?? null) ? array_values($data['indicators']) : [],
            ),
            series: array_map(
                static fn (array $series): RadarSeries => RadarSeries::fromArray($series),
                is_array($data['series'] ?? null) ? array_values($data['series']) : [],
            ),
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'kind' => $this->kind(),
            'indicators' => $this->indicators,
            'series' => $this->series,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
