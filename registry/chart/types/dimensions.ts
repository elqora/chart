import { DimensionRole, ValueType } from './enums';

export interface SerializedDimension {
  key: string;
  label: string;
  role: DimensionRole;
  value_type: ValueType;
  meta?: Record<string, unknown>;
}
