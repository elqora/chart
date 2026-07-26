<?php

declare(strict_types=1);

namespace Elqora\Chart\Tests\Unit;

use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Data\CategoryValueData;
use Elqora\Chart\Data\HierarchyData;
use Elqora\Chart\Data\PresentationHints;
use Elqora\Chart\Data\TabularData;
use Elqora\Chart\Dimensions\Dimension;
use Elqora\Chart\Enums\ChartFamily;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Enums\CurveType;
use Elqora\Chart\Enums\DimensionRole;
use Elqora\Chart\Enums\Orientation;
use Elqora\Chart\Enums\PercentageConvention;
use Elqora\Chart\Enums\StackingMode;
use Elqora\Chart\Enums\ValueType;
use Elqora\Chart\Formatting\ValueFormat;
use Elqora\Chart\Hierarchy\HierarchyNode;
use Elqora\Chart\Series\Series;
use PHPUnit\Framework\TestCase;

final class ChartModelTest extends TestCase
{
    public function testConstructsAndSerializesLineChartDeterministically(): void
    {
        $chart = new Chart(
            key: 'delivery.throughput',
            type: ChartType::LINE,
            title: 'Delivery throughput',
            description: 'Message delivery by half-hour interval.',
            data: new TabularData(
                categoryField: 'time',
                rows: [
                    ['time' => '10:00', 'delivered' => 0, 'failed' => 0],
                    ['time' => '10:30', 'delivered' => 500, 'failed' => 12],
                ],
                series: [
                    new Series('delivered', 'Delivered', 'delivered', ValueType::INTEGER),
                    new Series('failed', 'Failed', 'failed', ValueType::INTEGER),
                ],
                dimensions: [
                    new Dimension('time', 'Time', 'time', DimensionRole::TIME, ValueType::STRING),
                ],
                presentation: new PresentationHints(
                    orientation: Orientation::VERTICAL,
                    stacking: StackingMode::NONE,
                    connectNulls: false,
                ),
                meta: ['source' => 'unit-test'],
            ),
        );

        self::assertSame([
            'key',
            'type',
            'family',
            'title',
            'description',
            'data',
        ], array_keys($chart->toArray()));

        self::assertSame('line', $chart->toArray()['type']);
        self::assertSame('cartesian', $chart->toArray()['family']);
        self::assertSame('tabular', $chart->toArray()['data']['kind']);
        self::assertArrayNotHasKey('meta', $chart->toArray());
        self::assertArrayNotHasKey('unused', $chart->toArray()['data']);
        self::assertJsonStringEqualsJsonString(
            json_encode($chart->toArray(), JSON_THROW_ON_ERROR),
            json_encode($chart, JSON_THROW_ON_ERROR),
        );
        self::assertTrue($chart->validate()->isValid());
    }

    public function testValueFormatDocumentsPercentageConvention(): void
    {
        $format = ValueFormat::percentage(PercentageConvention::FRACTION, precision: 2);

        self::assertSame([
            'type' => 'percentage',
            'unit' => 'percent',
            'precision' => 2,
            'percentage_convention' => 'fraction',
        ], $format->toArray());
    }

    public function testHierarchyDataRepresentsNestedCharts(): void
    {
        $chart = new Chart(
            key: 'service.mix',
            type: ChartType::TREEMAP,
            title: 'Service mix',
            data: new HierarchyData([
                new HierarchyNode(
                    key: 'root',
                    label: 'All services',
                    children: [
                        new HierarchyNode('email', 'Email', 120),
                        new HierarchyNode('sms', 'SMS', 75),
                    ],
                ),
            ]),
        );

        self::assertTrue($chart->validate()->isValid());
        self::assertSame('hierarchy', $chart->toArray()['data']['kind']);
        self::assertSame('Email', $chart->toArray()['data']['roots'][0]['children'][0]['label']);
    }

    public function testCategoryValueChartRejectsNegativeValues(): void
    {
        $chart = new Chart(
            key: 'status.breakdown',
            type: ChartType::PIE,
            title: 'Status breakdown',
            data: new CategoryValueData(
                categoryField: 'status',
                valueField: 'count',
                rows: [
                    ['status' => 'completed', 'count' => 83],
                    ['status' => 'failed', 'count' => -1],
                ],
            ),
        );

        self::assertContains('category_value.value.negative', $chart->validate()->codes());
    }

    public function testPresentationHintsSupportsCurveType(): void
    {
        $hints = new PresentationHints(curveType: CurveType::LINEAR);
        $array = $hints->toArray();
        self::assertSame('linear', $array['curve_type']);

        $restored = PresentationHints::fromArray($array);
        self::assertSame(CurveType::LINEAR, $restored->curveType);
    }
}
