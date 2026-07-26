import {
  ChartType,
  ChartFamily,
  Orientation,
  StackingMode,
  PercentageConvention,
  SparklineMode,
  CurveType,
} from './enums';
import { SerializedDimension } from './dimensions';
import { SerializedSeries } from './series';

export interface PortablePresentationHints {
  orientation?: Orientation;
  stacking?: StackingMode;
  percentage_convention?: PercentageConvention;
  unit?: string;
  currency?: string;
  precision?: number;
  connect_nulls?: boolean;
  sparkline_mode?: SparklineMode | string;
  curve_type?: CurveType | string;
}

export interface SerializedChartData {
  kind?: string;
  category_field?: string;
  value_field?: string;
  x_field?: string;
  y_field?: string;
  size_field?: string;
  group_field?: string;
  rows?: Record<string, unknown>[];
  series?: SerializedSeries[];
  dimensions?: SerializedDimension[];
  presentation?: PortablePresentationHints;
  // Gauge
  value?: number;
  min?: number;
  max?: number;
  unit?: string;
  ranges?: { label?: string; min?: number; max?: number }[];
  // Radar
  indicators?: { key: string; label: string; max?: number }[];
  radarSeries?: { key: string; label: string; values: number[] }[];
  // Funnel
  stages?: { key: string; label: string; value: number }[];
  // Hierarchy
  roots?: { key: string; label: string; value?: number; children?: any[] }[];
  // BoxPlot
  items?: { category: string; min: number; lower_quartile: number; median: number; upper_quartile: number; max: number; outliers?: number[] }[];
  // Candlestick
  points?: { category: string; open: number; high: number; low: number; close: number }[];
  [key: string]: unknown;
}

export interface SerializedChart {
  key: string;
  type: ChartType | string;
  family: ChartFamily | string;
  title: string;
  description?: string;
  data: SerializedChartData;
  payload?: SerializedChartData; // Fallback alias
  meta?: Record<string, unknown>;
}
