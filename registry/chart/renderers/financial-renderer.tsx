import React from 'react';
import { SerializedChart } from '../types/chart';
import { formatChartValue } from '../utils/formatters';

export interface FinancialRendererProps {
  chart: SerializedChart;
  height?: number | string;
}

export const FinancialRenderer: React.FC<FinancialRendererProps> = ({ chart, height: customHeight }) => {
  const chartData = chart.data || chart.payload || {};
  const points = (chartData.points as any[]) || (chartData.rows as any[]) || [];
  const presentation = chartData.presentation;

  const allHighs = points.map((p: any) => Number(p.high) || 0);
  const allLows = points.map((p: any) => Number(p.low) || 0);
  const maxVal = Math.max(...allHighs, 1);
  const minVal = Math.min(...allLows, 0);
  const totalRange = maxVal - minVal || 1;

  const height = customHeight ?? 300;
  const containerHeight = typeof height === 'number' ? `${height}px` : height;

  return (
    <div
      style={{
        width: '100%',
        height: containerHeight,
        display: 'flex',
        alignItems: 'flex-end',
        justifyContent: 'space-around',
        gap: '12px',
        padding: '16px 24px',
        boxSizing: 'border-box',
        backgroundColor: '#090d16',
        borderRadius: '10px',
        border: '1px solid #1e293b',
      }}
    >
      {points.map((item: any, idx: number) => {
        const open = Number(item.open) || 0;
        const high = Number(item.high) || 0;
        const low = Number(item.low) || 0;
        const close = Number(item.close) || 0;

        const isBullish = close >= open;
        const bodyColor = isBullish ? '#10b981' : '#f43f5e';
        const wickColor = isBullish ? '#34d399' : '#fb7185';

        const highPct = ((high - minVal) / totalRange) * 100;
        const lowPct = ((low - minVal) / totalRange) * 100;
        const topVal = Math.max(open, close);
        const bottomVal = Math.min(open, close);
        const bodyTopPct = ((topVal - minVal) / totalRange) * 100;
        const bodyBottomPct = ((bottomVal - minVal) / totalRange) * 100;
        const bodyHeightPct = Math.max(bodyTopPct - bodyBottomPct, 2);

        return (
          <div
            key={item.category || idx}
            style={{
              display: 'flex',
              flexDirection: 'column',
              alignItems: 'center',
              flex: 1,
              height: '100%',
              position: 'relative',
            }}
            title={`${item.category}: Open ${formatChartValue(open, {
              precision: presentation?.precision,
            })}, High ${formatChartValue(high, {
              precision: presentation?.precision,
            })}, Low ${formatChartValue(low, {
              precision: presentation?.precision,
            })}, Close ${formatChartValue(close, {
              precision: presentation?.precision,
            })}`}
          >
            {/* Chart Candle Plotting Area */}
            <div style={{ position: 'relative', width: '100%', height: '220px', display: 'flex', justifyContent: 'center' }}>
              {/* High-Low Wick Line */}
              <div
                style={{
                  position: 'absolute',
                  width: '2px',
                  backgroundColor: wickColor,
                  bottom: `${lowPct}%`,
                  height: `${Math.max(highPct - lowPct, 2)}%`,
                }}
              />
              {/* Open-Close Candle Body */}
              <div
                style={{
                  position: 'absolute',
                  width: '24px',
                  borderRadius: '3px',
                  backgroundColor: bodyColor,
                  border: `1px solid ${wickColor}`,
                  bottom: `${bodyBottomPct}%`,
                  height: `${bodyHeightPct}%`,
                  boxShadow: '0 2px 4px rgba(0,0,0,0.3)',
                  transition: 'transform 0.15s ease',
                }}
              />
            </div>
            {/* Date / Category Label */}
            <div style={{ marginTop: '8px', textAlign: 'center' }}>
              <span style={{ fontSize: '11px', fontWeight: 600, color: '#94a3b8' }}>
                {item.category}
              </span>
              <div style={{ fontSize: '10px', color: bodyColor, fontWeight: 700, marginTop: '2px' }}>
                {formatChartValue(close, { precision: presentation?.precision })}
              </div>
            </div>
          </div>
        );
      })}
    </div>
  );
};
