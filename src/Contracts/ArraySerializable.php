<?php

declare(strict_types=1);

namespace Elqora\Chart\Contracts;

interface ArraySerializable
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
