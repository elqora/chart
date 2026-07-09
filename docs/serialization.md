# Serialization

All public DTOs implement `toArray()`. `Chart` also implements `JsonSerializable`.

```php
$array = $chart->toArray();
$json = json_encode($chart, JSON_THROW_ON_ERROR);
$hydrated = Chart::fromArray($array);
```

## Policies

- Enums serialize to backed string values.
- Nested DTOs serialize recursively.
- Nullable fields are omitted.
- Empty metadata arrays are omitted.
- Field order is deterministic.
- Serialized output includes `data.kind` and never includes PHP class names.

## Hydration boundary

Hydration supports the serialized shapes produced by this package. Unknown payload kinds throw `HydrationException`. Recognized payloads with invalid values should be validated with `Chart::validate()`.
