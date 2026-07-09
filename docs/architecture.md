# Architecture

Elqora Chart is a portable chart-data protocol. The common object is `Chart`, which contains a stable `key`, a built-in `ChartType` or custom type string, a human title, optional description, a typed data payload, and JSON-compatible metadata.

## Chart families

Built-in chart types are grouped by semantic data family:

- Cartesian: line, area, bar, stacked bar.
- Categorical: pie, doughnut.
- Coordinate: scatter, bubble.
- Radial: radar, gauge.
- Matrix: heatmap.
- Hierarchical: treemap, sunburst.
- Statistical: box plot.
- Financial: candlestick.
- Flow: funnel.
- Custom: explicit custom payloads for future or host-specific chart semantics.

Families are used to guide validation and host support decisions. They are not renderer names and do not imply a frontend implementation.

## Data payload strategy

The package avoids a universal `labels + datasets` model because several chart types need different semantics. Cartesian charts use `TabularData` with a category field and series field references. Pie and doughnut charts use `CategoryValueData`. Scatter and bubble charts use `CoordinateData`. Advanced chart families have explicit payloads such as `HierarchyData`, `RadarData`, `BoxPlotData`, and `CandlestickData`.

Each payload serializes a `kind` field so hydration can restore the proper DTO without serializing PHP class names.

## Series and fields

`Series` describes portable meaning: key, label, referenced value field, value type, optional format, grouping, stack identity, and optional compatible per-series chart type. It never contains renderer component names, callbacks, raw option objects, or CSS.

`Dimension` describes grouping or position fields where those concepts improve portability.

## Value semantics

`ValueFormat` captures semantic hints such as value type, unit, currency, duration unit, precision, prefix, suffix, and percentage convention. Percentages default to the `whole_number` convention, where `42` means `42%`. The `fraction` convention may be selected when producers use `0.42` for `42%`.

Formatting is intentionally not a frontend formatting engine.

## Serialization shape

Serialization is deterministic and JSON-compatible. Enums serialize to backed string values. Nested DTOs serialize recursively. Nullable fields are omitted rather than emitted as explicit `null`.

Serialized output never includes PHP class names, closures, resources, framework identifiers, or renderer classes.

## Hydration

`Chart::fromArray()` delegates to `ChartHydrator`. Hydration restores built-in enum types where possible, preserves unknown type strings for custom charts, and restores payload DTOs by `data.kind`.

Unknown payload kinds raise `HydrationException`; malformed but structurally recognizable payloads should be passed to validation.

## Validation

`ChartValidator` validates common chart requirements and family-specific semantics. It returns `ValidationResult` with stable `ValidationIssue` codes, messages, optional paths, and optional details.

Validation checks stable identifiers, JSON-compatible metadata, type-to-payload compatibility, field references, numeric values, stack requirements, radar dimensions, hierarchy key uniqueness, gauge ranges, box-plot ordering, and candlestick OHLC bounds.

## Extension strategy

Unknown chart type strings are allowed only with `CustomData`. The custom payload must have an explicit custom type, family, and JSON-compatible payload. Custom data is not a loophole for passing raw renderer configuration; hosts should reject custom payloads that are actually renderer option objects.

## Trade-offs

The model favors semantic correctness and portability over renderer feature coverage. Some visual choices, such as colors, animation timing, exact axis layout, callbacks, and responsive behavior, are intentionally left to hosts.
