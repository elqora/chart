<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Enums\CurveType;
use Elqora\Chart\Enums\Orientation;
use Elqora\Chart\Enums\SparklineMode;
use Elqora\Chart\Enums\StackingMode;
use Elqora\Chart\Support\SerializableValue;

final readonly class PresentationHints implements ArraySerializable
{
    public function __construct(
        public ?Orientation $orientation = null,
        public StackingMode $stacking = StackingMode::NONE,
        public ?bool $connectNulls = null,
        public ?bool $cumulative = null,
        public ?string $orderBy = null,
        public ?SparklineMode $sparklineMode = null,
        public ?CurveType $curveType = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            orientation: isset($data['orientation']) ? Orientation::from((string) $data['orientation']) : null,
            stacking: StackingMode::from((string) ($data['stacking'] ?? StackingMode::NONE->value)),
            connectNulls: isset($data['connect_nulls']) ? (bool) $data['connect_nulls'] : null,
            cumulative: isset($data['cumulative']) ? (bool) $data['cumulative'] : null,
            orderBy: isset($data['order_by']) ? (string) $data['order_by'] : null,
            sparklineMode: isset($data['sparkline_mode']) ? SparklineMode::from((string) $data['sparkline_mode']) : null,
            curveType: isset($data['curve_type']) ? CurveType::from((string) $data['curve_type']) : null,
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'orientation' => $this->orientation,
            'stacking' => $this->stacking,
            'connect_nulls' => $this->connectNulls,
            'cumulative' => $this->cumulative,
            'order_by' => $this->orderBy,
            'sparkline_mode' => $this->sparklineMode,
            'curve_type' => $this->curveType,
        ]);
    }
}
