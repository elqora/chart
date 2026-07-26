<?php

declare(strict_types=1);

namespace Elqora\Chart\Validation;

use Elqora\Chart\Charts\Chart;
use Elqora\Chart\Data\BoxPlotData;
use Elqora\Chart\Data\CandlestickData;
use Elqora\Chart\Data\CategoryValueData;
use Elqora\Chart\Data\CoordinateData;
use Elqora\Chart\Data\CustomData;
use Elqora\Chart\Data\FunnelData;
use Elqora\Chart\Data\GaugeData;
use Elqora\Chart\Data\HeatmapData;
use Elqora\Chart\Data\HierarchyData;
use Elqora\Chart\Data\RadarData;
use Elqora\Chart\Data\TabularData;
use Elqora\Chart\Enums\ChartType;
use Elqora\Chart\Hierarchy\HierarchyNode;
use Elqora\Chart\Series\Series;
use Elqora\Chart\Support\Identifier;
use Elqora\Chart\Support\SerializableValue;

final class ChartValidator
{
    public function validate(Chart $chart): ValidationResult
    {
        $issues = new IssueBag();

        $this->validateCommon($chart, $issues);
        $this->validateTypeCompatibility($chart, $issues);

        match (true) {
            $chart->data instanceof TabularData => $this->validateTabular($chart->data, $issues),
            $chart->data instanceof CategoryValueData => $this->validateCategoryValue($chart->data, $issues),
            $chart->data instanceof CoordinateData => $this->validateCoordinate($chart->data, $issues),
            $chart->data instanceof HeatmapData => $this->validateHeatmap($chart->data, $issues),
            $chart->data instanceof RadarData => $this->validateRadar($chart->data, $issues),
            $chart->data instanceof GaugeData => $this->validateGauge($chart->data, $issues),
            $chart->data instanceof FunnelData => $this->validateFunnel($chart->data, $issues),
            $chart->data instanceof HierarchyData => $this->validateHierarchy($chart->data, $issues),
            $chart->data instanceof BoxPlotData => $this->validateBoxPlot($chart->data, $issues),
            $chart->data instanceof CandlestickData => $this->validateCandlestick($chart->data, $issues),
            $chart->data instanceof CustomData => $this->validateCustom($chart->data, $issues),
            default => null,
        };

        return $issues->result();
    }

    private function validateCommon(Chart $chart, IssueBag $issues): void
    {
        if (trim($chart->key) === '') {
            $issues->add('chart.key.empty', 'Chart key must not be empty.', 'key');
        } elseif (! Identifier::isStable($chart->key)) {
            $issues->add('chart.key.unstable', 'Chart key must be a stable identifier.', 'key');
        }

        if (trim($chart->title) === '') {
            $issues->add('chart.title.empty', 'Chart title must not be empty.', 'title');
        }

        if (! $chart->isBuiltInType() && trim($chart->typeName()) === '') {
            $issues->add('chart.type.empty', 'Custom chart type must not be empty.', 'type');
        }

        if (! SerializableValue::isJsonCompatible($chart->meta)) {
            $issues->add('chart.meta.not_json_compatible', 'Chart metadata must be JSON-compatible.', 'meta');
        }
    }

    private function validateTypeCompatibility(Chart $chart, IssueBag $issues): void
    {
        if (! $chart->type instanceof ChartType) {
            if (! $chart->data instanceof CustomData) {
                $issues->add(
                    'chart.type.custom_requires_custom_data',
                    'Unknown chart types must use a custom data payload.',
                    'data.kind',
                );
            }

            return;
        }

        $valid = match ($chart->type) {
            ChartType::LINE, ChartType::AREA, ChartType::BAR, ChartType::STACKED_BAR, ChartType::SPARKLINE => $chart->data instanceof TabularData,
            ChartType::PIE, ChartType::DOUGHNUT => $chart->data instanceof CategoryValueData,
            ChartType::SCATTER, ChartType::BUBBLE => $chart->data instanceof CoordinateData,
            ChartType::HEATMAP => $chart->data instanceof HeatmapData,
            ChartType::RADAR => $chart->data instanceof RadarData,
            ChartType::GAUGE => $chart->data instanceof GaugeData,
            ChartType::FUNNEL => $chart->data instanceof FunnelData,
            ChartType::TREEMAP, ChartType::SUNBURST => $chart->data instanceof HierarchyData,
            ChartType::BOX_PLOT => $chart->data instanceof BoxPlotData,
            ChartType::CANDLESTICK => $chart->data instanceof CandlestickData,
        };

        if (! $valid) {
            $issues->add('chart.type.data_mismatch', 'Chart type is not compatible with the data payload kind.', 'data.kind');
        }

        if ($chart->type === ChartType::STACKED_BAR && $chart->data instanceof TabularData) {
            foreach ($chart->data->series as $index => $series) {
                if ($series->stack === null || $series->stack === '') {
                    $issues->add(
                        'series.stack.missing',
                        'Stacked bar series must declare a stack key.',
                        "data.series.$index.stack",
                    );
                }
            }
        }

        if ($chart->type === ChartType::BUBBLE && $chart->data instanceof CoordinateData && $chart->data->sizeField === null) {
            $issues->add('bubble.size_field.missing', 'Bubble charts must declare a size field.', 'data.size_field');
        }
    }

    private function validateTabular(TabularData $data, IssueBag $issues): void
    {
        if ($data->categoryField === '') {
            $issues->add('tabular.category_field.empty', 'Tabular charts must declare a category field.', 'data.category_field');
        }

        if ($data->rows === []) {
            $issues->add('data.rows.empty', 'Chart data must contain at least one row.', 'data.rows');
        }

        if ($data->series === []) {
            $issues->add('series.empty', 'Tabular charts must contain at least one series.', 'data.series');
        }

        $this->validateUniqueSeries($data->series, $issues);

        foreach ($data->rows as $rowIndex => $row) {
            $this->requireField($row, $data->categoryField, "data.rows.$rowIndex", $issues);

            foreach ($data->series as $seriesIndex => $series) {
                if (! $this->requireField($row, $series->field, "data.rows.$rowIndex", $issues)) {
                    continue;
                }

                if (! is_numeric($row[$series->field]) && $row[$series->field] !== null) {
                    $issues->add(
                        'series.value.not_numeric',
                        'Series values must be numeric or null.',
                        "data.rows.$rowIndex.{$series->field}",
                        ['series' => $series->key, 'series_index' => $seriesIndex],
                    );
                }
            }
        }
    }

    private function validateCategoryValue(CategoryValueData $data, IssueBag $issues): void
    {
        foreach (['category_field' => $data->categoryField, 'value_field' => $data->valueField] as $key => $field) {
            if ($field === '') {
                $issues->add("category_value.$key.empty", "Category-value charts must declare $key.", "data.$key");
            }
        }

        foreach ($data->rows as $rowIndex => $row) {
            $this->requireField($row, $data->categoryField, "data.rows.$rowIndex", $issues);

            if (! $this->requireField($row, $data->valueField, "data.rows.$rowIndex", $issues)) {
                continue;
            }

            $value = $row[$data->valueField];
            if (! is_numeric($value)) {
                $issues->add('category_value.value.not_numeric', 'Category values must be numeric.', "data.rows.$rowIndex.{$data->valueField}");
            } elseif ($value < 0) {
                $issues->add('category_value.value.negative', 'Category values must not be negative.', "data.rows.$rowIndex.{$data->valueField}");
            }
        }
    }

    private function validateCoordinate(CoordinateData $data, IssueBag $issues): void
    {
        foreach ($data->rows as $rowIndex => $row) {
            foreach ([$data->xField, $data->yField, $data->sizeField] as $field) {
                if ($field === null) {
                    continue;
                }

                if (! $this->requireField($row, $field, "data.rows.$rowIndex", $issues)) {
                    continue;
                }

                if (! is_numeric($row[$field])) {
                    $issues->add('coordinate.value.not_numeric', 'Coordinate values must be numeric.', "data.rows.$rowIndex.$field");
                }
            }

            if ($data->sizeField !== null && isset($row[$data->sizeField]) && is_numeric($row[$data->sizeField]) && $row[$data->sizeField] < 0) {
                $issues->add('bubble.size.negative', 'Bubble size values must not be negative.', "data.rows.$rowIndex.{$data->sizeField}");
            }
        }
    }

    private function validateHeatmap(HeatmapData $data, IssueBag $issues): void
    {
        foreach ($data->rows as $rowIndex => $row) {
            foreach ([$data->xField, $data->yField, $data->valueField] as $field) {
                if (! $this->requireField($row, $field, "data.rows.$rowIndex", $issues)) {
                    continue;
                }
            }

            if (isset($row[$data->valueField]) && ! is_numeric($row[$data->valueField])) {
                $issues->add('heatmap.value.not_numeric', 'Heatmap values must be numeric.', "data.rows.$rowIndex.{$data->valueField}");
            }
        }
    }

    private function validateRadar(RadarData $data, IssueBag $issues): void
    {
        if ($data->indicators === []) {
            $issues->add('radar.indicators.empty', 'Radar charts must declare indicators.', 'data.indicators');
        }

        foreach ($data->indicators as $index => $indicator) {
            if ($indicator->key === '' || $indicator->label === '') {
                $issues->add('radar.indicator.invalid', 'Radar indicators require a key and label.', "data.indicators.$index");
            }

            if ($indicator->minimum !== null && $indicator->maximum !== null && $indicator->minimum > $indicator->maximum) {
                $issues->add('radar.indicator.range_invalid', 'Radar indicator minimum must not exceed maximum.', "data.indicators.$index");
            }
        }

        foreach ($data->series as $seriesIndex => $series) {
            if (count($series->values) !== count($data->indicators)) {
                $issues->add('radar.series.length_mismatch', 'Radar series values must match indicator count.', "data.series.$seriesIndex.values");
            }

            foreach ($series->values as $valueIndex => $value) {
                if ($value !== null && ! is_numeric($value)) {
                    $issues->add('radar.value.not_numeric', 'Radar values must be numeric or null.', "data.series.$seriesIndex.values.$valueIndex");
                }
            }
        }
    }

    private function validateGauge(GaugeData $data, IssueBag $issues): void
    {
        if ($data->minimum !== null && $data->maximum !== null && $data->minimum >= $data->maximum) {
            $issues->add('gauge.range.invalid', 'Gauge minimum must be lower than maximum.', 'data');
        }

        if ($data->minimum !== null && $data->value < $data->minimum) {
            $issues->add('gauge.value.below_minimum', 'Gauge value must not be below minimum.', 'data.value');
        }

        if ($data->maximum !== null && $data->value > $data->maximum) {
            $issues->add('gauge.value.above_maximum', 'Gauge value must not be above maximum.', 'data.value');
        }
    }

    private function validateFunnel(FunnelData $data, IssueBag $issues): void
    {
        if ($data->stages === []) {
            $issues->add('funnel.stages.empty', 'Funnel charts must contain ordered stages.', 'data.stages');
        }

        foreach ($data->stages as $index => $stage) {
            if ($stage->key === '' || $stage->label === '') {
                $issues->add('funnel.stage.invalid', 'Funnel stages require a key and label.', "data.stages.$index");
            }

            if (! is_numeric($stage->value)) {
                $issues->add('funnel.stage.value_not_numeric', 'Funnel stage values must be numeric.', "data.stages.$index.value");
            }
        }
    }

    private function validateHierarchy(HierarchyData $data, IssueBag $issues): void
    {
        if ($data->roots === []) {
            $issues->add('hierarchy.roots.empty', 'Hierarchical charts must contain at least one root node.', 'data.roots');
        }

        $seen = [];
        foreach ($data->roots as $index => $node) {
            $this->validateNode($node, "data.roots.$index", $seen, $issues);
        }
    }

    /**
     * @param array<string, true> $seen
     */
    private function validateNode(HierarchyNode $node, string $path, array &$seen, IssueBag $issues): void
    {
        if ($node->key === '' || $node->label === '') {
            $issues->add('hierarchy.node.invalid', 'Hierarchy nodes require a key and label.', $path);
        }

        if (isset($seen[$node->key])) {
            $issues->add('hierarchy.node.duplicate_key', 'Hierarchy node keys must be unique within a chart.', "$path.key");
        }

        $seen[$node->key] = true;

        if ($node->value !== null && ! is_numeric($node->value)) {
            $issues->add('hierarchy.node.value_not_numeric', 'Hierarchy node values must be numeric when present.', "$path.value");
        }

        foreach ($node->children as $index => $child) {
            $this->validateNode($child, "$path.children.$index", $seen, $issues);
        }
    }

    private function validateBoxPlot(BoxPlotData $data, IssueBag $issues): void
    {
        foreach ($data->items as $index => $item) {
            if (! ($item->minimum <= $item->lowerQuartile && $item->lowerQuartile <= $item->median && $item->median <= $item->upperQuartile && $item->upperQuartile <= $item->maximum)) {
                $issues->add('box_plot.order.invalid', 'Box plot values must be ordered minimum <= Q1 <= median <= Q3 <= maximum.', "data.items.$index");
            }

            foreach ($item->outliers as $outlierIndex => $outlier) {
                if (! is_numeric($outlier)) {
                    $issues->add('box_plot.outlier.not_numeric', 'Box plot outliers must be numeric.', "data.items.$index.outliers.$outlierIndex");
                }
            }
        }
    }

    private function validateCandlestick(CandlestickData $data, IssueBag $issues): void
    {
        foreach ($data->points as $index => $point) {
            if ($point->category === '') {
                $issues->add('candlestick.category.empty', 'Candlestick points require a category or timestamp.', "data.points.$index.category");
            }

            if ($point->high < $point->low) {
                $issues->add('candlestick.bounds.invalid', 'Candlestick high must not be lower than low.', "data.points.$index");
            }

            if ($point->open > $point->high || $point->open < $point->low || $point->close > $point->high || $point->close < $point->low) {
                $issues->add('candlestick.ohlc.out_of_bounds', 'Open and close values must fall between low and high.', "data.points.$index");
            }
        }
    }

    private function validateCustom(CustomData $data, IssueBag $issues): void
    {
        if ($data->customType === '') {
            $issues->add('custom.type.empty', 'Custom payloads must declare a custom type.', 'data.custom_type');
        }

        if (! SerializableValue::isJsonCompatible($data->payload)) {
            $issues->add('custom.payload.not_json_compatible', 'Custom payloads must be JSON-compatible.', 'data.payload');
        }
    }

    /**
     * @param list<Series> $series
     */
    private function validateUniqueSeries(array $series, IssueBag $issues): void
    {
        $seen = [];

        foreach ($series as $index => $item) {
            if ($item->key === '') {
                $issues->add('series.key.empty', 'Series key must not be empty.', "data.series.$index.key");
            }

            if (isset($seen[$item->key])) {
                $issues->add('series.key.duplicate', 'Series keys must be unique.', "data.series.$index.key");
            }

            $seen[$item->key] = true;
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function requireField(array $row, string $field, string $path, IssueBag $issues): bool
    {
        if ($field === '' || ! array_key_exists($field, $row)) {
            $issues->add('data.field.missing', 'A referenced data field is missing.', $field === '' ? $path : "$path.$field");

            return false;
        }

        return true;
    }
}
