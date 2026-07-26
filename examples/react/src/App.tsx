import React, { useState } from 'react';
import { ChartRenderer } from '@/chart/chart-renderer';
import { ChartFamily, SerializedChart } from '@/chart/types';
import chartFixtures from './fixtures/charts.json';
import { BarChart3, Code2, Layers, Search, Sparkles } from 'lucide-react';

interface FixtureItem {
  fixtureKey: string;
  chart: SerializedChart;
}

export default function App() {
  const allCharts: FixtureItem[] = Object.entries(chartFixtures).map(([key, chart]) => ({
    fixtureKey: key,
    chart: chart as unknown as SerializedChart,
  }));

  const [selectedFamily, setSelectedFamily] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [showJsonMap, setShowJsonMap] = useState<Record<string, boolean>>({});

  const families = ['all', ...Object.values(ChartFamily)];

  const filteredCharts = allCharts.filter(({ chart }) => {
    const matchesFamily = selectedFamily === 'all' || chart.family === selectedFamily;
    const matchesSearch =
      (chart.title || '').toLowerCase().includes(searchQuery.toLowerCase()) ||
      (chart.type || '').toLowerCase().includes(searchQuery.toLowerCase()) ||
      (chart.key || '').toLowerCase().includes(searchQuery.toLowerCase());
    return matchesFamily && matchesSearch;
  });

  const toggleJson = (key: string) => {
    setShowJsonMap((prev) => ({ ...prev, [key]: !prev[key] }));
  };

  return (
    <div
      style={{
        minHeight: '100vh',
        backgroundColor: '#090d16',
        color: '#f8fafc',
        fontFamily: "'Inter', system-ui, -apple-system, sans-serif",
        display: 'flex',
        flexDirection: 'column',
      }}
    >
      {/* Header */}
      <header
        style={{
          borderBottom: '1px solid #1e293b',
          backgroundColor: 'rgba(15, 23, 42, 0.8)',
          backdropFilter: 'blur(8px)',
          position: 'sticky',
          top: 0,
          zIndex: 50,
          padding: '16px 24px',
        }}
      >
        <div
          style={{
            maxWidth: '1280px',
            margin: '0 auto',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
          }}
        >
          <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
            <div
              style={{
                padding: '10px',
                backgroundColor: 'rgba(59, 130, 246, 0.15)',
                color: '#60a5fa',
                borderRadius: '12px',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
              }}
            >
              <BarChart3 size={24} />
            </div>
            <div>
              <h1 style={{ fontSize: '20px', fontWeight: 700, margin: 0, letterSpacing: '-0.02em' }}>
                Elqora Chart Playground
              </h1>
              <p style={{ fontSize: '12px', color: '#94a3b8', margin: '2px 0 0 0' }}>
                React Host Implementation testing PHP Wire Protocol Payloads
              </p>
            </div>
          </div>
          <div
            style={{
              display: 'flex',
              alignItems: 'center',
              gap: '8px',
              backgroundColor: '#1e293b',
              padding: '6px 14px',
              borderRadius: '8px',
              border: '1px solid #334155',
            }}
          >
            <Sparkles size={14} color="#60a5fa" />
            <span style={{ fontSize: '12px', fontWeight: 600, color: '#e2e8f0' }}>
              {allCharts.length} Chart Types Loaded from PHP
            </span>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main
        style={{
          maxWidth: '1280px',
          margin: '0 auto',
          padding: '32px 24px',
          width: '100%',
          flex: 1,
          display: 'flex',
          flexDirection: 'column',
          gap: '24px',
          boxSizing: 'border-box',
        }}
      >
        {/* Filters & Search Bar */}
        <div
          style={{
            display: 'flex',
            flexWrap: 'wrap',
            alignItems: 'center',
            justifyContent: 'space-between',
            gap: '16px',
            backgroundColor: '#0f172a',
            padding: '16px',
            borderRadius: '16px',
            border: '1px solid #1e293b',
          }}
        >
          {/* Family Tabs */}
          <div style={{ display: 'flex', alignItems: 'center', gap: '6px', overflowX: 'auto', maxWidth: '100%' }}>
            {families.map((fam) => {
              const isSelected = selectedFamily === fam;
              return (
                <button
                  key={fam}
                  onClick={() => setSelectedFamily(fam)}
                  style={{
                    padding: '8px 14px',
                    borderRadius: '8px',
                    fontSize: '12px',
                    fontWeight: 600,
                    textTransform: 'capitalize',
                    whiteSpace: 'nowrap',
                    cursor: 'pointer',
                    transition: 'all 0.15s ease',
                    border: isSelected ? 'none' : '1px solid #334155',
                    backgroundColor: isSelected ? '#2563eb' : '#1e293b',
                    color: isSelected ? '#ffffff' : '#94a3b8',
                  }}
                >
                  {fam}
                </button>
              );
            })}
          </div>

          {/* Search Box */}
          <div style={{ position: 'relative', minWidth: '240px' }}>
            <Search
              size={16}
              style={{
                position: 'absolute',
                left: '12px',
                top: '50%',
                transform: 'translateY(-50%)',
                color: '#64748b',
              }}
            />
            <input
              type="text"
              placeholder="Search chart type..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              style={{
                width: '100%',
                backgroundColor: '#1e293b',
                border: '1px solid #334155',
                borderRadius: '8px',
                padding: '8px 12px 8px 36px',
                fontSize: '12px',
                color: '#ffffff',
                outline: 'none',
                boxSizing: 'border-box',
              }}
            />
          </div>
        </div>

        {/* Chart Grid */}
        {/* KPI Sparklines Showcase Section */}
        {(selectedFamily === 'all' || selectedFamily === 'cartesian') && !searchQuery && (
          <div style={{ marginBottom: '8px' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '14px' }}>
              <Sparkles size={18} style={{ color: '#3b82f6' }} />
              <h2 style={{ fontSize: '16px', fontWeight: 600, margin: 0, color: '#f8fafc' }}>
                Sparkline Micro-Charts in KPI Metric Cards
              </h2>
            </div>
            <div
              style={{
                display: 'grid',
                gridTemplateColumns: 'repeat(auto-fit, minmax(260px, 1fr))',
                gap: '16px',
              }}
            >
              {/* Monotone Sparkline Card */}
              {chartFixtures.sparkline_line && (
                <div
                  style={{
                    backgroundColor: '#0f172a',
                    border: '1px solid #1e293b',
                    borderRadius: '12px',
                    padding: '16px',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'space-between',
                  }}
                >
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '8px' }}>
                    <div>
                      <span style={{ fontSize: '12px', color: '#94a3b8', fontWeight: 500 }}>Total Revenue</span>
                      <h4 style={{ fontSize: '22px', fontWeight: 700, margin: '4px 0 0 0', color: '#f8fafc' }}>$142,500</h4>
                    </div>
                    <span style={{ fontSize: '12px', fontWeight: 600, color: '#22c55e', backgroundColor: 'rgba(34, 197, 94, 0.15)', padding: '2px 8px', borderRadius: '6px' }}>
                      +12.4%
                    </span>
                  </div>
                  <div style={{ marginTop: '8px' }}>
                    <div style={{ fontSize: '10px', color: '#64748b', marginBottom: '4px' }}>Mode: Line (Monotone)</div>
                    <ChartRenderer chart={chartFixtures.sparkline_line as any} />
                  </div>
                </div>
              )}

              {/* Linear Sparkline Card */}
              {chartFixtures.sparkline_linear && (
                <div
                  style={{
                    backgroundColor: '#0f172a',
                    border: '1px solid #1e293b',
                    borderRadius: '12px',
                    padding: '16px',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'space-between',
                  }}
                >
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '8px' }}>
                    <div>
                      <span style={{ fontSize: '12px', color: '#94a3b8', fontWeight: 500 }}>System Spikes</span>
                      <h4 style={{ fontSize: '22px', fontWeight: 700, margin: '4px 0 0 0', color: '#f8fafc' }}>1,350 ops/s</h4>
                    </div>
                    <span style={{ fontSize: '12px', fontWeight: 600, color: '#f59e0b', backgroundColor: 'rgba(245, 158, 11, 0.15)', padding: '2px 8px', borderRadius: '6px' }}>
                      +45.0%
                    </span>
                  </div>
                  <div style={{ marginTop: '8px' }}>
                    <div style={{ fontSize: '10px', color: '#64748b', marginBottom: '4px' }}>Mode: Line (Linear / Sharp)</div>
                    <ChartRenderer chart={chartFixtures.sparkline_linear as any} />
                  </div>
                </div>
              )}

              {/* Area Sparkline Card */}
              {chartFixtures.sparkline_area && (
                <div
                  style={{
                    backgroundColor: '#0f172a',
                    border: '1px solid #1e293b',
                    borderRadius: '12px',
                    padding: '16px',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'space-between',
                  }}
                >
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '8px' }}>
                    <div>
                      <span style={{ fontSize: '12px', color: '#94a3b8', fontWeight: 500 }}>Active Users</span>
                      <h4 style={{ fontSize: '22px', fontWeight: 700, margin: '4px 0 0 0', color: '#f8fafc' }}>4,890</h4>
                    </div>
                    <span style={{ fontSize: '12px', fontWeight: 600, color: '#22c55e', backgroundColor: 'rgba(34, 197, 94, 0.15)', padding: '2px 8px', borderRadius: '6px' }}>
                      +8.2%
                    </span>
                  </div>
                  <div style={{ marginTop: '8px' }}>
                    <div style={{ fontSize: '10px', color: '#64748b', marginBottom: '4px' }}>Mode: Area (Filled Gradient)</div>
                    <ChartRenderer chart={chartFixtures.sparkline_area as any} />
                  </div>
                </div>
              )}

              {/* Bar Sparkline Card */}
              {chartFixtures.sparkline_bar && (
                <div
                  style={{
                    backgroundColor: '#0f172a',
                    border: '1px solid #1e293b',
                    borderRadius: '12px',
                    padding: '16px',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'space-between',
                  }}
                >
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '8px' }}>
                    <div>
                      <span style={{ fontSize: '12px', color: '#94a3b8', fontWeight: 500 }}>Conversions</span>
                      <h4 style={{ fontSize: '22px', fontWeight: 700, margin: '4px 0 0 0', color: '#f8fafc' }}>1,250</h4>
                    </div>
                    <span style={{ fontSize: '12px', fontWeight: 600, color: '#22c55e', backgroundColor: 'rgba(34, 197, 94, 0.15)', padding: '2px 8px', borderRadius: '6px' }}>
                      +15.3%
                    </span>
                  </div>
                  <div style={{ marginTop: '8px' }}>
                    <div style={{ fontSize: '10px', color: '#64748b', marginBottom: '4px' }}>Mode: Bar (Micro Columns)</div>
                    <ChartRenderer chart={chartFixtures.sparkline_bar as any} />
                  </div>
                </div>
              )}
            </div>
          </div>
        )}

        {/* Main Grid */}
        <div
          style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(540px, 1fr))',
            gap: '24px',
          }}
        >
          {filteredCharts.map(({ fixtureKey, chart }) => {
            const isJsonOpen = !!showJsonMap[fixtureKey];

            return (
              <div
                key={fixtureKey}
                style={{
                  display: 'flex',
                  flexDirection: 'column',
                  border: '1px solid #1e293b',
                  borderRadius: '16px',
                  backgroundColor: '#0f172a',
                  overflow: 'hidden',
                  boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.3)',
                }}
              >
                {/* Header Badge */}
                <div
                  style={{
                    padding: '12px 20px',
                    borderBottom: '1px solid #1e293b',
                    backgroundColor: '#162032',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                  }}
                >
                  <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                    <span
                      style={{
                        padding: '3px 10px',
                        borderRadius: '9999px',
                        fontSize: '11px',
                        fontWeight: 700,
                        textTransform: 'uppercase',
                        letterSpacing: '0.05em',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        color: '#60a5fa',
                        border: '1px solid rgba(59, 130, 246, 0.4)',
                      }}
                    >
                      {chart.type}
                    </span>
                    <span
                      style={{
                        fontSize: '12px',
                        color: '#94a3b8',
                        textTransform: 'capitalize',
                        display: 'flex',
                        alignItems: 'center',
                        gap: '4px',
                      }}
                    >
                      <Layers size={14} /> {chart.family}
                    </span>
                  </div>

                  <button
                    onClick={() => toggleJson(fixtureKey)}
                    style={{
                      display: 'flex',
                      alignItems: 'center',
                      gap: '6px',
                      fontSize: '11px',
                      fontWeight: 600,
                      color: '#94a3b8',
                      backgroundColor: '#1e293b',
                      border: '1px solid #334155',
                      padding: '6px 12px',
                      borderRadius: '6px',
                      cursor: 'pointer',
                    }}
                  >
                    <Code2 size={14} />
                    {isJsonOpen ? 'Hide Payload' : 'View Wire JSON'}
                  </button>
                </div>

                {/* Rendered Chart */}
                <div style={{ padding: '20px', flex: 1 }}>
                  <ChartRenderer chart={chart} />
                </div>

                {/* JSON Wire Payload Viewer */}
                {isJsonOpen && (
                  <div
                    style={{
                      padding: '16px',
                      backgroundColor: '#020617',
                      borderTop: '1px solid #1e293b',
                      fontFamily: "'JetBrains Mono', monospace, monospace",
                      fontSize: '11px',
                      overflowX: 'auto',
                      maxHeight: '260px',
                      color: '#34d399',
                      lineHeight: '1.5',
                    }}
                  >
                    <pre style={{ margin: 0 }}>{JSON.stringify(chart, null, 2)}</pre>
                  </div>
                )}
              </div>
            );
          })}
        </div>

        {filteredCharts.length === 0 && (
          <div
            style={{
              textAlign: 'center',
              padding: '64px 24px',
              border: '1px dashed #334155',
              borderRadius: '16px',
              backgroundColor: '#0f172a',
            }}
          >
            <p style={{ color: '#94a3b8', fontSize: '14px', margin: 0 }}>
              No chart types matched your filter.
            </p>
          </div>
        )}
      </main>
    </div>
  );
}
