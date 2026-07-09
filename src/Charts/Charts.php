<?php

declare(strict_types=1);

namespace Elqora\Chart\Charts;

use Elqora\Chart\Data\CategoryValueData;
use Elqora\Chart\Data\CoordinateData;
use Elqora\Chart\Data\PresentationHints;
use Elqora\Chart\Data\TabularData;
use Elqora\Chart\Enums\ChartType;

final class Charts
{
    /**
     * @param list<array<string, mixed>> $rows
     * @param list<\Elqora\Chart\Series\Series> $series
     * @param array<string, mixed> $meta
     */
    public static function line(
        string $key,
        string $title,
        string $category,
        array $rows,
        array $series,
        ?PresentationHints $presentation = null,
        ?string $description = null,
        array $meta = [],
    ): Chart {
        return self::tabular(
            type: ChartType::LINE,
            key: $key,
            title: $title,
            category: $category,
            rows: $rows,
            series: $series,
            presentation: $presentation,
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @param list<\Elqora\Chart\Series\Series> $series
     * @param array<string, mixed> $meta
     */
    public static function area(
        string $key,
        string $title,
        string $category,
        array $rows,
        array $series,
        ?PresentationHints $presentation = null,
        ?string $description = null,
        array $meta = [],
    ): Chart {
        return self::tabular(
            type: ChartType::AREA,
            key: $key,
            title: $title,
            category: $category,
            rows: $rows,
            series: $series,
            presentation: $presentation,
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @param list<\Elqora\Chart\Series\Series> $series
     * @param array<string, mixed> $meta
     */
    public static function bar(
        string $key,
        string $title,
        string $category,
        array $rows,
        array $series,
        ?PresentationHints $presentation = null,
        ?string $description = null,
        array $meta = [],
    ): Chart {
        return self::tabular(
            type: ChartType::BAR,
            key: $key,
            title: $title,
            category: $category,
            rows: $rows,
            series: $series,
            presentation: $presentation,
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @param array<string, mixed> $meta
     */
    public static function pie(
        string $key,
        string $title,
        string $category,
        string $value,
        array $rows,
        ?string $description = null,
        array $meta = [],
    ): Chart {
        return self::categoryValue(
            type: ChartType::PIE,
            key: $key,
            title: $title,
            category: $category,
            value: $value,
            rows: $rows,
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @param array<string, mixed> $meta
     */
    public static function doughnut(
        string $key,
        string $title,
        string $category,
        string $value,
        array $rows,
        ?string $description = null,
        array $meta = [],
    ): Chart {
        return self::categoryValue(
            type: ChartType::DOUGHNUT,
            key: $key,
            title: $title,
            category: $category,
            value: $value,
            rows: $rows,
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @param array<string, mixed> $meta
     */
    public static function scatter(
        string $key,
        string $title,
        string $x,
        string $y,
        array $rows,
        ?string $group = null,
        ?string $description = null,
        array $meta = [],
    ): Chart {
        return new Chart(
            key: $key,
            type: ChartType::SCATTER,
            title: $title,
            data: new CoordinateData(
                xField: $x,
                yField: $y,
                rows: $rows,
                groupField: $group,
            ),
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @param list<\Elqora\Chart\Series\Series> $series
     * @param array<string, mixed> $meta
     */
    private static function tabular(
        ChartType $type,
        string $key,
        string $title,
        string $category,
        array $rows,
        array $series,
        ?PresentationHints $presentation,
        ?string $description,
        array $meta,
    ): Chart {
        return new Chart(
            key: $key,
            type: $type,
            title: $title,
            data: new TabularData(
                categoryField: $category,
                rows: $rows,
                series: $series,
                presentation: $presentation,
            ),
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @param array<string, mixed> $meta
     */
    private static function categoryValue(
        ChartType $type,
        string $key,
        string $title,
        string $category,
        string $value,
        array $rows,
        ?string $description,
        array $meta,
    ): Chart {
        return new Chart(
            key: $key,
            type: $type,
            title: $title,
            data: new CategoryValueData(
                categoryField: $category,
                valueField: $value,
                rows: $rows,
            ),
            description: $description,
            meta: $meta,
        );
    }
}
