import React from 'react';
import {
  ResponsiveContainer as RechartsResponsiveContainer,
  RadarChart as RechartsRadarChart,
  Radar as RechartsRadar,
  PolarGrid as RechartsPolarGrid,
  PolarAngleAxis as RechartsPolarAngleAxis,
  PolarRadiusAxis as RechartsPolarRadiusAxis,
  Tooltip as RechartsTooltip,
  Legend as RechartsLegend,
} from 'recharts';
import { SerializedChart } from '../types/chart';
import { ChartType } from '../types/enums';
import { getSeriesColor } from '../utils/palette';
import { formatChartValue } from '../utils/formatters';

const ResponsiveContainer = RechartsResponsiveContainer as any;
const RadarChart = RechartsRadarChart as any;
const Radar = RechartsRadar as any;
const PolarGrid = RechartsPolarGrid as any;
const PolarAngleAxis = RechartsPolarAngleAxis as any;
const PolarRadiusAxis = RechartsPolarRadiusAxis as any;
const Tooltip = RechartsTooltip as any;
const Legend = RechartsLegend as any;

export interface RadialRendererProps {
  chart: SerializedChart;
}

export const RadialRenderer: React.FC<RadialRendererProps> = ({ chart }) => {
  const chartData = chart.data || chart.payload || {};
  const { type } = chart;
  const presentation = chartData.presentation;

  if (type === ChartType.GAUGE) {
    const value = typeof chartData.value === 'number' ? chartData.value : 0;
    const min = typeof chartData.minimum === 'number' ? chartData.minimum : 0;
    const max = typeof chartData.maximum === 'number' ? chartData.maximum : 100;
    const unit = chartData.unit || presentation?.unit || '%';

    const percent = Math.min(Math.max((value - min) / (max - min || 1), 0), 1);
    const angle = -180 + percent * 180;

    return (
      <div style={{ width: '100%', height: '300px', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center' }}>
        <svg viewBox="0 0 200 120" style={{ width: '280px', height: '170px' }}>
          <path
            d="M 20 100 A 80 80 0 0 1 180 100"
            fill="none"
            stroke="#1e293b"
            strokeWidth={18}
            strokeLinecap="round"
          />
          <path
            d="M 20 100 A 80 80 0 0 1 180 100"
            fill="none"
            stroke="#3b82f6"
            strokeWidth={18}
            strokeDasharray={251}
            strokeDashoffset={251 * (1 - percent)}
            strokeLinecap="round"
          />
          {/* Needle */}
          <g transform={`rotate(${angle}, 100, 100)`}>
            <line x1="100" y1="100" x2="35" y2="100" stroke="#60a5fa" strokeWidth="4" strokeLinecap="round" />
            <circle cx="100" cy="100" r="7" fill="#60a5fa" stroke="#1e293b" strokeWidth="2" />
          </g>
        </svg>
        <div style={{ marginTop: '-10px', textAlign: 'center' }}>
          <span style={{ fontSize: '26px', fontWeight: 800, color: '#f8fafc' }}>
            {formatChartValue(value, { precision: presentation?.precision })} {unit}
          </span>
          <p style={{ fontSize: '12px', color: '#94a3b8', margin: '4px 0 0 0' }}>
            Range: {min} - {max}
          </p>
        </div>
      </div>
    );
  }

  // Radar Chart
  // PHP RadarData serializes: indicators: [{key, label, maximum}], series: [{key, label, values: [80, 95, ...]}, ...]
  const indicators = (chartData.indicators as any[]) || [];
  const seriesList = (chartData.series as any[]) || [];

  const formattedRadarData = indicators.map((ind: any, iIdx: number) => {
    const row: Record<string, unknown> = { indicator: ind.label || ind.key || `Ind ${iIdx + 1}` };
    seriesList.forEach((s: any) => {
      if (Array.isArray(s.values)) {
        row[s.key] = s.values[iIdx];
      } else if (chartData.rows && chartData.rows[iIdx]) {
        row[s.key] = chartData.rows[iIdx][s.field || s.key];
      }
    });
    return row;
  });

  return (
    <div style={{ width: '100%', height: '300px' }}>
      <ResponsiveContainer width="100%" height={300}>
        <RadarChart data={formattedRadarData}>
          <PolarGrid stroke="#334155" opacity={0.6} />
          <PolarAngleAxis dataKey="indicator" stroke="#94a3b8" tick={{ fill: '#cbd5e1', fontSize: 12 }} />
          <PolarRadiusAxis stroke="#475569" opacity={0.6} />
          <Tooltip
            contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '8px', color: '#fff' }}
            formatter={(val: number, name: string) => [
              formatChartValue(val, { precision: presentation?.precision }),
              name,
            ]}
          />
          <Legend wrapperStyle={{ color: '#cbd5e1' }} />
          {seriesList.map((s: any, idx: number) => (
            <Radar
              key={s.key}
              name={s.label || s.key}
              dataKey={s.key}
              stroke={getSeriesColor(idx)}
              fill={getSeriesColor(idx)}
              fillOpacity={0.4}
            />
          ))}
        </RadarChart>
      </ResponsiveContainer>
    </div>
  );
};
