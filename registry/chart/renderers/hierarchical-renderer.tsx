import React from 'react';
import { SerializedChart } from '../types/chart';
import { ChartType } from '../types/enums';
import { getSeriesColor } from '../utils/palette';
import { formatChartValue } from '../utils/formatters';

export interface HierarchicalRendererProps {
  chart: SerializedChart;
  height?: number | string;
}

export const HierarchicalRenderer: React.FC<HierarchicalRendererProps> = ({ chart, height: customHeight }) => {
  const chartData = chart.data || chart.payload || {};
  const { type } = chart;
  const roots = (chartData.roots as any[]) || (chartData.rows as any[]) || [];
  const presentation = chartData.presentation;

  const height = customHeight ?? 300;
  const containerHeight = typeof height === 'number' ? `${height}px` : height;

  // Flatten nodes for Treemap tile visualization
  const flattenNodes = (nodes: any[]): { key: string; label: string; value: number; depth: number }[] => {
    const list: { key: string; label: string; value: number; depth: number }[] = [];
    const traverse = (nodeList: any[], depth = 0) => {
      nodeList.forEach((node) => {
        if (node.value !== undefined && node.value > 0) {
          list.push({
            key: node.key || node.label,
            label: node.label || node.key,
            value: Number(node.value),
            depth,
          });
        }
        if (node.children && node.children.length > 0) {
          traverse(node.children, depth + 1);
        }
      });
    };
    traverse(nodes);
    return list;
  };

  const flatItems = flattenNodes(roots);
  const totalValue = flatItems.reduce((acc, item) => acc + item.value, 0) || 1;

  if (type === ChartType.TREEMAP) {
    return (
      <div style={{ width: '100%', minHeight: containerHeight, display: 'flex', flexWrap: 'wrap', gap: '8px', padding: '16px', boxSizing: 'border-box' }}>
        {flatItems.map((item, idx) => {
          const flexGrow = Math.max(Math.round((item.value / totalValue) * 100), 1);
          return (
            <div
              key={item.key + idx}
              style={{
                flexGrow: flexGrow,
                minWidth: '120px',
                height: '110px',
                backgroundColor: getSeriesColor(idx),
                borderRadius: '10px',
                padding: '12px',
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'space-between',
                color: '#ffffff',
                boxShadow: '0 2px 4px rgba(0,0,0,0.2)',
                boxSizing: 'border-box',
                transition: 'transform 0.15s ease',
              }}
            >
              <span style={{ fontSize: '13px', fontWeight: 600 }}>{item.label}</span>
              <span style={{ fontSize: '18px', fontWeight: 800 }}>
                {formatChartValue(item.value, { precision: presentation?.precision })}
              </span>
            </div>
          );
        })}
      </div>
    );
  }

  // Sunburst / Hierarchy Tree Node View
  const renderTreeNodes = (nodes: any[], depth = 0) => (
    <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', marginLeft: depth > 0 ? '16px' : '0', borderLeft: depth > 0 ? '2px solid #334155' : 'none', paddingLeft: depth > 0 ? '12px' : '0' }}>
      {nodes.map((node: any, idx: number) => (
        <div key={node.key || idx} style={{ backgroundColor: '#1e293b', border: '1px solid #334155', borderRadius: '10px', padding: '12px', color: '#f8fafc' }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', fontSize: '13px', fontWeight: 600 }}>
            <span style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
              <span style={{ width: '10px', height: '10px', borderRadius: '50%', backgroundColor: getSeriesColor(idx + depth) }} />
              {node.label || node.key}
            </span>
            {node.value !== undefined && (
              <span style={{ fontWeight: 800, backgroundColor: '#0f172a', padding: '4px 10px', borderRadius: '6px', color: '#60a5fa' }}>
                {formatChartValue(node.value, { precision: presentation?.precision })}
              </span>
            )}
          </div>
          {node.children && node.children.length > 0 && (
            <div style={{ marginTop: '10px' }}>
              {renderTreeNodes(node.children, depth + 1)}
            </div>
          )}
        </div>
      ))}
    </div>
  );

  return <div style={{ width: '100%', minHeight: containerHeight, padding: '16px', boxSizing: 'border-box' }}>{renderTreeNodes(roots)}</div>;
};
