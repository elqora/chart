export enum ChartType {
  LINE = 'line',
  AREA = 'area',
  BAR = 'bar',
  STACKED_BAR = 'stacked_bar',
  SPARKLINE = 'sparkline',
  PIE = 'pie',
  DOUGHNUT = 'doughnut',
  SCATTER = 'scatter',
  BUBBLE = 'bubble',
  RADAR = 'radar',
  GAUGE = 'gauge',
  FUNNEL = 'funnel',
  HEATMAP = 'heatmap',
  TREEMAP = 'treemap',
  SUNBURST = 'sunburst',
  BOX_PLOT = 'box_plot',
  CANDLESTICK = 'candlestick',
}

export enum SparklineMode {
  LINE = 'line',
  AREA = 'area',
  BAR = 'bar',
}

export enum CurveType {
  MONOTONE = 'monotone',
  LINEAR = 'linear',
  STEP = 'step',
  SMOOTH = 'smooth',
}

export enum ChartFamily {
  CARTESIAN = 'cartesian',
  CATEGORICAL = 'categorical',
  COORDINATE = 'coordinate',
  RADIAL = 'radial',
  FLOW = 'flow',
  MATRIX = 'matrix',
  HIERARCHICAL = 'hierarchical',
  STATISTICAL = 'statistical',
  FINANCIAL = 'financial',
}

export enum Orientation {
  VERTICAL = 'vertical',
  HORIZONTAL = 'horizontal',
}

export enum StackingMode {
  NORMAL = 'normal',
  PERCENT = 'percent',
}

export enum PercentageConvention {
  PERCENTAGE = 'percentage',
  DECIMAL = 'decimal',
}

export enum ValueType {
  STRING = 'string',
  CATEGORY = 'category',
  INTEGER = 'integer',
  NUMBER = 'number',
  PERCENTAGE = 'percentage',
  CURRENCY = 'currency',
  DURATION = 'duration',
  DATE = 'date',
  DATETIME = 'datetime',
  BOOLEAN = 'boolean',
}

export enum DimensionRole {
  CATEGORY = 'category',
  X = 'x',
  Y = 'y',
  TIME = 'time',
  GROUP = 'group',
}
