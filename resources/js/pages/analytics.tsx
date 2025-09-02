import { ImageWithFallback } from "@/components/figma/ImageWithFallback";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { PublicLayout } from "@/layouts/public-layout";
import { Head, router, usePage } from "@inertiajs/react";
import { ArrowLeft, BarChart3, Copy, RefreshCcw, Globe, Share2, Home, TrendingUp, Activity } from "lucide-react";
import { XAxis, YAxis, CartesianGrid, ResponsiveContainer, Area, AreaChart, LineChart, Line, BarChart, Bar } from 'recharts';
import { useState } from 'react';
import { toast } from 'react-hot-toast';
import { Link } from "@/queries/links/link-interface";


export default function Analytics() {
  const { props } = usePage();
  const link = props.link as Link;
  const analytics = props.analytics as {
    total_clicks: number;
    last_7_days_clicks: number;
    chart_data: Array<{ date: string; clicks: number }>;
    recent_clicks: Array<{
      dateTime: string;
      location: string;
      browser: string;
      source: string;
      platform: string;
    }>;
  };

  const [copiedLinkId, setCopiedLinkId] = useState<number | null>(null);
  const [chartType, setChartType] = useState<'area' | 'line' | 'bar'>('area');
  const [isRefreshing, setIsRefreshing] = useState(false);

  const isDarkMode = true;

  const copyToClipboard = async (url: string) => {
    try {
      await navigator.clipboard.writeText(url);
      setCopiedLinkId(link.id);
      setTimeout(() => setCopiedLinkId(null), 2000);
      toast.success(`Copied: ${url}`);
    } catch {
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = url;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);
      setCopiedLinkId(link.id);
      setTimeout(() => setCopiedLinkId(null), 2000);
      toast.success(`Copied: ${url}`);
    }
  };

  const handleRefresh = async () => {
    setIsRefreshing(true);
    try {
      router.reload({ only: ['analytics'] });
      toast.success('Analytics refreshed!');
    } catch {
      toast.error('Failed to refresh analytics');
    } finally {
      setIsRefreshing(false);
    }
  };

  // Fill missing dates with 0 clicks for the last 7 days
  const fillChartData = () => {
    const today = new Date();
    const chartData = [];

    for (let i = 6; i >= 0; i--) {
      const date = new Date(today);
      date.setDate(date.getDate() - i);
      const dateStr = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

      const existingData = analytics?.chart_data?.find((item: { date: string; clicks: number }) => item.date === dateStr);
      chartData.push({
        date: dateStr,
        clicks: existingData ? existingData.clicks : 0
      });
    }

    return chartData;
  };

  const chartData = fillChartData();
  const recentClicks = analytics?.recent_clicks || [];


  const renderChart = () => {
    const commonProps = {
      data: chartData,
      margin: { top: 5, right: 30, left: 20, bottom: 5 }
    };

    console.log(commonProps);

    switch (chartType) {
      case 'line':
        return (
          <LineChart {...commonProps}>
            <CartesianGrid strokeDasharray="3 3" stroke={isDarkMode ? "#374151" : "#e5e7eb"} />
            <XAxis 
              dataKey="date" 
              stroke={isDarkMode ? "#9ca3af" : "#6b7280"}
              fontSize={10}
            />
            <YAxis 
              stroke={isDarkMode ? "#9ca3af" : "#6b7280"}
              fontSize={10}
            />
            <Line
              type="monotone"
              dataKey="clicks"
              stroke="#238636"
              strokeWidth={2}
              dot={{ fill: '#238636', strokeWidth: 2, r: 4 }}
              activeDot={{ r: 6, stroke: '#238636', strokeWidth: 2 }}
            />
          </LineChart>
        );
      
      case 'bar':
        return (
          <BarChart {...commonProps}>
            <CartesianGrid strokeDasharray="3 3" stroke={isDarkMode ? "#374151" : "#e5e7eb"} />
            <XAxis 
              dataKey="date" 
              stroke={isDarkMode ? "#9ca3af" : "#6b7280"}
              fontSize={10}
            />
            <YAxis 
              stroke={isDarkMode ? "#9ca3af" : "#6b7280"}
              fontSize={10}
            />
            <Bar
              dataKey="clicks"
              fill="#238636"
              radius={[2, 2, 0, 0]}
            />
          </BarChart>
        );
      
      case 'area':
      default:
        return (
          <AreaChart {...commonProps}>
            <defs>
              <linearGradient id="colorClicks" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="#238636" stopOpacity={0.3}/>
                <stop offset="95%" stopColor="#238636" stopOpacity={0}/>
              </linearGradient>
            </defs>
            <CartesianGrid strokeDasharray="3 3" stroke={isDarkMode ? "#374151" : "#e5e7eb"} />
            <XAxis 
              dataKey="date" 
              stroke={isDarkMode ? "#9ca3af" : "#6b7280"}
              fontSize={10}
            />
            <YAxis 
              stroke={isDarkMode ? "#9ca3af" : "#6b7280"}
              fontSize={10}
            />
            <Area
              type="monotone"
              dataKey="clicks"
              stroke="#238636"
              strokeWidth={2}
              fillOpacity={1}
              fill="url(#colorClicks)"
            />
          </AreaChart>
        );
    }
  };

  // Removed theme/color variables. Use Tailwind dark: and light classes directly.

  return (
    <PublicLayout >
      <Head title={`Analytics - ${link?.short_code || 'Link'}`} />  
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 min-h-screen bg-gray-50 dark:bg-slate-900">
        {/* Back to Home Button */}
        <Button 
          variant="ghost" 
          className="mb-6 text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700"
          onClick={() => window.location.href = '/'}
        >
          <ArrowLeft className="w-4 h-4 mr-2" />
          Back to Home
        </Button>

        <div className="grid lg:grid-cols-2 gap-6 mb-6 sm:mb-8">
          {/* Short Link Section */}
          <Card>
            <CardHeader className="pb-4 border-b border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-800">
              <CardTitle className="text-gray-900 dark:text-white flex flex-col sm:flex-row sm:items-center gap-2">
                <span>Short Link:</span>
                <Badge className="text-green-400 border-green-500/30 self-start sm:self-auto" style={{ backgroundColor: 'rgba(35, 134, 54, 0.2)' }}>
                  Active
                </Badge>
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="bg-gray-100 dark:bg-slate-700 p-4 rounded-lg mb-4">
                <div className="text-xl sm:text-2xl font-mono mb-2 text-green-600">{link.short_code}</div>
              </div>
              <Button 
                className="w-full text-white bg-green-600 hover:bg-green-700"
                onClick={() => copyToClipboard(link.short_url)}
              >
                {copiedLinkId === link.id ? 'Copied!' : 'Copy Short URL'}
                <Copy className="w-4 h-4 ml-2" />
              </Button>
            </CardContent>
          </Card>

          {/* Original URL Section */}
          <Card>
            <CardHeader className="pb-4 border-b border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-800">
              <CardTitle className="text-gray-900 dark:text-white">Original URL:</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex gap-3 sm:gap-4">
                <div className="w-10 h-10 sm:w-12 sm:h-12 rounded-lg overflow-hidden flex-shrink-0">
                  <ImageWithFallback
                    src={link.og_image_url}
                    alt={`${new URL(link.original_url).hostname} preview`}
                    className="w-full h-full object-cover"
                  />
                </div>
                <div className="flex-1 min-w-0">
                  <div className="text-gray-500 dark:text-slate-400 text-xs sm:text-sm mb-1 break-all">
                    {link.original_url}
                  </div>
                  <h3 className="text-gray-900 dark:text-white font-medium mb-1 text-sm sm:text-base line-clamp-2">
                    {link.title}
                  </h3>
                  <a href={link.original_url} className="text-xs sm:text-sm text-green-600 hover:text-green-700 dark:hover:text-green-300">
                    Visit Original
                  </a>
                  <p className="text-gray-500 dark:text-slate-400 text-xs sm:text-sm mt-2 line-clamp-2">
                    {link.description}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Analytics Period */}
        <Card className="mb-6 sm:mb-8">
          <CardContent className="">
            <div className="text-sm sm:text-base text-primary">
              <strong>Analytics Period</strong>
            </div>
            <div className="text-gray-600 dark:text-slate-300 text-xs sm:text-sm">
              Analytics available for last 7 days.<br />
              Created: {link.created_at}
            </div>
          </CardContent>
        </Card>

        {/* Clicks Chart */}
        <Card className="mb-6 sm:mb-8">
          <CardHeader className="pb-4 border-b border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <div className="flex flex-col gap-4">
              <CardTitle className="text-gray-900 dark:text-white flex items-center gap-2 text-lg sm:text-xl">
                <BarChart3 className="w-5 h-5" />
                <span className="leading-tight">Clicks per Day (Last 7 Days)</span>
              </CardTitle>
              <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div className="flex gap-4 sm:gap-6">
                  <div className="text-center">
                    <div className="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{analytics?.total_clicks || 0}</div>
                    <div className="text-xs text-gray-500 dark:text-slate-400">Total Clicks</div>
                  </div>
                  <div className="text-center">
                    <div className="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{analytics?.last_7_days_clicks || 0}</div>
                    <div className="text-xs text-gray-500 dark:text-slate-400">Last 7 Days</div>
                  </div>
                </div>
                <Button 
                  variant="secondary" 
                  size="sm" 
                  className="self-start sm:self-auto bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600"
                  onClick={handleRefresh}
                  disabled={isRefreshing}
                >
                  <RefreshCcw className={`w-4 h-4 mr-2 ${isRefreshing ? 'animate-spin' : ''}`} />
                  {isRefreshing ? 'Refreshing...' : 'Refresh'}
                </Button>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="text-gray-500 dark:text-slate-400 text-xs sm:text-sm mb-4">Interactive chart showing click trends</div>
            <div className="h-48 sm:h-64">
              {chartData.every(item => item.clicks === 0) ? (
                <div className="flex items-center justify-center h-full">
                  <div className="text-center">
                    <div className="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-gray-100 dark:bg-slate-700">
                      <BarChart3 className="w-8 h-8 text-gray-400 dark:text-slate-500" />
                    </div>
                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No click data</h3>
                    <p className="text-gray-500 dark:text-slate-400 text-sm">
                      No clicks recorded for the last 7 days. Start sharing your link to see analytics here!
                    </p>
                  </div>
                </div>
              ) : (
                <ResponsiveContainer width="100%" height="100%">
                  {renderChart()}
                </ResponsiveContainer>
              )}
            </div>
            <div className="flex gap-2 mt-4">
              <Button 
                size="sm" 
                className={`text-xs sm:text-sm ${chartType === 'area' ? 'text-white bg-green-600 hover:bg-green-700' : 'border-gray-300 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:border-slate-600 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700'}`}
                variant={chartType === 'area' ? 'default' : 'outline'}
                onClick={() => setChartType('area')}
              >
                <BarChart3 className="w-3 h-3 mr-1" />
                Area
              </Button>
              <Button 
                size="sm" 
                className={`text-xs sm:text-sm ${chartType === 'line' ? 'text-white bg-green-600 hover:bg-green-700' : 'border-gray-300 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:border-slate-600 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700'}`}
                variant={chartType === 'line' ? 'default' : 'outline'}
                onClick={() => setChartType('line')}
              >
                <TrendingUp className="w-3 h-3 mr-1" />
                Line
              </Button>
              <Button 
                size="sm" 
                className={`text-xs sm:text-sm ${chartType === 'bar' ? 'text-white bg-green-600 hover:bg-green-700' : 'border-gray-300 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:border-slate-600 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700'}`}
                variant={chartType === 'bar' ? 'default' : 'outline'}
                onClick={() => setChartType('bar')}
              >
                <Activity className="w-3 h-3 mr-1" />
                Bar
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* Recent Clicks Table */}
        <Card className="mb-6 sm:mb-8">
          <CardHeader className="pb-4 border-b border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <div className="flex flex-col gap-4">
              <CardTitle className="text-gray-900 dark:text-white flex items-start gap-2 text-lg sm:text-xl">
                <div className="w-8 h-8 rounded-full flex items-center justify-center mt-0.5" style={{ backgroundColor: 'rgba(35, 134, 54, 0.2)' }}>
                  <BarChart3 className="w-4 h-4" style={{ color: '#4ade80' }} />
                </div>
                <span className="leading-tight">Recent Clicks (Last 7 Days)</span>
              </CardTitle>
              <div className="text-gray-500 dark:text-slate-400 text-xs sm:text-sm">
                Showing {recentClicks.length} of {analytics?.last_7_days_clicks || 0} clicks (Last 7 Days)
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="overflow-x-auto">
              {recentClicks.length === 0 ? (
                <div className="text-center py-12">
                  <div className="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-gray-100 dark:bg-slate-700">
                    <BarChart3 className="w-8 h-8 text-gray-400 dark:text-slate-500" />
                  </div>
                  <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No clicks yet</h3>
                  <p className="text-gray-500 dark:text-slate-400 text-sm">
                    This link hasn't received any clicks in the last 7 days. Share it to start tracking analytics!
                  </p>
                </div>
              ) : (
                <table className="w-full min-w-[600px]">
                  <thead>
                    <tr className="border-b border-gray-200 dark:border-slate-700"> 
                      <th className="text-left py-3 text-gray-600 dark:text-slate-300 font-medium text-xs sm:text-sm">Date & Time</th>
                      <th className="text-left py-3 text-gray-600 dark:text-slate-300 font-medium text-xs sm:text-sm">Location</th>
                      <th className="text-left py-3 text-gray-600 dark:text-slate-300 font-medium text-xs sm:text-sm">Browser</th>
                      <th className="text-left py-3 text-gray-600 dark:text-slate-300 font-medium text-xs sm:text-sm">Source</th>
                    </tr>
                  </thead>
                  <tbody>
                    {recentClicks.map((click: { dateTime: string; location: string; browser: string; source: string, platform: string }, index: number) => (
                      <tr key={index} className="border-b border-gray-100 dark:border-slate-700/50"> 
                        <td className="py-4 text-gray-600 dark:text-slate-300 whitespace-pre-line text-xs sm:text-sm">{click.dateTime}</td>
                        <td className="py-4">
                          <div className="flex items-center gap-2">
                            <Globe className="w-3 h-3 sm:w-4 sm:h-4 text-green-600" />
                            <span className="text-gray-600 dark:text-slate-300 text-xs sm:text-sm">{click.location}</span>
                          </div>
                        </td>
                        <td className="py-4">
                          <div className="flex flex-col sm:flex-row items-start sm:items-center gap-1 sm:gap-2">
                            <Badge className="bg-orange-600/20 text-orange-400 border-orange-500/30 text-xs">
                              {click.browser}
                            </Badge>
                            <Badge className="text-xs bg-gray-100 text-gray-600 border-gray-300 dark:bg-slate-600/50 dark:text-slate-300 dark:border-slate-500/30">
                              {click.platform}
                            </Badge>
                          </div>
                        </td>
                        <td className="py-4 text-gray-600 dark:text-slate-300 text-xs sm:text-sm">{click.source}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}
            </div>
            {recentClicks.length > 0 && (
              <div className="mt-4 text-center">
                <div className="flex items-center justify-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-slate-400">
                  <div className="w-2 h-2 rounded-full bg-green-400"></div>
                  <span>All clicks loaded</span>
                </div>
              </div>
            )}
          </CardContent>
        </Card>

        {/* CTA Section */}
        <Card className="border mb-6 sm:mb-8 bg-primary/20 border-primary/20 dark:bg-primary/30 dark:border-primary/30">
          <CardContent className="p-6 sm:p-8 text-center">
            <div className="mb-6">
              <div className="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center mx-auto mb-4 bg-green-600">
                <Share2 className="w-5 h-5 sm:w-6 sm:h-6 text-white" />
              </div>
              <h2 className="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white mb-4">Want to create more short links?</h2>
              <p className="text-gray-600 dark:text-slate-300 text-sm sm:text-base">
                Go back to the homepage to create more short links or sign up for advanced features.
              </p>
            </div>
            <div className="flex flex-col sm:flex-row gap-3 justify-center">
              <Button 
                className="text-white bg-green-600 hover:bg-green-700"
                onClick={() => window.location.href = '/'}
              >
                <Home className="w-4 h-4 mr-2" />
                Back to Home
              </Button>
              <Button 
                variant="secondary" 
                className="bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600"
                onClick={() => copyToClipboard(window.location.href)}
              > 
                <Share2 className="w-4 h-4 mr-2" />
                Share Analytics
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </PublicLayout>
  ); 
}