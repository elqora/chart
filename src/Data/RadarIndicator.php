<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Formatting\ValueFormat;
use Elqora\Chart\Support\SerializableValue;

final readonly class RadarIndicator implements ArraySerializable
{
    /**
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $key,
        public string $label,
        public ?float $minimum = null,
        public ?float $maximum = null,
        public ?ValueFormat $format = null,
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
            minimum: isset($data['minimum']) ? (float) $data['minimum'] : null,
            maximum: isset($data['maximum']) ? (float) $data['maximum'] : null,
            format: isset($data['format']) && is_array($data['format']) ? ValueFormat::fromArray($data['format']) : null,
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'key' => $this->key,
            'label' => $this->label,
            'minimum' => $this->minimum,
            'maximum' => $this->maximum,
            'format' => $this->format,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
