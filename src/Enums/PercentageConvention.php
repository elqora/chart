<?php

declare(strict_types=1);

namespace Elqora\Chart\Enums;

enum PercentageConvention: string
{
    case WHOLE_NUMBER = 'whole_number';
    case FRACTION = 'fraction';
}
