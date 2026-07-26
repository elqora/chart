<?php

declare(strict_types=1);

namespace Elqora\Chart\Enums;

enum CurveType: string
{
    case MONOTONE = 'monotone';
    case LINEAR = 'linear';
    case STEP = 'step';
    case SMOOTH = 'smooth';
}
