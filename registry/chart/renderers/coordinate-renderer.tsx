import React from 'react';
import {
  ResponsiveContainer as RechartsResponsiveContainer,
  ScatterChart as RechartsScatterChart,
  Scatter as RechartsScatter,
  XAxis as RechartsXAxis,
  YAxis as RechartsYAxis,
  ZAxis as RechartsZAxis,
  CartesianGrid as RechartsCartesianGrid,
  Tooltip as RechartsTooltip,
  Legend as RechartsLegend,
} from 'recharts';
import { SerializedChart } from '../types/chart';
import { ChartType } from '../types/enums';
import { getSeriesColor } from '../utils/palette';
import { formatChartValue } from '../utils/formatters';

const ResponsiveContainer = RechartsResponsiveContainer as any;
const ScatterChart = RechartsScatterChart as any;
const Scatter = RechartsScatter as any;
const XAxis = RechartsXAxis as any;
const YAxis = RechartsYAxis as any;
const ZAxis = RechartsZAxis as any;
const CartesianGrid = RechartsCartesianGrid as any;
const Tooltip = RechartsTooltip as any;
const Legend = RechartsLegend as any;

export interface CoordinateRendererProps {
  chart: SerializedChart;
}

export const CoordinateRenderer: React.FC<CoordinateRendererProps> = ({ chart }) => {
  const chartData = chart.data || chart.payload || {};
  const { type } = chart;
  const rows = (chartData.rows as any[]) || [];
  const dimensions = (chartData.dimensions as any[]) || [];
  const series = (chartData.series as any[]) || [];
  const presentation = chartData.presentation;

  const xKey = chartData.x_field || dimensions.find((d) => d.role === 'x')?.key || 'x';
  const yKey = chartData.y_field || dimensions.find((d) => d.role === 'y')?.key || 'y';

  const isBubble = type === ChartType.BUBBLE;
  const sizeKey = chartData.size_field || series.find((s) => s.key === 'size')?.field || 'size';

  return (
    <div style={{ width: '100%', height: '300px' }}>
      <ResponsiveContainer width="100%" height={300}>
        <ScatterChart>
          <CartesianGrid stroke="#334155" opacity={0.5} strokeDasharray="3 3" />
          <XAxis dataKey={xKey} name="X" stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
          <YAxis dataKey={yKey} name="Y" stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
          {isBubble && <ZAxis dataKey={sizeKey} range={[60, 400]} name="Size" />}
          <Tooltip
            cursor={{ strokeDasharray: '3 3' }}
            contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '8px', color: '#fff' }}
            formatter={(val: number, name: string) => [
              formatChartValue(val, { precision: presentation?.precision }),
              name,
            ]}
          />
          <Legend wrapperStyle={{ color: '#cbd5e1' }} />
          <Scatter
            name={chart.title || 'Scatter'}
            data={rows}
            fill={getSeriesColor(0)}
          />
        </ScatterChart>
      </ResponsiveContainer>
    </div>
  );
};
