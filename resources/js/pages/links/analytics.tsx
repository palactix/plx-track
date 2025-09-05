import React, { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { ArrowLeft, X, BarChart3, TrendingUp, Calendar, Globe, Activity } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
  ResponsiveContainer,
  AreaChart,
  Area,
  LineChart,
  Line,
  BarChart,
  Bar,
  PieChart as RechartsPieChart,
  Cell,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  Pie
} from 'recharts';
import { useLinkAnalytics } from '@/queries/links/use-link-analytics';
import { ChartPoint } from '@/queries/links/link-analytics-interface';


export default function AnalyticsPage() {
  const { props } = usePage();
  
  const request = props.request as {
    segments: string[];
    [key: string]: unknown;
  };

  const code = request.segments && request.segments.length >= 3 ? request.segments[2] : 'Unknown';


  const [chartType, setChartType] = useState<'area' | 'line' | 'bar'>('area');
  const { data } = useLinkAnalytics(code || undefined);

  // Safely unwrap query data with defaults
  const chart_data: ChartPoint[] = data?.chart_data ?? [];
  const recent_clicks = data?.recent_clicks ?? [];
  const total_clicks: number = data?.total_clicks ?? 0;
  const clicks_7_days: number = data?.clicks_7_days ?? 0;
  const browsers = data?.browsers ?? [];

  const handleGoBack = () => {
    if (window.history.length > 1) {
      router.visit('/links');
    } else {
      router.visit('/links');
    }
  };

  const renderChart = () => {
    const commonProps = {
      data: chart_data,
      margin: { top: 10, right: 30, left: 0, bottom: 0 },
    };

    switch (chartType) {
      case 'area':
        return (
          <AreaChart {...commonProps}>
            <CartesianGrid strokeDasharray="3 3" className="opacity-30" />
            <XAxis
              dataKey="date"
              tick={{ fontSize: 12 }}
              tickFormatter={(value) => value}
            />
            <YAxis tick={{ fontSize: 12 }} />
            <Tooltip
              labelFormatter={(value) => value}
              formatter={(value) => [value, 'Clicks']}
            />
            <Area
              type="monotone"
              dataKey="clicks"
              stroke="#3B82F6"
              fill="#3B82F6"
              fillOpacity={0.3}
              strokeWidth={2}
            />
          </AreaChart>
        );
      case 'line':
        return (
          <LineChart {...commonProps}>
            <CartesianGrid strokeDasharray="3 3" className="opacity-30" />
            <XAxis
              dataKey="date"
              tick={{ fontSize: 12 }}
              tickFormatter={(value) => value}
            />
            <YAxis tick={{ fontSize: 12 }} />
            <Tooltip
              labelFormatter={(value) => value}
              formatter={(value) => [value, 'Clicks']}
            />
            <Line
              type="monotone"
              dataKey="clicks"
              stroke="#3B82F6"
              strokeWidth={3}
              dot={{ fill: '#3B82F6', strokeWidth: 2, r: 4 }}
              activeDot={{ r: 6, stroke: '#3B82F6', strokeWidth: 2 }}
            />
          </LineChart>
        );
      case 'bar':
        return (
          <BarChart {...commonProps}>
            <CartesianGrid strokeDasharray="3 3" className="opacity-30" />
            <XAxis
              dataKey="date"
              tick={{ fontSize: 12 }}
              tickFormatter={(value) => value}
            />
            <YAxis tick={{ fontSize: 12 }} />
            <Tooltip
              labelFormatter={(value) => value}
              formatter={(value) => [value, 'Clicks']}
            />
            <Bar dataKey="clicks" fill="#3B82F6" radius={[2, 2, 0, 0]} />
          </BarChart>
        );
      default:
        return (
          <AreaChart {...commonProps}>
            <CartesianGrid strokeDasharray="3 3" className="opacity-30" />
            <XAxis
              dataKey="date"
              tick={{ fontSize: 12 }}
              tickFormatter={(value) => value}
            />
            <YAxis tick={{ fontSize: 12 }} />
            <Tooltip
              labelFormatter={(value) => value}
              formatter={(value) => [value, 'Clicks']}
            />
            <Area
              type="monotone"
              dataKey="clicks"
              stroke="#3B82F6"
              fill="#3B82F6"
              fillOpacity={0.3}
              strokeWidth={2}
            />
          </AreaChart>
        );
    }
  };

  return (
    <>
      <Head title={`Analytics - ${code}`} />

      {/* Popup/Modal Style Layout */}
      <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
        {/* Header with Close Button */}
        <div className="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-3">
              <Button
                variant="ghost"
                size="sm"
                onClick={handleGoBack}
                className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full"
              >
                <ArrowLeft className="w-5 h-5" />
              </Button>
              <div>
                <h1 className="text-xl font-semibold text-gray-900 dark:text-white">
                  Link Analytics
                </h1>
                <p className="text-sm text-gray-500 dark:text-gray-400">
                  {code}
                </p>
              </div>
            </div>
            <Button
              variant="ghost"
              size="sm"
              onClick={handleGoBack}
              className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full"
            >
              <X className="w-5 h-5" />
            </Button>
          </div>
        </div>

        {/* Content */}
        <div className="max-w-7xl mx-auto px-6 py-8 space-y-8">
          {/* Stats Cards */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Total Clicks</CardTitle>
                <BarChart3 className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{(total_clicks || 0).toLocaleString()}</div>
                <p className="text-xs text-muted-foreground">
                  All time clicks
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Last 7 Days</CardTitle>
                <Calendar className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{(clicks_7_days || 0).toLocaleString()}</div>
                <p className="text-xs text-muted-foreground">
                  Recent activity
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Last 30 Days</CardTitle>
                <TrendingUp className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{(chart_data.reduce((sum: number, item: ChartPoint) => sum + item.clicks, 0) || 0).toLocaleString()}</div>
                <p className="text-xs text-muted-foreground">
                  Monthly performance
                </p>
              </CardContent>
            </Card>
          </div>

          {/* Chart Section */}
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle className="flex items-center space-x-2">
                  <BarChart3 className="h-5 w-5" />
                  <span>Clicks Over Time (7 Days)</span>
                </CardTitle>
                <div className="flex space-x-2">
                  <Button
                    variant={chartType === 'area' ? 'default' : 'outline'}
                    size="sm"
                    onClick={() => setChartType('area')}
                  >
                    Area
                  </Button>
                  <Button
                    variant={chartType === 'line' ? 'default' : 'outline'}
                    size="sm"
                    onClick={() => setChartType('line')}
                  >
                    Line
                  </Button>
                  <Button
                    variant={chartType === 'bar' ? 'default' : 'outline'}
                    size="sm"
                    onClick={() => setChartType('bar')}
                  >
                    Bar
                  </Button>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              <div className="h-80">
                <ResponsiveContainer width="100%" height="100%">
                  {renderChart()}
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>

          {/* Browser Analytics & Cities */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {/* Browser Pie Chart */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center space-x-2">
                  <Globe className="h-5 w-5" />
                  <span>Browser Distribution</span>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="h-64">
                  <ResponsiveContainer width="100%" height="100%">
                    <RechartsPieChart>
                      <Pie
                        data={browsers}
                        cx="50%"
                        cy="50%"
                        innerRadius={60}
                        outerRadius={100}
                        paddingAngle={5}
                        dataKey="value"
                      >
                        {browsers.map((entry: { name: string; value: number; color?: string }, index: number) => (
                          <Cell key={`cell-${index}`} fill={entry.color} />
                        ))}
                      </Pie>
                      <Tooltip formatter={(value) => [`${value}%`, 'Usage']} />
                      <Legend />
                    </RechartsPieChart>
                  </ResponsiveContainer>
                </div>
              </CardContent>
            </Card>

            {/* Recent Clicks */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center space-x-2">
                  <Activity className="h-5 w-5" />
                  <span>Recent Clicks</span>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {recent_clicks && recent_clicks.length > 0 ? (
                    recent_clicks.slice(0, 5).map((click: { browser: string; country: string; referrer: string; created_at: string }, index: number) => (
                      <div key={index} className="flex items-center justify-between py-2">
                        <div className="flex items-center space-x-3">
                          <div className="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <Activity className="w-4 h-4 text-green-600 dark:text-green-400" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900 dark:text-white">
                              {click.browser}
                            </p>
                            <p className="text-xs text-gray-500 dark:text-gray-400">
                              {click.country} • {click.referrer}
                            </p>
                          </div>
                        </div>
                        <div className="text-right">
                          <p className="text-xs text-gray-500 dark:text-gray-400">
                            {new Date(click.created_at).toLocaleString()}
                          </p>
                        </div>
                      </div>
                    ))
                  ) : (
                    <div className="text-center py-8">
                      <Activity className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                      <p className="text-gray-500 dark:text-gray-400">No recent clicks</p>
                    </div>
                  )}
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </>
  );
}