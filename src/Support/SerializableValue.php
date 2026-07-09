<?php

declare(strict_types=1);

namespace Elqora\Chart\Support;

use BackedEnum;
use Elqora\Chart\Contracts\ArraySerializable;

final class SerializableValue
{
    public static function normalize(mixed $value): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if ($value instanceof ArraySerializable) {
            return $value->toArray();
        }

        if (is_array($value)) {
            $normalized = [];

            foreach ($value as $key => $item) {
                $normalized[$key] = self::normalize($item);
            }

            return $normalized;
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return array<string, mixed>
     */
    public static function omitNull(array $values): array
    {
        $serialized = [];

        foreach ($values as $key => $value) {
            if ($value === null) {
                continue;
            }

            $serialized[$key] = self::normalize($value);
        }

        return $serialized;
    }

    public static function isJsonCompatible(mixed $value): bool
    {
        if ($value === null || is_scalar($value)) {
            return true;
        }

        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $key => $item) {
            if (! is_int($key) && ! is_string($key)) {
                return false;
            }

            if (! self::isJsonCompatible($item)) {
                return false;
            }
        }

        return true;
    }
}
