<?php

declare(strict_types=1);

namespace Elqora\Chart\Enums;

enum ChartFamily: string
{
    case CARTESIAN = 'cartesian';
    case CATEGORICAL = 'categorical';
    case RADIAL = 'radial';
    case COORDINATE = 'coordinate';
    case HIERARCHICAL = 'hierarchical';
    case MATRIX = 'matrix';
    case STATISTICAL = 'statistical';
    case FINANCIAL = 'financial';
    case FLOW = 'flow';
    case NETWORK = 'network';
    case SPECIALIZED = 'specialized';
    case CUSTOM = 'custom';
}
