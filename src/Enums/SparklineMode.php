<?php

declare(strict_types=1);

namespace Elqora\Chart\Enums;

enum SparklineMode: string
{
    case LINE = 'line';
    case AREA = 'area';
    case BAR = 'bar';
}
