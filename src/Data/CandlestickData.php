<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Formatting\ValueFormat;
use Elqora\Chart\Support\SerializableValue;

final readonly class CandlestickData implements ChartData
{
    /**
     * @param list<CandlestickPoint> $points
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public array $points,
        public ?ValueFormat $format = null,
        public array $meta = [],
    ) {
    }

    public function kind(): string
    {
        return 'candlestick';
    }

    public function family(): ChartFamily
    {
        return ChartFamily::FINANCIAL;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            points: array_map(
                static fn (array $point): CandlestickPoint => CandlestickPoint::fromArray($point),
                is_array($data['points'] ?? null) ? array_values($data['points']) : [],
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
            'points' => $this->points,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
