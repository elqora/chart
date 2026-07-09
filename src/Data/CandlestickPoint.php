<?php

declare(strict_types=1);

namespace Elqora\Chart\Data;

use Elqora\Chart\Contracts\ArraySerializable;
use Elqora\Chart\Support\SerializableValue;

final readonly class CandlestickPoint implements ArraySerializable
{
    /**
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $category,
        public int|float $open,
        public int|float $high,
        public int|float $low,
        public int|float $close,
        public int|float|null $volume = null,
        public array $meta = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            category: (string) ($data['category'] ?? ''),
            open: is_numeric($data['open'] ?? null) ? $data['open'] + 0 : 0,
            high: is_numeric($data['high'] ?? null) ? $data['high'] + 0 : 0,
            low: is_numeric($data['low'] ?? null) ? $data['low'] + 0 : 0,
            close: is_numeric($data['close'] ?? null) ? $data['close'] + 0 : 0,
            volume: is_numeric($data['volume'] ?? null) ? $data['volume'] + 0 : null,
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return SerializableValue::omitNull([
            'category' => $this->category,
            'open' => $this->open,
            'high' => $this->high,
            'low' => $this->low,
            'close' => $this->close,
            'volume' => $this->volume,
            'meta' => $this->meta === [] ? null : $this->meta,
        ]);
    }
}
