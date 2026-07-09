<?php

declare(strict_types=1);

namespace Elqora\Chart\Contracts;

use Elqora\Chart\Enums\ChartFamily;

interface ChartData extends ArraySerializable
{
    public function kind(): string;

    public function family(): ChartFamily;
}
