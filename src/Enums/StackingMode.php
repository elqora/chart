<?php

declare(strict_types=1);

namespace Elqora\Chart\Enums;

enum StackingMode: string
{
    case NONE = 'none';
    case STACKED = 'stacked';
    case NORMALIZED = 'normalized';
}
