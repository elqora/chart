import React from 'react';
import { SerializedChart } from '../types/chart';
import { getSeriesColor } from '../utils/palette';
import { formatChartValue } from '../utils/formatters';

export interface FlowRendererProps {
  chart: SerializedChart;
}

export const FlowRenderer: React.FC<FlowRendererProps> = ({ chart }) => {
  const chartData = chart.data || chart.payload || {};
  const stages = (chartData.stages as any[]) || (chartData.rows as any[]) || [];
  const presentation = chartData.presentation;

  const values = stages.map((item: any) => Number(item.value) || 0);
  const maxValue = Math.max(...values, 1);

  return (
    <div style={{ width: '100%', minHeight: '300px', display: 'flex', flexDirection: 'column', justifyContent: 'center', gap: '12px', padding: '16px', boxSizing: 'border-box' }}>
      {stages.map((item: any, idx: number) => {
        const stageName = String(item.label || item.key || `Stage ${idx + 1}`);
        const val = Number(item.value) || 0;
        const widthPercent = Math.max((val / maxValue) * 100, 15);

        return (
          <div key={item.key || idx} style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', width: '100%' }}>
            <div
              style={{
                width: `${widthPercent}%`,
                height: '44px',
                borderRadius: '10px',
                backgroundColor: getSeriesColor(idx),
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                padding: '0 16px',
                color: '#ffffff',
                fontWeight: 600,
                fontSize: '13px',
                boxShadow: '0 2px 4px rgba(0,0,0,0.2)',
                transition: 'all 0.2s ease',
                boxSizing: 'border-box',
              }}
            >
              <span style={{ whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{stageName}</span>
              <span style={{ fontWeight: 700, backgroundColor: 'rgba(0,0,0,0.25)', padding: '4px 10px', borderRadius: '6px', marginLeft: '12px' }}>
                {formatChartValue(val, { precision: presentation?.precision })}
              </span>
            </div>
          </div>
        );
      })}
    </div>
  );
};
