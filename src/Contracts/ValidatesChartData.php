<?php

declare(strict_types=1);

namespace Elqora\Chart\Contracts;

use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Validation\ValidationResult;

interface ValidatesChartData
{
    public function validate(Chart $chart): ValidationResult;
}
