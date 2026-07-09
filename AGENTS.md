AGENTS.md

Project

This repository contains Elqora Chart, a framework-neutral PHP package for defining portable, renderer-agnostic chart data.

The package provides contracts, DTOs, enums, validation, serialization, and hydration for representing charts as structured data.

It does not render charts.

The chart definitions produced by this package should be consumable by hosts using systems such as:

- Apache ECharts
- Chart.js
- Recharts
- ApexCharts
- Plotly
- Highcharts
- D3-based renderers
- native mobile chart libraries
- reporting and document-generation systems
- custom internal visualization engines

The package must not depend on any of those systems.

Primary objective

Design a portable chart model capable of representing the semantics required by most major chart systems without copying or exposing any renderer-specific configuration format.

The package should describe:

- what kind of chart is being represented;
- what data is being visualized;
- what each field and dimension means;
- which data series are involved;
- how values should be interpreted;
- how complex chart data is structured;
- enough portable presentation intent for a host to render the chart correctly.

The package should not describe:

- exact JavaScript components;
- frontend callbacks;
- renderer option objects;
- CSS;
- DOM behaviour;
- framework-specific configuration;
- chart-library-specific property names.

Core rule

Elqora Chart describes chart meaning and chart data.

The host decides how the chart is rendered.

A valid chart definition must remain useful even when the consuming host changes from one renderer to another.

For example, the same Elqora Chart object should be adaptable into:

ECharts option
Chart.js configuration
Recharts components
ApexCharts series/options
Plotly traces/layout
Highcharts series/options
a data table
an SVG or PDF report

Renderer compatibility does not mean reproducing every library option.

It means representing the portable semantic common ground required to create equivalent charts across those systems.

Package identity

- Composer package: "elqora/chart"
- PHP namespace: "Elqora\Chart\"
- PHP version: 8.2 or newer
- Source root: "src/"
- Tests root: "tests/"
- License: MIT
- Runtime dependencies: none
- Framework dependencies: forbidden
- Renderer dependencies: forbidden

Use strict types in all PHP files.

Prefer:

- "final readonly" DTOs;
- backed enums for stable controlled values;
- small interfaces where interoperability requires them;
- immutable public chart definitions;
- deterministic array and JSON serialization;
- structured validation results.

Follow PSR-12.

Package boundaries

Elqora Chart owns:

- chart identity;
- chart types;
- chart-family classifications;
- chart data payloads;
- series definitions;
- dimensions;
- measures;
- categories;
- hierarchical nodes;
- coordinate data;
- range data;
- financial data;
- value semantics;
- portable formatting hints;
- serialization;
- hydration;
- validation;
- structured validation issues;
- extension mechanisms for unsupported chart types.

Hosts own:

- chart-library selection;
- visual themes;
- colors;
- fonts;
- exact axis layout;
- responsive behaviour;
- animations;
- transitions;
- interactive tooltips;
- frontend legends;
- zoom controls;
- brushing;
- exporting;
- accessibility presentation;
- renderer-specific fallback behaviour;
- conversion into renderer-specific configuration.

Renderer neutrality

Do not expose renderer-specific properties in the public API.

Forbidden examples include:

$chart->echartsOptions
$chart->chartJsConfig
$chart->rechartsComponent
$chart->plotlyTrace
$chart->highchartsOptions
$series->apexType

Do not expose renderer callbacks such as:

tooltipFormatter
axisLabelFormatter
dataLabelCallback
onPointClick

Portable semantic hints are allowed when they describe the data rather than a specific implementation.

Examples:

valueType: ValueType::PERCENTAGE
unit: 'percent'
currency: 'USD'
precision: 2
stack: 'revenue'
orientation: Orientation::HORIZONTAL

A host may translate those semantics into its chosen renderer.

Public API discipline

Treat every public element as a long-term contract:

- namespace;
- class name;
- interface;
- enum case;
- constructor;
- property;
- method;
- serialized key;
- validation code;
- payload shape.

Before changing the public model:

1. Determine whether the change is backward-compatible.
2. Review all examples and tests.
3. Preserve stable serialized keys.
4. Document major architectural decisions.
5. Avoid aliases for unresolved design uncertainty.
6. Do not expose implementation-specific classes in serialized output.

Never serialize:

- PHP class names;
- closures;
- resources;
- frontend callbacks;
- renderer classes;
- framework service identifiers;
- arbitrary non-serializable objects.

Chart architecture

Do not assume every chart can be represented adequately as:

labels + datasets

That model works for some line, bar, and pie charts, but not for all chart families.

The package must support semantically different data structures where required.

Prefer a design with:

- a small common chart definition;
- explicit chart type;
- typed or clearly defined data payloads;
- portable dimension and series declarations;
- family-specific validation;
- predictable serialized representations.

Avoid a single universal DTO containing dozens of nullable properties.

Also avoid creating a completely unrelated class hierarchy for every individual chart type when several chart types share a valid common model.

Use chart families where useful.

Possible chart families include:

Cartesian
Categorical
Radial
Coordinate
Hierarchical
Matrix
Statistical
Financial
Flow
Network
Specialized

The exact family structure should be justified in "docs/architecture.md".

Initial chart types

The initial public model should account for common and advanced chart types such as:

- line;
- area;
- bar;
- stacked bar;
- pie;
- doughnut;
- scatter;
- bubble;
- radar;
- gauge;
- funnel;
- heatmap;
- treemap;
- sunburst;
- box plot;
- candlestick.

The architecture should leave a clear extension path for:

- waterfall;
- histogram;
- range charts;
- polar charts;
- sankey;
- graph or network charts;
- geographic maps;
- choropleths;
- parallel coordinates;
- timeline charts;
- Gantt charts;
- custom chart types.

Do not implement every possible chart merely to increase the enum size.

Only add a built-in type when the data semantics and validation rules are understood.

Common chart model

The core chart definition should contain stable, portable concepts such as:

- chart key;
- chart type;
- title;
- optional description;
- data payload;
- series or measures where applicable;
- dimensions where applicable;
- optional semantic presentation hints;
- optional metadata.

A chart should have a stable key so hosts can:

- identify it;
- cache it;
- replace it;
- arrange it;
- persist it;
- compare revisions.

The title should describe the chart to a human.

The chart type should identify the intended visualization family.

The data payload should contain the actual renderer-neutral information required to render the chart.

Chart data representations

The package must deliberately support different data shapes.

Cartesian and category charts

Examples:

- line;
- area;
- bar;
- stacked bar.

Typical portable representation:

[
    ['time' => '10:00', 'delivered' => 0, 'failed' => 0],
    ['time' => '10:30', 'delivered' => 500, 'failed' => 12],
    ['time' => '11:00', 'delivered' => 1200, 'failed' => 15],
]

The model should identify:

- the category or x dimension;
- one or more numeric series;
- labels;
- value semantics;
- optional stack identity.

Pie and doughnut charts

Typical representation:

[
    ['status' => 'completed', 'count' => 83],
    ['status' => 'failed', 'count' => 9],
    ['status' => 'canceled', 'count' => 8],
]

The model should identify:

- the category field;
- the numeric value field;
- optional labels;
- value semantics.

Scatter charts

Typical representation:

[
    ['quantity' => 100, 'duration' => 12],
    ['quantity' => 500, 'duration' => 42],
    ['quantity' => 1000, 'duration' => 95],
]

The model should identify:

- numeric x value;
- numeric y value;
- optional point label;
- optional grouping series.

Bubble charts

The model should identify at least:

- numeric x value;
- numeric y value;
- numeric size value;
- optional category or series identity.

Heatmaps

The model should support:

x
y
value

or an equivalent matrix representation.

The representation must remain portable across ECharts, Plotly, Highcharts, ApexCharts, and other capable renderers.

Radar charts

The model should represent:

- indicator definitions;
- indicator labels;
- optional indicator limits;
- one or more sets of values.

Box plots

The model should represent:

- minimum;
- lower quartile;
- median;
- upper quartile;
- maximum;
- optional outliers.

Candlestick charts

The model should represent:

- open;
- high;
- low;
- close;
- category or timestamp;
- optional volume or related series.

Treemap and sunburst charts

The model should represent hierarchical nodes with:

- stable key;
- label;
- optional value;
- optional children;
- optional metadata.

Validation must detect malformed hierarchies where practical.

Gauge charts

The model should represent:

- current value;
- optional minimum;
- optional maximum;
- optional unit;
- optional threshold or range semantics.

Avoid exposing renderer-specific arc, pointer, or dial configuration.

Funnel charts

The model should represent ordered stages and numeric values.

The order of stages must be preserved.

Dimensions and measures

Where useful, model chart fields semantically as dimensions and measures.

A dimension identifies how data is grouped or positioned.

Examples:

time
date
category
region
status
quantity range
service

A measure identifies a value being compared or aggregated.

Examples:

delivered units
completion rate
revenue
duration
failure count
average speed

Do not force these terms into every chart if they make a chart family less clear.

Use them when they improve portability and validation.

Series

Series definitions should describe portable meaning.

A series may include:

- stable key;
- label;
- referenced value field;
- optional value type;
- optional unit;
- optional grouping;
- optional stack key;
- optional metadata.

A series should not contain:

- renderer component names;
- JavaScript callbacks;
- raw renderer options;
- CSS colors unless a future portable styling contract explicitly supports them;
- library-specific interpolation modes.

Mixed charts may require an optional per-series chart type.

Only support this if the architecture clearly defines compatibility and validation.

Value semantics

Hosts must be able to interpret values without guessing.

Provide portable value semantics where necessary.

Possible value types include:

- string;
- category;
- integer;
- number;
- percentage;
- currency;
- duration;
- date;
- datetime;
- boolean.

Possible portable formatting metadata may include:

- unit;
- prefix;
- suffix;
- currency code;
- duration unit;
- decimal precision;
- percentage representation.

Choose and document a consistent percentage convention.

For example, decide whether:

42 means 42%

or:

0.42 means 42%

Do not leave this ambiguous.

Formatting metadata must remain optional and semantic.

It must not become a frontend formatting engine.

Presentation hints

Some presentation intent may be portable enough to belong in core.

Examples may include:

- horizontal versus vertical orientation;
- stacked versus grouped series;
- normalized percentage stacking;
- chart title;
- chart description;
- value units;
- dimension labels;
- series labels;
- ordering;
- whether null values should connect;
- whether the chart is cumulative.

Only include presentation hints that have clear meaning across major chart systems.

Do not include low-level appearance configuration such as:

- exact pixel widths;
- CSS classes;
- font families;
- colors;
- gradients;
- shadows;
- animation duration;
- renderer-specific curves;
- component names.

When uncertain, prefer leaving visual decisions to the host.

Metadata

Metadata is for optional producer or domain-specific information.

It must remain JSON-compatible.

Metadata is not a substitute for missing core semantics.

Do not place required chart behaviour inside "meta".

Bad:

meta: [
    'xField' => 'time',
    'valueField' => 'delivered',
]

when those fields are necessary to interpret the chart.

Good:

meta: [
    'source' => 'service_analysis',
    'sample_size' => 120,
]

Serialization

All public chart objects must serialize into deterministic, JSON-compatible arrays.

Requirements:

- enums serialize to backed string values;
- nested DTOs serialize recursively;
- field names remain stable;
- array order is deterministic where order is meaningful;
- metadata remains JSON-compatible;
- no PHP implementation details leak into output;
- nullable-field behaviour is consistent;
- chart data remains understandable outside PHP.

Document whether null fields are:

- omitted; or
- serialized explicitly as "null".

Use one policy consistently.

A serialized chart should be suitable for:

json_encode($chart->toArray());

without preprocessing.

Hydration

Support hydration from arrays if it can be implemented safely and consistently.

Hydration should:

- restore enums;
- restore nested DTOs;
- preserve chart-family payloads;
- reject unsupported structures;
- produce clear structured errors;
- round-trip valid serialized chart definitions.

Do not silently guess malformed data shapes.

If full hydration would create excessive complexity, document the supported boundary clearly.

Validation

Validation must be renderer-neutral and chart-family aware.

Expected invalid chart payloads should produce structured validation results rather than relying only on exceptions.

A validation issue should contain:

- stable code;
- human-readable message;
- optional path;
- optional details.

Validation should collect multiple independent issues where practical.

Common validation should include:

- non-empty chart key;
- valid chart type;
- non-empty title where required;
- valid data payload;
- unique series keys;
- valid referenced fields;
- JSON-compatible metadata;
- compatible value types;
- deterministic identifiers.

Chart-specific validation should include:

Line, area, and bar

- category or x field exists;
- referenced series fields exist;
- measure values are numeric;
- stack declarations are valid.

Pie and doughnut

- category field exists;
- value field exists;
- values are numeric;
- negative values are rejected unless deliberately supported.

Scatter

- x and y values are numeric.

Bubble

- x, y, and size values are numeric;
- bubble size follows documented constraints.

Heatmap

- x, y, and value fields exist;
- value fields are numeric.

Radar

- indicators are valid;
- data lengths match indicator counts;
- optional maximums are numeric.

Box plot

- required statistical values exist;
- values are numeric;
- ordering constraints are valid where applicable.

Candlestick

- open, high, low, and close exist;
- values are numeric;
- high and low bounds are logically valid.

Treemap and sunburst

- node keys are stable;
- child collections are valid;
- recursive structures are serializable;
- invalid recursion or cycles are rejected where detectable.

Gauge

- value is numeric;
- minimum and maximum are valid;
- value-range rules are documented.

Funnel

- stage order is preserved;
- stage labels exist;
- values are numeric.

Do not validate whether a chart is visually attractive.

Validation and construction

Prefer allowing chart objects to be constructed and then validated through a validator or validation method when data may originate externally.

Use constructor exceptions only for immediate programmer invariants such as impossible object state.

Do not use exceptions as the sole mechanism for user-supplied payload validation.

Extensibility

The package must support future chart types without forcing every host to upgrade immediately.

Hosts should be able to determine:

- the chart type;
- whether they support it;
- whether they can provide a fallback;
- whether the payload is built-in or custom.

Consider supporting a custom chart identifier or custom payload mechanism.

Any custom mechanism must:

- preserve explicit type identification;
- preserve serialization;
- remain JSON-compatible;
- allow unsupported-type detection;
- avoid weakening validation of built-in chart types;
- avoid exposing renderer-specific configuration as “custom data.”

Custom chart support must not become a loophole for passing raw ECharts, Chart.js, Plotly, or Highcharts configuration through the package.

Compatibility strategy

Use major chart libraries as compatibility references, not implementation dependencies.

While designing a built-in chart type, consider how its semantics map into:

- Apache ECharts;
- Chart.js;
- Recharts;
- ApexCharts;
- Plotly;
- Highcharts.

The goal is not byte-for-byte option compatibility.

The goal is that a host adapter can translate the portable chart object into an equivalent representation without missing essential data.

When adding or changing a chart type, document:

1. the portable semantic model;
2. the required data shape;
3. how it maps conceptually to major renderer families;
4. what renderer-specific features are intentionally excluded;
5. fallback considerations for hosts that do not support it.

Maintain a compatibility document such as:

docs/renderer-mapping.md

This document should describe conceptual mappings without adding renderer dependencies.

Explicit exclusions

Do not implement:

- actual chart rendering;
- HTML generation;
- SVG generation;
- PNG generation;
- canvas rendering;
- React components;
- Vue components;
- Angular components;
- frontend packages;
- Laravel service providers;
- Symfony bundles;
- database models;
- HTTP controllers;
- dashboards;
- analytics engines;
- report builders;
- renderer adapters;
- ECharts option builders;
- Chart.js configuration builders;
- Plotly trace builders;
- Highcharts option builders;
- DGP-specific service analysis;
- domain-specific metrics.

Those belong in separate packages or consuming applications.

Suggested repository structure

Use a structure similar to:

src/
├── Contracts/
├── Charts/
├── Data/
├── Dimensions/
├── Series/
├── Enums/
├── Formatting/
├── Hierarchy/
├── Serialization/
├── Validation/
├── Exceptions/
└── Support/

tests/
├── Unit/
├── Serialization/
├── Hydration/
├── Validation/
└── Fixtures/

docs/
├── architecture.md
├── chart-data.md
├── chart-types.md
├── renderer-mapping.md
├── serialization.md
└── validation.md

This is guidance, not a requirement.

Prefer a simpler structure when it remains clear.

Avoid unnecessary directory depth and one-class abstractions with no meaningful purpose.

Documentation requirements

Create and maintain:

- "README.md"
- "docs/architecture.md"
- "docs/chart-data.md"
- "docs/chart-types.md"
- "docs/renderer-mapping.md"
- "docs/serialization.md"
- "docs/validation.md"

The README should include:

- package purpose;
- installation;
- minimal line-chart example;
- bar-chart example;
- pie-chart example;
- scatter-chart example;
- hierarchical-chart example;
- financial-chart example;
- serialization example;
- validation example;
- explanation of renderer neutrality;
- explicit non-goals.

Every documented example must compile against the current API.

Do not claim that a renderer is directly supported unless a separate adapter exists.

Instead say that the portable chart model can be mapped by a host into that renderer.

Architecture documentation

Before implementing a broad public API, document the proposed model in "docs/architecture.md".

The architecture document should explain:

- the common chart abstraction;
- chart-family structure;
- data payload strategy;
- series model;
- value semantics;
- serialization shape;
- validation strategy;
- extension strategy;
- renderer-neutrality rules;
- important trade-offs.

If implementation reveals a better design, update the architecture document rather than allowing it to become stale.

Tests

Every public behaviour requires PHPUnit coverage.

Tests should include:

- chart construction;
- every built-in chart type;
- chart-family payloads;
- series definitions;
- dimensions;
- measures;
- value semantics;
- metadata;
- serialization;
- hydration;
- round-trip behaviour;
- enum serialization;
- deterministic output;
- unique identifiers;
- missing fields;
- invalid references;
- invalid numeric values;
- malformed hierarchy;
- invalid candlestick values;
- invalid box-plot values;
- invalid heatmap values;
- invalid radar dimensions;
- unsupported custom chart behaviour;
- JSON compatibility;
- null-field policy.

Tests must run without:

- network access;
- Node.js;
- browsers;
- databases;
- PHP frameworks;
- chart libraries.

Do not remove or weaken tests merely to make implementation pass.

Quality checks

Before declaring work complete:

1. Run "composer validate --strict".
2. Run the full PHPUnit suite.
3. Run PHP syntax checks over source and tests.
4. Confirm that the runtime package has no dependencies.
5. Confirm that no framework dependency exists.
6. Confirm that no renderer dependency exists.
7. Search the source for renderer-specific option names.
8. Review all serialized keys for stability.
9. Verify all README examples against the actual API.
10. Review whether any metadata field should instead be a core semantic field.
11. Review whether any public abstraction is speculative or unused.
12. Confirm that advanced chart types have semantically correct payloads.
13. Confirm that hosts can detect unsupported chart types.
14. Confirm that the package remains useful outside DGP and Elqora applications.

Working procedure

For substantial work:

1. Inspect the repository.
2. Read this file and current documentation.
3. Identify affected public contracts.
4. Document major architecture decisions.
5. Add or update tests.
6. Implement the smallest coherent change.
7. Run targeted tests.
8. Run the complete test suite.
9. Update documentation.
10. Review serialization compatibility.
11. Review renderer neutrality.
12. Summarize changes and unresolved trade-offs.

Do not introduce unrelated refactors while completing a focused task.

Decision priority

When design concerns conflict, apply this order:

1. Renderer neutrality
2. Semantically correct chart data
3. Stable public contracts
4. Compatibility with major chart systems
5. Framework neutrality
6. Deterministic serialization
7. Strong validation
8. Extensibility
9. Simplicity
10. Convenience
11. Renderer-specific feature coverage

A feature that requires exposing one renderer’s private configuration format does not belong in core.

A model that is simple but cannot correctly represent the chart’s data semantics should be redesigned.

A model that supports every renderer option but loses portability should be rejected.

Final standard

Elqora Chart should become a stable chart-data protocol for PHP applications.

A producer should be able to create a chart definition without knowing which frontend library will render it.

A host should be able to receive that definition, inspect its type and semantics, validate support, and translate it into its chosen renderer.

The chart object must therefore be:

- portable;
- explicit;
- serializable;
- validatable;
- extensible;
- independent of rendering technology.