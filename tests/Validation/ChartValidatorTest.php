<?php

declare(strict_types=1);

namespace Elqora\Chart\Tests\Validation;

use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Data\BoxPlotData;
use Elqora\Chart\Data\BoxPlotItem;
use Elqora\Chart\Data\CandlestickData;
use Elqora\Chart\Data\CandlestickPoint;
use Elqora\Chart\Data\CategoryValueData;
use Elqora\Chart\Data\CoordinateData;
use Elqora\Chart\Data\CustomData;
use Elqora\Chart\Data\FunnelData;
use Elqora\Chart\Data\FunnelStage;
use Elqora\Chart\Data\GaugeData;
use Elqora\Chart\Data\HeatmapData;
use Elqora\Chart\Data\HierarchyData;
use Elqora\Chart\Data\RadarData;
use Elqora\Chart\Data\RadarIndicator;
use Elqora\Chart\Data\RadarSeries;
use Elqora\Chart\Data\TabularData;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Hierarchy\HierarchyNode;
use Elqora\Chart\Series\Series;
use PHPUnit\Framework\TestCase;

final class ChartValidatorTest extends TestCase
{
    public function testCoversEveryBuiltInChartTypeWithValidPayload(): void
    {
        $charts = [
            new Chart('line', ChartType::LINE, 'Line', $this->tabular()),
            new Chart('area', ChartType::AREA, 'Area', $this->tabular()),
            new Chart('bar', ChartType::BAR, 'Bar', $this->tabular()),
            new Chart('stacked-bar', ChartType::STACKED_BAR, 'Stacked bar', $this->tabular(stacked: true)),
            new Chart('pie', ChartType::PIE, 'Pie', $this->categoryValue()),
            new Chart('doughnut', ChartType::DOUGHNUT, 'Doughnut', $this->categoryValue()),
            new Chart('scatter', ChartType::SCATTER, 'Scatter', $this->coordinate()),
            new Chart('bubble', ChartType::BUBBLE, 'Bubble', $this->coordinate(size: true)),
            new Chart('radar', ChartType::RADAR, 'Radar', new RadarData([
                new RadarIndicator('speed', 'Speed', 0, 100),
                new RadarIndicator('quality', 'Quality', 0, 100),
            ], [
                new RadarSeries('current', 'Current', [80, 90]),
            ])),
            new Chart('gauge', ChartType::GAUGE, 'Gauge', new GaugeData(75, 0, 100)),
            new Chart('funnel', ChartType::FUNNEL, 'Funnel', new FunnelData([
                new FunnelStage('seen', 'Seen', 100),
                new FunnelStage('clicked', 'Clicked', 40),
            ])),
            new Chart('heatmap', ChartType::HEATMAP, 'Heatmap', new HeatmapData('day', 'hour', 'value', [
                ['day' => 'Mon', 'hour' => '10:00', 'value' => 25],
            ])),
            new Chart('treemap', ChartType::TREEMAP, 'Treemap', $this->hierarchy()),
            new Chart('sunburst', ChartType::SUNBURST, 'Sunburst', $this->hierarchy()),
            new Chart('box', ChartType::BOX_PLOT, 'Box plot', new BoxPlotData([
                new BoxPlotItem('A', 1, 2, 3, 4, 5, [0, 6]),
            ])),
            new Chart('candle', ChartType::CANDLESTICK, 'Candlestick', new CandlestickData([
                new CandlestickPoint('2026-07-08', 100, 110, 90, 95),
            ])),
        ];

        foreach ($charts as $chart) {
            self::assertTrue($chart->validate()->isValid(), $chart->typeName());
        }
    }

    public function testCollectsCommonAndTabularValidationIssues(): void
    {
        $chart = new Chart(
            key: '',
            type: ChartType::LINE,
            title: '',
            data: new TabularData(
                categoryField: 'time',
                rows: [['delivered' => 'many']],
                series: [
                    new Series('delivered', 'Delivered', 'delivered'),
                    new Series('delivered', 'Delivered again', 'missing'),
                ],
            ),
        );

        self::assertSame([
            'chart.key.empty',
            'chart.title.empty',
            'series.key.duplicate',
            'data.field.missing',
            'series.value.not_numeric',
            'data.field.missing',
        ], $chart->validate()->codes());
    }

    public function testDetectsSpecializedInvalidPayloads(): void
    {
        $cases = [
            'bubble.size_field.missing' => new Chart('bubble', ChartType::BUBBLE, 'Bubble', $this->coordinate()),
            'heatmap.value.not_numeric' => new Chart('heat', ChartType::HEATMAP, 'Heat', new HeatmapData('x', 'y', 'value', [
                ['x' => 'A', 'y' => 'B', 'value' => 'hot'],
            ])),
            'radar.series.length_mismatch' => new Chart('radar', ChartType::RADAR, 'Radar', new RadarData([
                new RadarIndicator('a', 'A'),
                new RadarIndicator('b', 'B'),
            ], [
                new RadarSeries('s', 'S', [1]),
            ])),
            'gauge.value.above_maximum' => new Chart('gauge', ChartType::GAUGE, 'Gauge', new GaugeData(120, 0, 100)),
            'box_plot.order.invalid' => new Chart('box', ChartType::BOX_PLOT, 'Box', new BoxPlotData([
                new BoxPlotItem('A', 1, 5, 3, 4, 6),
            ])),
            'candlestick.bounds.invalid' => new Chart('candle', ChartType::CANDLESTICK, 'Candle', new CandlestickData([
                new CandlestickPoint('now', 10, 9, 11, 10),
            ])),
            'hierarchy.node.duplicate_key' => new Chart('tree', ChartType::TREEMAP, 'Tree', new HierarchyData([
                new HierarchyNode('same', 'Root', children: [
                    new HierarchyNode('same', 'Child'),
                ]),
            ])),
            'custom.payload.not_json_compatible' => new Chart('custom', 'timeline', 'Timeline', new CustomData(
                customType: 'timeline',
                family: ChartFamily::SPECIALIZED,
                payload: ['resource' => fopen('php://memory', 'r')],
            )),
        ];

        foreach ($cases as $code => $chart) {
            self::assertContains($code, $chart->validate()->codes(), $code);
        }
    }

    private function tabular(bool $stacked = false): TabularData
    {
        return new TabularData(
            categoryField: 'time',
            rows: [
                ['time' => '10:00', 'delivered' => 100, 'failed' => 2],
            ],
            series: [
                new Series('delivered', 'Delivered', 'delivered', stack: $stacked ? 'messages' : null),
                new Series('failed', 'Failed', 'failed', stack: $stacked ? 'messages' : null),
            ],
        );
    }

    private function categoryValue(): CategoryValueData
    {
        return new CategoryValueData('status', 'count', [
            ['status' => 'completed', 'count' => 83],
            ['status' => 'failed', 'count' => 9],
        ]);
    }

    private function coordinate(bool $size = false): CoordinateData
    {
        return new CoordinateData(
            xField: 'quantity',
            yField: 'duration',
            rows: [
                ['quantity' => 100, 'duration' => 12, 'size' => 5],
            ],
            sizeField: $size ? 'size' : null,
        );
    }

    private function hierarchy(): HierarchyData
    {
        return new HierarchyData([
            new HierarchyNode('root', 'Root', children: [
                new HierarchyNode('child', 'Child', 10),
            ]),
        ]);
    }
}
