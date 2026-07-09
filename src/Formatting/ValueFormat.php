<?php

declare(strict_types=1);

namespace Elqora\Chart\Formatting;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Enums\PercentageConvention;
use Elqora\Chart\Enums\ValueType;
use Elqora\Chart\Support\SerializableValue;

final readonly class ValueFormat implements ArraySerializable
{
    public function __construct(
        public ValueType $type = ValueType::NUMBER,
        public ?string $unit = null,
        public ?string $prefix = null,
        public ?string $suffix = null,
        public ?string $currency = null,
        public ?string $durationUnit = null,
        public ?int $precision = null,
        public PercentageConvention $percentageConvention = PercentageConvention::WHOLE_NUMBER,
    ) {
    }

    public static function percentage(
        PercentageConvention $convention = PercentageConvention::WHOLE_NUMBER,
        ?int $precision = null,
    ): self {
        return new self(
            type: ValueType::PERCENTAGE,
            unit: 'percent',
            precision: $precision,
            percentageConvention: $convention,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: ValueType::from((string) ($data['type'] ?? ValueType::NUMBER->value)),
            unit: isset($data['unit']) ? (string) $data['unit'] : null,
            prefix: isset($data['prefix']) ? (string) $data['prefix'] : null,
            suffix: isset($data['suffix']) ? (string) $data['suffix'] : null,
            currency: isset($data['currency']) ? (string) $data['currency'] : null,
            durationUnit: isset($data['duration_unit']) ? (string) $data['duration_unit'] : null,
            precision: isset($data['precision']) ? (int) $data['precision'] : null,
            percentageConvention: PercentageConvention::from(
                (string) ($data['percentage_convention'] ?? PercentageConvention::WHOLE_NUMBER->value),
            ),
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'type' => $this->type,
            'unit' => $this->unit,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'currency' => $this->currency,
            'duration_unit' => $this->durationUnit,
            'precision' => $this->precision,
            'percentage_convention' => $this->percentageConvention,
        ]);
    }
}
