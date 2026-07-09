<?php

declare(strict_types=1);

namespace Elqora\Chart\Enums;

enum DimensionRole: string
{
    case CATEGORY = 'category';
    case X = 'x';
    case Y = 'y';
    case GROUP = 'group';
    case TIME = 'time';
    case SERIES = 'series';
}
