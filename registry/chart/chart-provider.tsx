import React from 'react';
import { ChartContext, ChartContextValue } from './chart-context';

export interface ChartProviderProps extends ChartContextValue {
  children: React.ReactNode;
}

export const ChartProvider: React.FC<ChartProviderProps> = ({
  children,
  palette,
  locale,
  className,
}) => {
  return (
    <ChartContext.Provider value={{ palette, locale, className }}>
      {children}
    </ChartContext.Provider>
  );
};
