<?php

declare(strict_types=1);

namespace Elqora\Chart\Enums;

enum ChartType: string
{
    case LINE = 'line';
    case AREA = 'area';
    case BAR = 'bar';
    case STACKED_BAR = 'stacked_bar';
    case PIE = 'pie';
    case DOUGHNUT = 'doughnut';
    case SCATTER = 'scatter';
    case BUBBLE = 'bubble';
    case RADAR = 'radar';
    case GAUGE = 'gauge';
    case FUNNEL = 'funnel';
    case HEATMAP = 'heatmap';
    case TREEMAP = 'treemap';
    case SUNBURST = 'sunburst';
    case BOX_PLOT = 'box_plot';
    case CANDLESTICK = 'candlestick';

    public function family(): ChartFamily
    {
        return match ($this) {
            self::LINE, self::AREA, self::BAR, self::STACKED_BAR => ChartFamily::CARTESIAN,
            self::PIE, self::DOUGHNUT => ChartFamily::CATEGORICAL,
            self::SCATTER, self::BUBBLE => ChartFamily::COORDINATE,
            self::RADAR, self::GAUGE => ChartFamily::RADIAL,
            self::FUNNEL => ChartFamily::FLOW,
            self::HEATMAP => ChartFamily::MATRIX,
            self::TREEMAP, self::SUNBURST => ChartFamily::HIERARCHICAL,
            self::BOX_PLOT => ChartFamily::STATISTICAL,
            self::CANDLESTICK => ChartFamily::FINANCIAL,
        };
    }
}
