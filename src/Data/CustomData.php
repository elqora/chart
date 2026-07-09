<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Support\SerializableValue;

final readonly class CustomData implements ChartData
{
    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $customType,
        public ChartFamily $family,
        public array $payload,
        public array $meta = [],
    ) {
    }

    public function kind(): string
    {
        return 'custom';
    }

    public function family(): ChartFamily
    {
        return $this->family;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            customType: (string) ($data['custom_type'] ?? ''),
            family: ChartFamily::from((string) ($data['family'] ?? ChartFamily::CUSTOM->value)),
            payload: is_array($data['payload'] ?? null) ? $data['payload'] : [],
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'kind' => $this->kind(),
            'custom_type' => $this->customType,
            'family' => $this->family,
            'payload' => $this->payload,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
