export const DEFAULT_CHART_COLORS = [
  'hsl(var(--chart-1, 220 70% 50%))',
  'hsl(var(--chart-2, 160 60% 45%))',
  'hsl(var(--chart-3, 30 80% 55%))',
  'hsl(var(--chart-4, 280 65% 60%))',
  'hsl(var(--chart-5, 340 75% 55%))',
  'hsl(210, 90%, 56%)',
  'hsl(145, 63%, 49%)',
  'hsl(36, 100%, 50%)',
  'hsl(280, 67%, 55%)',
  'hsl(0, 84%, 60%)',
];

export function getSeriesColor(index: number, customPalette?: string[]): string {
  const palette = customPalette && customPalette.length > 0 ? customPalette : DEFAULT_CHART_COLORS;
  return palette[index % palette.length];
}
