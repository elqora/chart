import { ValueType } from '../types/enums';

export interface FormatValueOptions {
  valueType?: ValueType;
  currency?: string;
  precision?: number;
  unit?: string;
  prefix?: string;
  suffix?: string;
}

export function formatChartValue(
  value: number | string | null | undefined,
  options: FormatValueOptions = {}
): string {
  if (value === null || value === undefined) {
    return '-';
  }

  if (typeof value === 'string') {
    return value;
  }

  const { valueType, currency = 'USD', precision = 2, unit, prefix = '', suffix = '' } = options;

  let formatted = '';

  switch (valueType) {
    case ValueType.CURRENCY:
      try {
        formatted = new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency,
          minimumFractionDigits: precision,
          maximumFractionDigits: precision,
        }).format(value);
      } catch {
        formatted = `${currency} ${value.toFixed(precision)}`;
      }
      break;

    case ValueType.PERCENTAGE:
      formatted = `${(value * (options.precision !== undefined ? 1 : 100)).toFixed(
        precision
      )}%`;
      break;

    case ValueType.INTEGER:
      formatted = Math.round(value).toLocaleString();
      break;

    case ValueType.DURATION:
      formatted = `${value}s`;
      break;

    case ValueType.NUMBER:
    default:
      formatted = value.toLocaleString(undefined, {
        minimumFractionDigits: options.precision !== undefined ? precision : 0,
        maximumFractionDigits: options.precision !== undefined ? precision : 2,
      });
      break;
  }

  const unitSuffix = unit ? ` ${unit}` : '';
  return `${prefix}${formatted}${unitSuffix}${suffix}`;
}
