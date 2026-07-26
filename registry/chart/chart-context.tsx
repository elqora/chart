import React, { createContext, useContext } from 'react';

export interface ChartContextValue {
  palette?: string[];
  locale?: string;
  className?: string;
}

export const ChartContext = createContext<ChartContextValue>({});

export function useChartContext(): ChartContextValue {
  return useContext(ChartContext);
}
