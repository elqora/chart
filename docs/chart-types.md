# Chart Types

Built-in chart types are represented by `ChartType`.

| Type | Payload | Notes |
| --- | --- | --- |
| `line` | `TabularData` | Category or x field plus numeric series. |
| `area` | `TabularData` | Same field semantics as line. |
| `bar` | `TabularData` | Supports horizontal or vertical orientation hints. |
| `stacked_bar` | `TabularData` | Each series must declare a stack key. |
| `pie` | `CategoryValueData` | Non-negative numeric values. |
| `doughnut` | `CategoryValueData` | Same data semantics as pie. |
| `scatter` | `CoordinateData` | Numeric x and y values. |
| `bubble` | `CoordinateData` | Numeric x, y, and non-negative size values. |
| `radar` | `RadarData` | Indicator definitions plus aligned series values. |
| `gauge` | `GaugeData` | Current value, optional min, max, ranges, and format. |
| `funnel` | `FunnelData` | Ordered stages and numeric values. |
| `heatmap` | `HeatmapData` | x, y, and numeric value fields. |
| `treemap` | `HierarchyData` | Stable hierarchical nodes. |
| `sunburst` | `HierarchyData` | Same hierarchy semantics as treemap. |
| `box_plot` | `BoxPlotData` | Ordered minimum, quartiles, median, and maximum. |
| `candlestick` | `CandlestickData` | OHLC values with logical high and low bounds. |
| `sparkline` | `TabularData` | Minimalist inline trend micro-chart. Supports modes: `line`, `area`, `bar`. |

Future chart types can be represented through `CustomData` when the producer and host agree on a portable custom semantic payload.
