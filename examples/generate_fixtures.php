<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Charts\Charts;
use Elqora\Chart\Data\BoxPlotData;
use Elqora\Chart\Data\BoxPlotItem;
use Elqora\Chart\Data\CandlestickData;
use Elqora\Chart\Data\CandlestickPoint;
use Elqora\Chart\Data\CategoryValueData;
use Elqora\Chart\Data\CoordinateData;
use Elqora\Chart\Data\FunnelData;
use Elqora\Chart\Data\FunnelStage;
use Elqora\Chart\Data\GaugeData;
use Elqora\Chart\Data\HeatmapData;
use Elqora\Chart\Data\HierarchyData;
use Elqora\Chart\Data\PresentationHints;
use Elqora\Chart\Data\RadarData;
use Elqora\Chart\Data\RadarIndicator;
use Elqora\Chart\Data\RadarSeries;
use Elqora\Chart\Data\TabularData;
use Elqora\Chart\Dimensions\Dimension;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Enums\DimensionRole;
use Elqora\Chart\Enums\CurveType;
use Elqora\Chart\Enums\Orientation;
use Elqora\Chart\Enums\SparklineMode;
use Elqora\Chart\Enums\StackingMode;
use Elqora\Chart\Enums\ValueType;
use Elqora\Chart\Hierarchy\HierarchyNode;
use Elqora\Chart\Series\Series;

$tabularRows = [
    ['time' => '10:00', 'delivered' => 200, 'failed' => 5],
    ['time' => '10:30', 'delivered' => 550, 'failed' => 12],
    ['time' => '11:00', 'delivered' => 1200, 'failed' => 25],
    ['time' => '11:30', 'delivered' => 890, 'failed' => 18],
    ['time' => '12:00', 'delivered' => 1400, 'failed' => 30],
];

$tabularSeries = [
    new Series('delivered', 'Delivered Units', 'delivered', ValueType::INTEGER),
    new Series('failed', 'Failed Delivery', 'failed', ValueType::INTEGER),
];

$charts = [
    'line' => Charts::line('line.throughput', 'Line Chart: System Throughput', 'time', $tabularRows, $tabularSeries, description: 'Message delivery over 30-min intervals.'),

    'area' => Charts::area('area.volume', 'Area Chart: Message Volume', 'time', $tabularRows, $tabularSeries, new PresentationHints(stacking: StackingMode::STACKED), description: 'Stacked area representation of throughput.'),

    'bar' => Charts::bar('bar.traffic', 'Bar Chart: Hourly Traffic', 'time', $tabularRows, $tabularSeries, description: 'Bar chart of delivered vs failed messages.'),

    'sparkline_line' => Charts::sparkline(
        'sparkline.revenue',
        'Sparkline (Line - Monotone): Revenue Trend',
        'time',
        [
            ['time' => '10:00', 'delivered' => 200],
            ['time' => '10:30', 'delivered' => 450],
            ['time' => '11:00', 'delivered' => 600],
            ['time' => '11:30', 'delivered' => 1100],
            ['time' => '12:00', 'delivered' => 950],
            ['time' => '12:30', 'delivered' => 1400],
        ],
        [new Series('delivered', 'Revenue', 'delivered', ValueType::CURRENCY)],
        mode: SparklineMode::LINE,
        description: 'Smooth monotonic curve connecting data points without sharp breaks.'
    ),

    'sparkline_linear' => Charts::sparkline(
        'sparkline.linear',
        'Sparkline (Line - Linear): Direct Metric',
        'time',
        [
            ['time' => '10:00', 'delivered' => 100],
            ['time' => '10:30', 'delivered' => 1200],
            ['time' => '11:00', 'delivered' => 250],
            ['time' => '11:30', 'delivered' => 1350],
            ['time' => '12:00', 'delivered' => 300],
            ['time' => '12:30', 'delivered' => 1400],
        ],
        [new Series('delivered', 'Metrics', 'delivered', ValueType::NUMBER)],
        mode: SparklineMode::LINE,
        presentation: new PresentationHints(curveType: CurveType::LINEAR),
        description: 'Sharp straight-line segments with distinct angular corners.'
    ),

    'sparkline_area' => Charts::sparkline(
        'sparkline.users',
        'Sparkline (Area): Active Users',
        'time',
        [
            ['time' => '10:00', 'delivered' => 300],
            ['time' => '10:30', 'delivered' => 650],
            ['time' => '11:00', 'delivered' => 500],
            ['time' => '11:30', 'delivered' => 980],
            ['time' => '12:00', 'delivered' => 850],
            ['time' => '12:30', 'delivered' => 1300],
        ],
        [new Series('delivered', 'Users', 'delivered', ValueType::INTEGER)],
        mode: SparklineMode::AREA,
        description: 'Filled color gradient area below the curve line.'
    ),

    'sparkline_bar' => Charts::sparkline(
        'sparkline.conversions',
        'Sparkline (Bar): Conversions',
        'time',
        [
            ['time' => '10:00', 'delivered' => 400],
            ['time' => '10:30', 'delivered' => 850],
            ['time' => '11:00', 'delivered' => 350],
            ['time' => '11:30', 'delivered' => 1100],
            ['time' => '12:00', 'delivered' => 650],
            ['time' => '12:30', 'delivered' => 1250],
        ],
        [new Series('delivered', 'Conversions', 'delivered', ValueType::INTEGER)],
        mode: SparklineMode::BAR,
        description: 'Compact vertical bar micro-chart.'
    ),

    'stacked_bar' => new Chart(
        key: 'bar.stacked',
        type: ChartType::STACKED_BAR,
        title: 'Stacked Bar: System Load',
        data: new TabularData(
            categoryField: 'time',
            rows: $tabularRows,
            series: $tabularSeries,
            presentation: new PresentationHints(stacking: StackingMode::STACKED),
        ),
        description: 'Normalized stacked bar visualization.'
    ),

    'pie' => Charts::pie('pie.status', 'Pie Chart: Delivery Status', 'status', 'count', [
        ['status' => 'Delivered', 'count' => 4240],
        ['status' => 'Failed', 'count' => 90],
        ['status' => 'Pending', 'count' => 310],
    ], description: 'Proportional distribution of delivery statuses.'),

    'doughnut' => Charts::doughnut('doughnut.status', 'Doughnut Chart: Status Mix', 'status', 'count', [
        ['status' => 'Completed', 'count' => 830],
        ['status' => 'Failed', 'count' => 90],
        ['status' => 'Canceled', 'count' => 80],
    ], description: 'Doughnut distribution layout.'),

    'scatter' => Charts::scatter('scatter.latency', 'Scatter Plot: Batch Latency', 'quantity', 'duration', [
        ['quantity' => 100, 'duration' => 12],
        ['quantity' => 250, 'duration' => 22],
        ['quantity' => 500, 'duration' => 45],
        ['quantity' => 800, 'duration' => 70],
        ['quantity' => 1200, 'duration' => 110],
    ], description: 'Scatter correlation between batch size and execution duration.'),

    'bubble' => new Chart(
        key: 'bubble.performance',
        type: ChartType::BUBBLE,
        title: 'Bubble Chart: Service Metrics',
        data: new CoordinateData(
            xField: 'requests',
            yField: 'latency',
            rows: [
                ['requests' => 100, 'latency' => 15, 'size' => 30],
                ['requests' => 400, 'latency' => 35, 'size' => 80],
                ['requests' => 900, 'latency' => 60, 'size' => 140],
            ],
            sizeField: 'size',
        ),
        description: 'Coordinate scatter with relative bubble size dimensions.'
    ),

    'radar' => new Chart(
        key: 'radar.quality',
        type: ChartType::RADAR,
        title: 'Radar Chart: Service Quality Indicators',
        data: new RadarData(
            indicators: [
                new RadarIndicator('speed', 'Response Speed', 100),
                new RadarIndicator('reliability', 'Uptime Reliability', 100),
                new RadarIndicator('security', 'Security Score', 100),
                new RadarIndicator('usability', 'Usability Index', 100),
            ],
            series: [
                new RadarSeries('gateway_a', 'API Gateway A', [90, 95, 85, 80]),
                new RadarSeries('gateway_b', 'API Gateway B', [70, 85, 95, 90]),
            ],
        ),
        description: 'Multi-axis indicator radar breakdown.'
    ),

    'gauge' => new Chart(
        key: 'gauge.cpu',
        type: ChartType::GAUGE,
        title: 'Gauge Chart: Cluster CPU Utilization',
        data: new GaugeData(
            value: 78.4,
            minimum: 0.0,
            maximum: 100.0,
        ),
        description: 'Real-time percentage gauge display.'
    ),

    'funnel' => new Chart(
        key: 'funnel.conversion',
        type: ChartType::FUNNEL,
        title: 'Funnel Chart: Checkout Conversion Flow',
        data: new FunnelData([
            new FunnelStage('impressions', 'Page Impressions', 10000),
            new FunnelStage('cart', 'Added to Cart', 3500),
            new FunnelStage('checkout', 'Initiated Checkout', 1200),
            new FunnelStage('purchase', 'Completed Purchase', 480),
        ]),
        description: 'Stage conversion pipeline.'
    ),

    'heatmap' => new Chart(
        key: 'heatmap.traffic',
        type: ChartType::HEATMAP,
        title: 'Heatmap Chart: Hourly Request Matrix',
        data: new HeatmapData(
            xField: 'day',
            yField: 'hour',
            valueField: 'requests',
            rows: [
                ['day' => 'Mon', 'hour' => '09:00', 'requests' => 120],
                ['day' => 'Mon', 'hour' => '12:00', 'requests' => 450],
                ['day' => 'Mon', 'hour' => '15:00', 'requests' => 310],
                ['day' => 'Tue', 'hour' => '09:00', 'requests' => 200],
                ['day' => 'Tue', 'hour' => '12:00', 'requests' => 520],
                ['day' => 'Tue', 'hour' => '15:00', 'requests' => 280],
            ],
        ),
        description: '2D Matrix heatmap representation.'
    ),

    'treemap' => new Chart(
        key: 'treemap.storage',
        type: ChartType::TREEMAP,
        title: 'Treemap Chart: Storage Breakdown',
        data: new HierarchyData([
            new HierarchyNode('root', 'All Storage', 500, [
                new HierarchyNode('db', 'Databases', 300, [
                    new HierarchyNode('pg', 'PostgreSQL', 200),
                    new HierarchyNode('redis', 'Redis Cache', 100),
                ]),
                new HierarchyNode('media', 'Media Assets', 200, [
                    new HierarchyNode('images', 'Images', 150),
                    new HierarchyNode('documents', 'PDFs', 50),
                ]),
            ]),
        ]),
        description: 'Proportional hierarchical treemap.'
    ),

    'sunburst' => new Chart(
        key: 'sunburst.org',
        type: ChartType::SUNBURST,
        title: 'Sunburst Chart: Organizational Hierarchy',
        data: new HierarchyData([
            new HierarchyNode('company', 'Elqora Global', 100, [
                new HierarchyNode('eng', 'Engineering', 60, [
                    new HierarchyNode('backend', 'Backend Systems', 35),
                    new HierarchyNode('frontend', 'Frontend UI', 25),
                ]),
                new HierarchyNode('product', 'Product & Design', 40),
            ]),
        ]),
        description: 'Multi-level radial sunburst view.'
    ),

    'box_plot' => new Chart(
        key: 'box_plot.response_time',
        type: ChartType::BOX_PLOT,
        title: 'Box Plot: Latency Distribution',
        data: new BoxPlotData([
            new BoxPlotItem('US-East', 12.0, 25.0, 42.0, 65.0, 95.0, [110.0]),
            new BoxPlotItem('EU-Central', 18.0, 32.0, 50.0, 78.0, 115.0, [140.0]),
            new BoxPlotItem('AP-South', 25.0, 45.0, 75.0, 110.0, 160.0),
        ]),
        description: 'Interquartile statistical distribution.'
    ),

    'candlestick' => new Chart(
        key: 'candlestick.stock',
        type: ChartType::CANDLESTICK,
        title: 'Candlestick Chart: Stock OHLC Pricing',
        data: new CandlestickData([
            new CandlestickPoint('Jul 21', 150.0, 158.5, 148.0, 155.2),
            new CandlestickPoint('Jul 22', 155.2, 162.0, 153.5, 160.0),
            new CandlestickPoint('Jul 23', 160.0, 161.5, 152.0, 153.8),
            new CandlestickPoint('Jul 24', 153.8, 159.0, 151.0, 158.4),
            new CandlestickPoint('Jul 25', 158.4, 165.0, 157.0, 164.2),
        ]),
        description: 'Financial Open-High-Low-Close price action.'
    ),
];

$output = [];
foreach ($charts as $typeKey => $chart) {
    $output[$typeKey] = $chart->toArray();
}

$targetPath = __DIR__ . '/react/src/fixtures/charts.json';
@mkdir(dirname($targetPath), 0777, true);
file_put_contents($targetPath, json_encode($output, JSON_PRETTY_PRINT));
echo "Successfully generated fixtures at " . $targetPath . "\n";
