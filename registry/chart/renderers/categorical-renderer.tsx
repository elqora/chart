import React from 'react';
import {
  ResponsiveContainer as RechartsResponsiveContainer,
  PieChart as RechartsPieChart,
  Pie as RechartsPie,
  Cell as RechartsCell,
  Tooltip as RechartsTooltip,
  Legend as RechartsLegend,
} from 'recharts';
import { SerializedChart } from '../types/chart';
import { ChartType } from '../types/enums';
import { getSeriesColor } from '../utils/palette';
import { formatChartValue } from '../utils/formatters';

const ResponsiveContainer = RechartsResponsiveContainer as any;
const PieChart = RechartsPieChart as any;
const Pie = RechartsPie as any;
const Cell = RechartsCell as any;
const Tooltip = RechartsTooltip as any;
const Legend = RechartsLegend as any;

export interface CategoricalRendererProps {
  chart: SerializedChart;
  height?: number | string;
}

export const CategoricalRenderer: React.FC<CategoricalRendererProps> = ({ chart, height: customHeight }) => {
  const chartData = chart.data || chart.payload || {};
  const { type } = chart;
  const rows = chartData.rows || [];
  const categoryField = chartData.category_field || chartData.dimensions?.[0]?.key || 'category';
  const valueField = chartData.value_field || chartData.series?.[0]?.field || 'value';
  const presentation = chartData.presentation;

  const isDoughnut = type === ChartType.DOUGHNUT;
  const height = customHeight ?? 300;
  const containerHeight = typeof height === 'number' ? `${height}px` : height;

  return (
    <div style={{ width: '100%', height: containerHeight }}>
      <ResponsiveContainer width="100%" height={height}>
        <PieChart>
          <Tooltip
            formatter={(val: number, name: string) => [
              formatChartValue(val, { precision: presentation?.precision }),
              name,
            ]}
          />
          <Legend />
          <Pie
            data={rows}
            dataKey={valueField}
            nameKey={categoryField}
            cx="50%"
            cy="50%"
            outerRadius={95}
            innerRadius={isDoughnut ? 55 : 0}
            paddingAngle={isDoughnut ? 4 : 0}
            label={({ name, percent }: { name?: string; percent?: number }) =>
              name ? `${name}: ${((percent || 0) * 100).toFixed(0)}%` : ''
            }
          >
            {rows.map((_, idx) => (
              <Cell key={`cell-${idx}`} fill={getSeriesColor(idx)} />
            ))}
          </Pie>
        </PieChart>
      </ResponsiveContainer>
    </div>
  );
};
