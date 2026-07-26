# Renderer Mapping

This package does not provide renderer adapters. It provides a semantic chart definition that a host may map into a renderer.

## Conceptual mappings

- Cartesian `TabularData` can map to series-based renderers, report tables, or document charts by using the category field and each series field.
- `CategoryValueData` can map to radial or categorical renderers by using the category labels and numeric values.
- `CoordinateData` can map to point-based renderers. Bubble charts use the additional size field.
- `HeatmapData` can map to matrix or triplet formats by grouping x, y, and value fields.
- `HierarchyData` can map to tree-capable renderers, nested tables, or document outlines.
- `CandlestickData` can map to OHLC-capable renderers or financial tables.
- Sparkline `TabularData` maps to compact micro-charts stripped of visual clutter (gridlines, axes, legends, dots) in `line`, `area`, or `bar` modes.

## Excluded renderer features

Renderer-specific callbacks, component names, raw option objects, CSS, gradients, exact pixel sizing, interaction behavior, and animation settings are intentionally excluded.

## Fallbacks

Hosts can inspect `Chart::typeName()`, `Chart::isBuiltInType()`, the serialized `family`, and `data.kind` to decide whether to render directly, use a simpler fallback, show a data table, or reject an unsupported chart.
