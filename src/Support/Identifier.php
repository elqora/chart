<?php

declare(strict_types=1);

namespace Elqora\Chart\Support;

final class Identifier
{
    public static function isStable(string $value): bool
    {
        return preg_match('/^[A-Za-z0-9][A-Za-z0-9_.:-]*$/', $value) === 1;
    }
}
