import { ValueType } from './enums';

export interface SerializedMeasure {
  key: string;
  label: string;
  field: string;
  value_type?: ValueType;
  unit?: string;
  currency?: string;
  precision?: number;
  stack?: string;
  meta?: Record<string, unknown>;
}

export interface SerializedSeries {
  key: string;
  label: string;
  field: string;
  value_type?: ValueType;
  unit?: string;
  currency?: string;
  precision?: number;
  stack?: string;
  meta?: Record<string, unknown>;
}
