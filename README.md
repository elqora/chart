# Elqora Chart

Elqora Chart is a framework-neutral PHP package for describing portable chart data. It provides immutable DTOs, backed enums, serialization, hydration, and structured validation for chart definitions that can be rendered by a host using any charting system.

It does not render charts, generate HTML, build JavaScript options, or depend on frontend or PHP frameworks.

## Installation

```bash
composer require elqora/chart
```

## Convenience builders

The `Charts` facade creates the same canonical `Chart` objects as manual construction.

## Minimal line chart

```php
use Elqora\Chart\Charts\Charts;
use Elqora\Chart\Enums\ValueType;
use Elqora\Chart\Series\Series;

$chart = Charts::line(
    key: 'delivery.throughput',
    title: 'Delivery throughput',
    category: 'time',
    rows: [
        ['time' => '10:00', 'delivered' => 0, 'failed' => 0],
        ['time' => '10:30', 'delivered' => 500, 'failed' => 12],
    ],
    series: [
        new Series('delivered', 'Delivered', 'delivered', ValueType::INTEGER),
        new Series('failed', 'Failed', 'failed', ValueType::INTEGER),
    ],
);
```

## Bar chart

```php
use Elqora\Chart\Charts\Charts;
use Elqora\Chart\Data\PresentationHints;
use Elqora\Chart\Enums\Orientation;
use Elqora\Chart\Series\Series;

$chart = Charts::bar(
    key: 'revenue.by-region',
    title: 'Revenue by region',
    category: 'region',
    rows: [
        ['region' => 'North', 'revenue' => 125000],
        ['region' => 'South', 'revenue' => 98000],
    ],
    series: [
        new Series('revenue', 'Revenue', 'revenue'),
    ],
    presentation: new PresentationHints(orientation: Orientation::HORIZONTAL),
);
```

## Pie chart

```php
use Elqora\Chart\Charts\Charts;

$chart = Charts::pie(
    key: 'orders.status',
    title: 'Orders by status',
    category: 'status',
    value: 'count',
    rows: [
        ['status' => 'completed', 'count' => 83],
        ['status' => 'failed', 'count' => 9],
        ['status' => 'canceled', 'count' => 8],
    ],
);
```

## Scatter chart

```php
use Elqora\Chart\Charts\Charts;

$chart = Charts::scatter(
    key: 'duration.by-quantity',
    title: 'Duration by quantity',
    x: 'quantity',
    y: 'duration',
    rows: [
        ['quantity' => 100, 'duration' => 12],
        ['quantity' => 500, 'duration' => 42],
    ],
);
```

## Manual construction

The builders are only convenience methods. Manual construction remains the canonical chart protocol and is useful when you need direct access to payload DTOs.

```php
use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Data\TabularData;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Enums\ValueType;
use Elqora\Chart\Series\Series;

$chart = new Chart(
    key: 'delivery.throughput',
    type: ChartType::LINE,
    title: 'Delivery throughput',
    data: new TabularData(
        categoryField: 'time',
        rows: [
            ['time' => '10:00', 'delivered' => 0],
            ['time' => '10:30', 'delivered' => 500],
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
```

## Hierarchical chart

```php
use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Data\HierarchyData;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Hierarchy\HierarchyNode;

$chart = new Chart(
    key: 'service.mix',
    type: ChartType::TREEMAP,
    title: 'Service mix',
    data: new HierarchyData([
        new HierarchyNode('root', 'All services', children: [
            new HierarchyNode('email', 'Email', 120),
            new HierarchyNode('sms', 'SMS', 75),
        ]),
    ]),
);
```

## Financial chart

```php
use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Data\CandlestickData;
use Elqora\Chart\Data\CandlestickPoint;
use Elqora\Chart\Enums\ChartType;

$chart = new Chart(
    key: 'market.ohlc',
    type: ChartType::CANDLESTICK,
    title: 'Market OHLC',
    data: new CandlestickData([
        new CandlestickPoint('2026-07-08', open: 100, high: 110, low: 95, close: 105, volume: 1000),
    ]),
);
```

## Serialization

All DTOs serialize to deterministic, JSON-compatible arrays. Nullable fields are omitted consistently.

```php
$array = $chart->toArray();
$json = json_encode($chart, JSON_THROW_ON_ERROR);
$hydrated = Chart::fromArray($array);
```

## Validation

Validation collects structured issues and does not rely on exceptions for expected user-data errors.

```php
$result = $chart->validate();

if (! $result->isValid()) {
    foreach ($result->issues as $issue) {
        echo $issue->code . ': ' . $issue->message . PHP_EOL;
    }
}
```

## Renderer neutrality

Elqora Chart describes chart meaning and data. Hosts decide how to map that model into a renderer, a report, a data table, or another output. The public API deliberately avoids renderer option objects, callbacks, CSS, framework service identifiers, DOM behavior, and component names.

## Non-goals

- Rendering charts.
- Building renderer-specific options.
- Providing frontend components.
- Providing Laravel, Symfony, or other framework integrations.
- Acting as a dashboard, analytics, reporting, or database package.
