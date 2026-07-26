import React from 'react';
import { SerializedChart } from '../types/chart';
import { getSeriesColor } from '../utils/palette';
import { formatChartValue } from '../utils/formatters';

export interface StatisticalRendererProps {
  chart: SerializedChart;
  height?: number | string;
}

export const StatisticalRenderer: React.FC<StatisticalRendererProps> = ({ chart, height: customHeight }) => {
  const chartData = chart.data || chart.payload || {};
  const items = (chartData.items as any[]) || (chartData.rows as any[]) || [];
  const presentation = chartData.presentation;

  const height = customHeight ?? 300;
  const containerHeight = typeof height === 'number' ? `${height}px` : height;

  return (
    <div style={{ width: '100%', minHeight: containerHeight, display: 'flex', flexDirection: 'column', gap: '20px', padding: '16px', boxSizing: 'border-box' }}>
      {items.map((item: any, idx: number) => {
        const min = typeof item.minimum === 'number' ? item.minimum : (typeof item.min === 'number' ? item.min : 0);
        const max = typeof item.maximum === 'number' ? item.maximum : (typeof item.max === 'number' ? item.max : 100);
        const q1 = typeof item.lower_quartile === 'number' ? item.lower_quartile : (typeof item.lowerQuartile === 'number' ? item.lowerQuartile : (typeof item.q1 === 'number' ? item.q1 : min));
        const median = typeof item.median === 'number' ? item.median : (min + max) / 2;
        const q3 = typeof item.upper_quartile === 'number' ? item.upper_quartile : (typeof item.upperQuartile === 'number' ? item.upperQuartile : (typeof item.q3 === 'number' ? item.q3 : max));

        const range = max - min || 1;
        const q1Pct = Math.min(Math.max(((q1 - min) / range) * 100, 0), 100);
        const q3Pct = Math.min(Math.max(((q3 - min) / range) * 100, 0), 100);

        return (
          <div key={item.category || idx} style={{ display: 'flex', flexDirection: 'column', gap: '6px', width: '100%' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '12px', fontWeight: 600, color: '#94a3b8' }}>
              <span style={{ color: '#f8fafc', fontWeight: 700 }}>{item.category || `Item ${idx + 1}`}</span>
              <span>
                Min: {formatChartValue(min, { precision: presentation?.precision })} | Q1: {formatChartValue(q1, { precision: presentation?.precision })} | Median: {formatChartValue(median, { precision: presentation?.precision })} | Q3: {formatChartValue(q3, { precision: presentation?.precision })} | Max: {formatChartValue(max, { precision: presentation?.precision })}
              </span>
            </div>
            {/* Boxplot Whisker & Box bar */}
            <div style={{ position: 'relative', width: '100%', height: '36px', display: 'flex', alignItems: 'center', backgroundColor: '#1e293b', borderRadius: '8px', padding: '0 16px', boxSizing: 'border-box' }}>
              {/* Main Whisker line */}
              <div style={{ position: 'absolute', left: '16px', right: '16px', height: '2px', backgroundColor: '#64748b' }} />

              {/* Min & Max Cap Lines */}
              <div style={{ position: 'absolute', left: '16px', height: '16px', width: '2px', backgroundColor: '#64748b' }} />
              <div style={{ position: 'absolute', right: '16px', height: '16px', width: '2px', backgroundColor: '#64748b' }} />

              {/* IQR Box */}
              <div
                style={{
                  position: 'absolute',
                  height: '24px',
                  borderRadius: '6px',
                  border: '1px solid rgba(255,255,255,0.3)',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  left: `calc(16px + (100% - 32px) * ${q1Pct / 100})`,
                  width: `calc((100% - 32px) * ${Math.max((q3Pct - q1Pct) / 100, 0.05)})`,
                  backgroundColor: getSeriesColor(idx),
                  boxShadow: '0 2px 4px rgba(0,0,0,0.3)',
                }}
              >
                {/* Median Line */}
                <div
                  style={{
                    position: 'absolute',
                    top: 0,
                    bottom: 0,
                    width: '3px',
                    backgroundColor: '#ffffff',
                    left: `${((median - q1) / (q3 - q1 || 1)) * 100}%`,
                    boxShadow: '0 0 4px rgba(0,0,0,0.5)',
                  }}
                />
              </div>
            </div>
          </div>
        );
      })}
    </div>
  );
};
