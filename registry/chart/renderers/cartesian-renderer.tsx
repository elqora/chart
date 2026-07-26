import React from 'react';
import {
  ResponsiveContainer as RechartsResponsiveContainer,
  LineChart as RechartsLineChart,
  Line as RechartsLine,
  AreaChart as RechartsAreaChart,
  Area as RechartsArea,
  BarChart as RechartsBarChart,
  Bar as RechartsBar,
  XAxis as RechartsXAxis,
  YAxis as RechartsYAxis,
  CartesianGrid as RechartsCartesianGrid,
  Tooltip as RechartsTooltip,
  Legend as RechartsLegend,
} from 'recharts';
import { SerializedChart } from '../types/chart';
import { ChartType, Orientation, SparklineMode } from '../types/enums';
import { getSeriesColor } from '../utils/palette';
import { formatChartValue } from '../utils/formatters';

const ResponsiveContainer = RechartsResponsiveContainer as any;
const LineChart = RechartsLineChart as any;
const Line = RechartsLine as any;
const AreaChart = RechartsAreaChart as any;
const Area = RechartsArea as any;
const BarChart = RechartsBarChart as any;
const Bar = RechartsBar as any;
const XAxis = RechartsXAxis as any;
const YAxis = RechartsYAxis as any;
const CartesianGrid = RechartsCartesianGrid as any;
const Tooltip = RechartsTooltip as any;
const Legend = RechartsLegend as any;

export interface CartesianRendererProps {
  chart: SerializedChart;
}

export const CartesianRenderer: React.FC<CartesianRendererProps> = ({ chart }) => {
  const chartData = chart.data || chart.payload || {};
  const { type } = chart;
  const rows = (chartData.rows as any[]) || [];
  const series = (chartData.series as any[]) || [];
  const dimensions = (chartData.dimensions as any[]) || [];
  const presentation = chartData.presentation;

  const isHorizontal = presentation?.orientation === Orientation.HORIZONTAL;
  const categoryField = chartData.category_field || dimensions[0]?.key || 'category';
  const sparklineMode = presentation?.sparkline_mode || SparklineMode.LINE;
  const isSparkline = type === ChartType.SPARKLINE;
  const curveType = (presentation?.curve_type as any) || 'monotone';

  const renderChartBody = () => {
    if (isSparkline) {
      const margin = { top: 2, right: 2, left: 2, bottom: 2 };
      const tooltip = (
        <Tooltip
          contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '8px', color: '#fff', fontSize: '11px' }}
          formatter={(val: number, name: string) => [
            formatChartValue(val, { precision: presentation?.precision }),
            name,
          ]}
        />
      );

      if (sparklineMode === SparklineMode.AREA) {
        return (
          <AreaChart data={rows} margin={margin}>
            {tooltip}
            {series.map((s: any, idx: number) => (
              <Area
                key={s.key}
                type={curveType}
                dataKey={s.field}
                name={s.label || s.key}
                stroke={getSeriesColor(idx)}
                fill={getSeriesColor(idx)}
                fillOpacity={0.45}
                strokeWidth={2.5}
                dot={false}
              />
            ))}
          </AreaChart>
        );
      }

      if (sparklineMode === SparklineMode.BAR) {
        return (
          <BarChart data={rows} margin={margin}>
            {tooltip}
            {series.map((s: any, idx: number) => (
              <Bar
                key={s.key}
                dataKey={s.field}
                name={s.label || s.key}
                fill={getSeriesColor(idx)}
                radius={[3, 3, 0, 0]}
              />
            ))}
          </BarChart>
        );
      }

      return (
        <LineChart data={rows} margin={margin}>
          {tooltip}
          {series.map((s: any, idx: number) => (
            <Line
              key={s.key}
              type={curveType}
              dataKey={s.field}
              name={s.label || s.key}
              stroke={getSeriesColor(idx)}
              strokeWidth={2.5}
              dot={false}
            />
          ))}
        </LineChart>
      );
    }

    switch (type) {
      case ChartType.LINE:
        return (
          <LineChart data={rows} layout={isHorizontal ? 'vertical' : 'horizontal'}>
            <CartesianGrid stroke="#334155" opacity={0.5} strokeDasharray="3 3" />
            {isHorizontal ? (
              <>
                <YAxis dataKey={categoryField} type="category" stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
                <XAxis type="number" stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
              </>
            ) : (
              <>
                <XAxis dataKey={categoryField} stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
                <YAxis stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
              </>
            )}
            <Tooltip
              contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '8px', color: '#fff' }}
              formatter={(val: number, name: string) => [
                formatChartValue(val, { precision: presentation?.precision }),
                name,
              ]}
            />
            <Legend wrapperStyle={{ color: '#cbd5e1' }} />
            {series.map((s: any, idx: number) => (
              <Line
                key={s.key}
                type={curveType}
                dataKey={s.field}
                name={s.label || s.key}
                stroke={getSeriesColor(idx)}
                strokeWidth={3}
                dot={{ r: 4, fill: getSeriesColor(idx) }}
                activeDot={{ r: 7 }}
              />
            ))}
          </LineChart>
        );

      case ChartType.AREA:
        return (
          <AreaChart data={rows} layout={isHorizontal ? 'vertical' : 'horizontal'}>
            <CartesianGrid stroke="#334155" opacity={0.5} strokeDasharray="3 3" />
            {isHorizontal ? (
              <>
                <YAxis dataKey={categoryField} type="category" stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
                <XAxis type="number" stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
              </>
            ) : (
              <>
                <XAxis dataKey={categoryField} stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
                <YAxis stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
              </>
            )}
            <Tooltip
              contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '8px', color: '#fff' }}
              formatter={(val: number, name: string) => [
                formatChartValue(val, { precision: presentation?.precision }),
                name,
              ]}
            />
            <Legend wrapperStyle={{ color: '#cbd5e1' }} />
            {series.map((s: any, idx: number) => (
              <Area
                key={s.key}
                type={curveType}
                dataKey={s.field}
                name={s.label || s.key}
                stroke={getSeriesColor(idx)}
                fill={getSeriesColor(idx)}
                fillOpacity={0.4}
                stackId={s.stack || (presentation?.stacking ? 'stack' : undefined)}
              />
            ))}
          </AreaChart>
        );

      case ChartType.BAR:
      case ChartType.STACKED_BAR:
      default:
        return (
          <BarChart data={rows} layout={isHorizontal ? 'vertical' : 'horizontal'}>
            <CartesianGrid stroke="#334155" opacity={0.5} strokeDasharray="3 3" />
            {isHorizontal ? (
              <>
                <YAxis dataKey={categoryField} type="category" stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
                <XAxis type="number" stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
              </>
            ) : (
              <>
                <XAxis dataKey={categoryField} stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
                <YAxis stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
              </>
            )}
            <Tooltip
              contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '8px', color: '#fff' }}
              formatter={(val: number, name: string) => [
                formatChartValue(val, { precision: presentation?.precision }),
                name,
              ]}
            />
            <Legend wrapperStyle={{ color: '#cbd5e1' }} />
            {series.map((s: any, idx: number) => (
              <Bar
                key={s.key}
                dataKey={s.field}
                name={s.label || s.key}
                fill={getSeriesColor(idx)}
                stackId={s.stack || (type === ChartType.STACKED_BAR ? 'stack' : undefined)}
                radius={[4, 4, 0, 0]}
              />
            ))}
          </BarChart>
        );
    }
  };

  const height = isSparkline ? 60 : 300;

  return (
    <div style={{ width: '100%', height: `${height}px` }}>
      <ResponsiveContainer width="100%" height={height}>
        {renderChartBody()}
      </ResponsiveContainer>
    </div>
  );
};
