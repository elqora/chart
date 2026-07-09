<?php

declare(strict_types=1);

namespace Elqora\Chart\Enums;

enum ValueType: string
{
    case STRING = 'string';
    case CATEGORY = 'category';
    case INTEGER = 'integer';
    case NUMBER = 'number';
    case PERCENTAGE = 'percentage';
    case CURRENCY = 'currency';
    case DURATION = 'duration';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case BOOLEAN = 'boolean';
}
