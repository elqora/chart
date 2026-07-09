<?php

declare(strict_types=1);

namespace Elqora\Chart\Hydration;

use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Contracts\ChartData;
use Elqora\Chart\Data\BoxPlotData;
use Elqora\Chart\Data\CandlestickData;
use Elqora\Chart\Data\CategoryValueData;
use Elqora\Chart\Data\CoordinateData;
use Elqora\Chart\Data\CustomData;
use Elqora\Chart\Data\FunnelData;
use Elqora\Chart\Data\GaugeData;
use Elqora\Chart\Data\HeatmapData;
use Elqora\Chart\Data\HierarchyData;
use Elqora\Chart\Data\RadarData;
use Elqora\Chart\Data\TabularData;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Exceptions\HydrationException;

final class ChartHydrator
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function hydrate(array $payload): Chart
    {
        if (! is_array($payload['data'] ?? null)) {
            throw new HydrationException('Chart data must be an array payload.');
        }

        $typeName = (string) ($payload['type'] ?? '');
        $type = ChartType::tryFrom($typeName) ?? $typeName;

        return new Chart(
            key: (string) ($payload['key'] ?? ''),
            type: $type,
            title: (string) ($payload['title'] ?? ''),
            data: self::hydrateData($payload['data']),
            description: isset($payload['description']) ? (string) $payload['description'] : null,
            meta: is_array($payload['meta'] ?? null) ? $payload['meta'] : [],
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function hydrateData(array $data): ChartData
    {
        return match ((string) ($data['kind'] ?? '')) {
            'tabular' => TabularData::fromArray($data),
            'category_value' => CategoryValueData::fromArray($data),
            'coordinate' => CoordinateData::fromArray($data),
            'heatmap' => HeatmapData::fromArray($data),
            'radar' => RadarData::fromArray($data),
            'gauge' => GaugeData::fromArray($data),
            'funnel' => FunnelData::fromArray($data),
            'hierarchy' => HierarchyData::fromArray($data),
            'box_plot' => BoxPlotData::fromArray($data),
            'candlestick' => CandlestickData::fromArray($data),
            'custom' => CustomData::fromArray($data),
            default => throw new HydrationException('Unsupported chart data kind.'),
        };
    }
}
