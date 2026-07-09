<?php

declare(strict_types=1);

namespace Elqora\Chart\Tests\Serialization;

use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Data\CandlestickData;
use Elqora\Chart\Data\CandlestickPoint;
use Elqora\Chart\Data\CustomData;
use Elqora\Chart\Data\HeatmapData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Exceptions\HydrationException;
use PHPUnit\Framework\TestCase;

final class HydrationTest extends TestCase
{
    public function testRoundTripsBuiltInChartFromSerializedArray(): void
    {
        $chart = new Chart(
            key: 'market.ohlc',
            type: ChartType::CANDLESTICK,
            title: 'Market OHLC',
            data: new CandlestickData([
                new CandlestickPoint('2026-07-08', 100, 110, 95, 105, 1000),
            ]),
            meta: ['source' => 'fixture'],
        );

        $hydrated = Chart::fromArray($chart->toArray());

        self::assertEquals($chart->toArray(), $hydrated->toArray());
        self::assertTrue($hydrated->validate()->isValid());
    }

    public function testRoundTripsCustomChartWithExplicitUnsupportedTypeDetection(): void
    {
        $chart = new Chart(
            key: 'custom.timeline',
            type: 'timeline',
            title: 'Timeline',
            data: new CustomData(
                customType: 'timeline',
                family: ChartFamily::SPECIALIZED,
                payload: ['events' => [['label' => 'Started', 'at' => '2026-07-08']]],
            ),
        );

        $hydrated = Chart::fromArray($chart->toArray());

        self::assertFalse($hydrated->isBuiltInType());
        self::assertTrue($hydrated->validate()->isValid());
        self::assertSame('timeline', $hydrated->typeName());
    }

    public function testRejectsUnknownDataKindDuringHydration(): void
    {
        $this->expectException(HydrationException::class);

        Chart::fromArray([
            'key' => 'bad',
            'type' => 'line',
            'title' => 'Bad',
            'data' => ['kind' => 'renderer_options'],
        ]);
    }

    public function testHydratesHeatmapPayload(): void
    {
        $chart = Chart::fromArray([
            'key' => 'heatmap.load',
            'type' => 'heatmap',
            'title' => 'Load by day and hour',
            'data' => [
                'kind' => 'heatmap',
                'x_field' => 'day',
                'y_field' => 'hour',
                'value_field' => 'load',
                'rows' => [
                    ['day' => 'Mon', 'hour' => '10:00', 'load' => 42],
                ],
            ],
        ]);

        self::assertInstanceOf(HeatmapData::class, $chart->data);
        self::assertTrue($chart->validate()->isValid());
    }
}
