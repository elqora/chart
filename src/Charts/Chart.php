<?php

declare(strict_types=1);

namespace Elqora\Chart\Charts;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Hydration\ChartHydrator;
use Elqora\Chart\Support\SerializableValue;
use Elqora\Chart\Validation\ChartValidator;
use Elqora\Chart\Validation\ValidationResult;
use JsonSerializable;

final readonly class Chart implements ArraySerializable, JsonSerializable
{
    /**
     * @param ChartType|string $type
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $key,
        public ChartType|string $type,
        public string $title,
        public ChartData $data,
        public ?string $description = null,
        public array $meta = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return ChartHydrator::hydrate($data);
    }

    public function typeName(): string
    {
        return $this->type instanceof ChartType ? $this->type->value : $this->type;
    }

    public function isBuiltInType(): bool
    {
        return $this->type instanceof ChartType;
    }

    public function validate(?ChartValidator $validator = null): ValidationResult
    {
        return ($validator ?? new ChartValidator())->validate($this);
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'key' => $this->key,
            'type' => $this->typeName(),
            'family' => $this->data->family(),
            'title' => $this->title,
            'description' => $this->description,
            'data' => $this->data,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
