# Validation

`Chart::validate()` returns `ValidationResult`.

```php
$result = $chart->validate();

if (! $result->isValid()) {
    foreach ($result->issues as $issue) {
        // $issue->code, $issue->message, $issue->path, $issue->details
    }
}
```

Validation is renderer-neutral and chart-family aware. It collects multiple independent issues where practical.

## Common checks

- Non-empty stable chart key.
- Non-empty chart title.
- Compatible chart type and data payload.
- JSON-compatible chart metadata.
- Unknown chart types require `CustomData`.

## Payload checks

- Tabular charts require field references to exist and series values to be numeric or null.
- Stacked bars require stack keys.
- Pie and doughnut values must be numeric and non-negative.
- Scatter values must be numeric.
- Bubble charts require numeric, non-negative sizes.
- Heatmaps require x, y, and numeric value fields.
- Radar series lengths must match indicator counts.
- Gauge values must respect documented min and max ranges.
- Funnels preserve stage order and require stage labels and numeric values.
- Hierarchies require stable node keys and reject duplicate keys.
- Box plots require ordered five-number summaries.
- Candlestick charts require valid OHLC bounds.
- Custom payloads must be explicit and JSON-compatible.
