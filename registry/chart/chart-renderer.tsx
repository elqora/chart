import React from 'react';
import { SerializedChart } from './types/chart';
import { ChartFamily, ChartType } from './types/enums';
import { CartesianRenderer } from './renderers/cartesian-renderer';
import { CategoricalRenderer } from './renderers/categorical-renderer';
import { CoordinateRenderer } from './renderers/coordinate-renderer';
import { RadialRenderer } from './renderers/radial-renderer';
import { FlowRenderer } from './renderers/flow-renderer';
import { MatrixRenderer } from './renderers/matrix-renderer';
import { HierarchicalRenderer } from './renderers/hierarchical-renderer';
import { StatisticalRenderer } from './renderers/statistical-renderer';
import { FinancialRenderer } from './renderers/financial-renderer';

export interface ChartRendererProps {
  chart: SerializedChart;
  className?: string;
}

export const ChartRenderer: React.FC<ChartRendererProps> = ({ chart, className = '' }) => {
  if (!chart || !chart.type) {
    return (
      <div
        style={{
          padding: '16px',
          fontSize: '12px',
          color: '#94a3b8',
          border: '1px solid #334155',
          borderRadius: '8px',
        }}
      >
        No valid chart payload provided.
      </div>
    );
  }

  const renderContent = () => {
    const family = chart.family;
    const type = chart.type;

    switch (family) {
      case ChartFamily.CARTESIAN:
        return <CartesianRenderer chart={chart} />;
      case ChartFamily.CATEGORICAL:
        return <CategoricalRenderer chart={chart} />;
      case ChartFamily.COORDINATE:
        return <CoordinateRenderer chart={chart} />;
      case ChartFamily.RADIAL:
        return <RadialRenderer chart={chart} />;
      case ChartFamily.FLOW:
        return <FlowRenderer chart={chart} />;
      case ChartFamily.MATRIX:
        return <MatrixRenderer chart={chart} />;
      case ChartFamily.HIERARCHICAL:
        return <HierarchicalRenderer chart={chart} />;
      case ChartFamily.STATISTICAL:
        return <StatisticalRenderer chart={chart} />;
      case ChartFamily.FINANCIAL:
        return <FinancialRenderer chart={chart} />;
      default:
        if ([ChartType.LINE, ChartType.AREA, ChartType.BAR, ChartType.STACKED_BAR, ChartType.SPARKLINE].includes(type as ChartType)) {
          return <CartesianRenderer chart={chart} />;
        }
        if ([ChartType.PIE, ChartType.DOUGHNUT].includes(type as ChartType)) {
          return <CategoricalRenderer chart={chart} />;
        }
        return (
          <div
            style={{
              padding: '16px',
              fontSize: '12px',
              color: '#f59e0b',
              border: '1px solid #78350f',
              backgroundColor: 'rgba(120, 53, 15, 0.2)',
              borderRadius: '8px',
            }}
          >
            Unsupported chart family: {family} ({type})
          </div>
        );
    }
  };

  return (
    <div
      style={{
        width: '100%',
        border: '1px solid #1e293b',
        borderRadius: '12px',
        backgroundColor: '#0f172a',
        color: '#f8fafc',
        boxShadow: '0 1px 3px rgba(0,0,0,0.2)',
        padding: '16px',
        boxSizing: 'border-box',
      }}
      className={className}
    >
      {chart.title && (
        <div style={{ marginBottom: '16px' }}>
          <h3 style={{ fontSize: '16px', fontWeight: 600, margin: 0, color: '#f8fafc' }}>
            {chart.title}
          </h3>
          {chart.description && (
            <p style={{ fontSize: '12px', color: '#94a3b8', margin: '4px 0 0 0' }}>
              {chart.description}
            </p>
          )}
        </div>
      )}
      {renderContent()}
    </div>
  );
};
