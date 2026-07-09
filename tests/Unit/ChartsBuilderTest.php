<?php

declare(strict_types=1);

namespace Elqora\Chart\Tests\Unit;

use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Charts\Charts;
use Elqora\Chart\Data\CategoryValueData;
use Elqora\Chart\Data\CoordinateData;
use Elqora\Chart\Data\PresentationHints;
use Elqora\Chart\Data\TabularData;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Enums\Orientation;
use Elqora\Chart\Enums\ValueType;
use Elqora\Chart\Series\Series;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ChartsBuilderTest extends TestCase
{
    /**
     * @return iterable<string, array{Chart, ChartType}>
     */
    public static function builderCharts(): iterable
    {
        $rows = [
            ['time' => '10:00', 'delivered' => 0],
            ['time' => '10:30', 'delivered' => 500],
        ];
        $series = [
            new Series('delivered', 'Delivered', 'delivered', ValueType::INTEGER),
        ];

        yield 'line' => [Charts::line('delivery.line', 'Delivery line', 'time', $rows, $series), ChartType::LINE];
        yield 'area' => [Charts::area('delivery.area', 'Delivery area', 'time', $rows, $series), ChartType::AREA];
        yield 'bar' => [Charts::bar('delivery.bar', 'Delivery bar', 'time', $rows, $series), ChartType::BAR];
        yield 'pie' => [
            Charts::pie('orders.pie', 'Orders pie', 'status', 'count', [
                ['status' => 'completed', 'count' => 83],
                ['status' => 'failed', 'count' => 9],
            ]),
            ChartType::PIE,
        ];
        yield 'doughnut' => [
            Charts::doughnut('orders.doughnut', 'Orders doughnut', 'status', 'count', [
                ['status' => 'completed', 'count' => 83],
                ['status' => 'failed', 'count' => 9],
            ]),
            ChartType::DOUGHNUT,
        ];
        yield 'scatter' => [
            Charts::scatter('duration.scatter', 'Duration scatter', 'quantity', 'duration', [
                ['quantity' => 100, 'duration' => 12],
                ['quantity' => 500, 'duration' => 42],
            ]),
            ChartType::SCATTER,
        ];
    }

    #[DataProvider('builderCharts')]
    public function testBuilderReturnsCanonicalChartWithExpectedType(Chart $chart, ChartType $type): void
    {
        self::assertInstanceOf(Chart::class, $chart);
        self::assertSame($type, $chart->type);
        self::assertTrue($chart->validate()->isValid());
    }

    public function testTabularBuildersSerializeLikeManualConstruction(): void
    {
        $rows = [
            ['time' => '10:00', 'delivered' => 0],
            ['time' => '10:30', 'delivered' => 500],
        ];
        $series = [
            new Series('delivered', 'Delivered', 'delivered', ValueType::INTEGER),
        ];
        $presentation = new PresentationHints(orientation: Orientation::VERTICAL, connectNulls: false);

        $built = Charts::line(
            key: 'delivery.throughput',
            title: 'Delivery throughput',
            category: 'time',
            rows: $rows,
            series: $series,
            presentation: $presentation,
            description: 'Message delivery by half-hour interval.',
            meta: ['source' => 'builder-test'],
        );

        $manual = new Chart(
            key: 'delivery.throughput',
            type: ChartType::LINE,
            title: 'Delivery throughput',
            data: new TabularData(
                categoryField: 'time',
                rows: $rows,
                series: $series,
                presentation: $presentation,
            ),
            description: 'Message delivery by half-hour interval.',
            meta: ['source' => 'builder-test'],
        );

        self::assertSame($manual->toArray(), $built->toArray());
        self::assertSame('builder-test', $built->toArray()['meta']['source']);
        self::assertSame('vertical', $built->toArray()['data']['presentation']['orientation']);
        self::assertFalse($built->toArray()['data']['presentation']['connect_nulls']);
    }

    public function testCategoryBuildersSerializeLikeManualConstruction(): void
    {
        $rows = [
            ['status' => 'completed', 'count' => 83],
            ['status' => 'failed', 'count' => 9],
        ];

        $built = Charts::pie(
            key: 'orders.status',
            title: 'Orders by status',
            category: 'status',
            value: 'count',
            rows: $rows,
            description: 'Order status totals.',
            meta: ['source' => 'builder-test'],
        );

        $manual = new Chart(
            key: 'orders.status',
            type: ChartType::PIE,
            title: 'Orders by status',
            data: new CategoryValueData(
                categoryField: 'status',
                valueField: 'count',
                rows: $rows,
            ),
            description: 'Order status totals.',
            meta: ['source' => 'builder-test'],
        );

        self::assertSame($manual->toArray(), $built->toArray());
    }

    public function testScatterBuilderSerializesLikeManualConstructionAndPreservesGroupField(): void
    {
        $rows = [
            ['quantity' => 100, 'duration' => 12, 'service' => 'email'],
            ['quantity' => 500, 'duration' => 42, 'service' => 'sms'],
        ];

        $built = Charts::scatter(
            key: 'duration.by-quantity',
            title: 'Duration by quantity',
            x: 'quantity',
            y: 'duration',
            rows: $rows,
            group: 'service',
            description: 'Duration grouped by service.',
            meta: ['source' => 'builder-test'],
        );

        $manual = new Chart(
            key: 'duration.by-quantity',
            type: ChartType::SCATTER,
            title: 'Duration by quantity',
            data: new CoordinateData(
                xField: 'quantity',
                yField: 'duration',
                rows: $rows,
                groupField: 'service',
            ),
            description: 'Duration grouped by service.',
            meta: ['source' => 'builder-test'],
        );

        self::assertSame($manual->toArray(), $built->toArray());
        self::assertSame('service', $built->toArray()['data']['group_field']);
    }

    public function testInvalidBuilderChartReportsThroughExistingValidation(): void
    {
        $chart = Charts::pie(
            key: 'orders.status',
            title: 'Orders by status',
            category: 'status',
            value: 'count',
            rows: [
                ['status' => 'completed', 'count' => 83],
                ['status' => 'failed', 'count' => -1],
            ],
        );

        self::assertContains('category_value.value.negative', $chart->validate()->codes());
    }

    public function testManualApiRemainsUnchanged(): void
    {
        $chart = new Chart(
            key: 'delivery.throughput',
            type: ChartType::LINE,
            title: 'Delivery throughput',
            data: new TabularData(
                categoryField: 'time',
                rows: [
                    ['time' => '10:00', 'delivered' => 0],
                ],
                series: [
                    new Series(
                        key: 'delivered',
                        label: 'Delivered',
                        field: 'delivered',
                        valueType: ValueType::INTEGER,
                    ),
                ],
            ),
        );

        self::assertTrue($chart->validate()->isValid());
        self::assertSame('line', $chart->toArray()['type']);
    }
}
