import React from 'react';
import { SerializedChart } from '../types/chart';
import { formatChartValue } from '../utils/formatters';

export interface MatrixRendererProps {
  chart: SerializedChart;
  height?: number | string;
}

export const MatrixRenderer: React.FC<MatrixRendererProps> = ({ chart, height: customHeight }) => {
  const chartData = chart.data || chart.payload || {};
  const rows = (chartData.rows as Record<string, unknown>[]) || [];
  const presentation = chartData.presentation;

  const xField = chartData.x_field || 'x';
  const yField = chartData.y_field || 'y';
  const valueField = chartData.value_field || 'value';

  const xCategories: string[] = Array.from(new Set(rows.map((p) => String(p[xField]))));
  const yCategories: string[] = Array.from(new Set(rows.map((p) => String(p[yField]))));

  const values = rows.map((p) => Number(p[valueField]) || 0);
  const maxVal = Math.max(...values, 1);
  const minVal = Math.min(...values, 0);

  const height = customHeight ?? 300;
  const containerHeight = typeof height === 'number' ? `${height}px` : height;

  const getCellVal = (x: string, y: string): number | null => {
    const item = rows.find((p) => String(p[xField]) === x && String(p[yField]) === y);
    return item && typeof item[valueField] === 'number' ? (item[valueField] as number) : null;
  };

  return (
    <div style={{ width: '100%', minHeight: containerHeight, overflowX: 'auto', padding: '16px', boxSizing: 'border-box' }}>
      <table style={{ width: '100%', borderCollapse: 'separate', borderSpacing: '6px', fontSize: '13px' }}>
        <thead>
          <tr>
            <th style={{ padding: '8px', color: '#94a3b8', textAlign: 'right' }}></th>
            {xCategories.map((x) => (
              <th key={x} style={{ padding: '8px 12px', color: '#cbd5e1', fontWeight: 600, textAlign: 'center', backgroundColor: '#1e293b', borderRadius: '6px' }}>
                {x}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {yCategories.map((y) => (
            <tr key={y}>
              <td style={{ padding: '8px 12px', color: '#cbd5e1', fontWeight: 600, textAlign: 'right', backgroundColor: '#1e293b', borderRadius: '6px' }}>{y}</td>
              {xCategories.map((x) => {
                const val = getCellVal(x, y);
                const opacity =
                  val !== null ? Math.max(0.2, (val - minVal) / (maxVal - minVal || 1)) : 0;
                return (
                  <td
                    key={x}
                    style={{
                      padding: '14px 12px',
                      textAlign: 'center',
                      fontWeight: 700,
                      color: '#ffffff',
                      borderRadius: '8px',
                      backgroundColor:
                        val !== null ? `rgba(59, 130, 246, ${opacity})` : '#0f172a',
                      border: '1px solid #1e293b',
                      transition: 'all 0.15s ease',
                    }}
                    title={`${y} x ${x}: ${val !== null ? val : '-'}`}
                  >
                    {val !== null ? formatChartValue(val, { precision: presentation?.precision }) : '-'}
                  </td>
                );
              })}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};
