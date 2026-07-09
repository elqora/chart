# Chart Data

Elqora Chart supports several payload shapes because chart families carry different semantics.

## Tabular data

`TabularData` is used for line, area, bar, and stacked bar charts. It declares a `categoryField`, a list of rows, series definitions, optional dimensions, and optional presentation hints.

Rows are associative arrays. Series reference fields inside those rows.

## Category-value data

`CategoryValueData` is used for pie and doughnut charts. It declares a category field, a numeric value field, and rows. Values must be numeric and non-negative.

## Coordinate data

`CoordinateData` is used for scatter and bubble charts. It declares numeric x and y fields. Bubble charts additionally require a numeric, non-negative size field.

## Heatmap data

`HeatmapData` declares x, y, and numeric value fields over a list of rows. This keeps the model portable across matrix-capable renderers and data-table fallbacks.

## Radar data

`RadarData` declares indicator definitions and one or more `RadarSeries` value lists. Each series must have the same number of values as the indicator list.

## Hierarchy data

`HierarchyData` contains one or more root `HierarchyNode` objects. Nodes have stable keys, labels, optional numeric values, optional children, and metadata.

## Statistical and financial data

`BoxPlotData` contains five-number summaries with optional outliers. `CandlestickData` contains category or timestamp values plus open, high, low, close, and optional volume.

## Metadata

Metadata is optional and must be JSON-compatible. Required chart behavior belongs in first-class fields, not `meta`.
