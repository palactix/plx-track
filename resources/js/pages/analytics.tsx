import { ImageWithFallback } from "@/components/figma/ImageWithFallback";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { PublicLayout } from "@/layouts/public-layout";
import { Head } from "@inertiajs/react";
import { ArrowLeft, Badge, BarChart3, Copy, RefreshCcw, Globe, Share2, Home } from "lucide-react";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, ResponsiveContainer, Area, AreaChart } from 'recharts';


export default function Analytics() {

  const isDarkMode = true;
   const chartData = [
    { date: 'Aug 14', clicks: 0 },
    { date: 'Aug 15', clicks: 0 },
    { date: 'Aug 16', clicks: 0 },
    { date: 'Aug 17', clicks: 0 },
    { date: 'Aug 18', clicks: 0 },
    { date: 'Aug 19', clicks: 0 },
    { date: 'Aug 20', clicks: 1 },
  ];

  const recentClicks = [
    {
      dateTime: 'Aug 20, 2025\n1:07 PM',
      location: '104.23.168.100',
      browser: 'Firefox',
      source: 'Direct'
    }
  ];

  // Removed theme/color variables. Use Tailwind dark: and light classes directly.

  return (
    <PublicLayout >
      <Head title="Analytics" />  
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 min-h-screen bg-gray-50 dark:bg-slate-900">
        {/* Back to Home Button */}
        <Button 
          variant="ghost" 
          className="mb-6 text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700"
        >
          <ArrowLeft className="w-4 h-4 mr-2" />
          Back to Home
        </Button>

        <div className="grid lg:grid-cols-2 gap-6 mb-6 sm:mb-8">
          {/* Short Link Section */}
          <Card className="bg-white border-gray-200 dark:bg-slate-800 dark:border-slate-700">
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
                <div className="text-xl sm:text-2xl font-mono mb-2 text-green-600">MthAOJ</div>
              </div>
              <Button className="w-full text-white bg-green-600 hover:bg-green-700">
                <Copy className="w-4 h-4 mr-2" />
                Copy Short URL
              </Button>
            </CardContent>
          </Card>

          {/* Original URL Section */}
          <Card className="bg-white border-gray-200 dark:bg-slate-800 dark:border-slate-700">
            <CardHeader className="pb-4 border-b border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-800">
              <CardTitle className="text-gray-900 dark:text-white">Original URL:</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex gap-3 sm:gap-4">
                <div className="w-10 h-10 sm:w-12 sm:h-12 rounded-lg overflow-hidden flex-shrink-0">
                  <ImageWithFallback
                    src="https://images.unsplash.com/photo-1692106979244-a2ac98253f6b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHx3ZWIlMjBkZXZlbG9wbWVudCUyMGNvZGluZ3xlbnwxfHx8fDE3NTU2MjQ4NDR8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
                    alt="Laravel logo"
                    className="w-full h-full object-cover"
                  />
                </div>
                <div className="flex-1 min-w-0">
                  <div className="text-gray-500 dark:text-slate-400 text-xs sm:text-sm mb-1 break-all">
                    https://pix.bz/laravel-deep-into-jobs-listener-in-artisan-commands
                  </div>
                  <h3 className="text-gray-900 dark:text-white font-medium mb-1 text-sm sm:text-base line-clamp-2">
                    Build Faster Laravel Apps: Deep Dive Into Jobs, Listeners & Artisan Commands
                  </h3>
                  <a href="#" className="text-xs sm:text-sm text-green-600 hover:text-green-700 dark:hover:text-green-300">
                    One Into Jobs, Listeners & Artisan Commands
                  </a>
                  <p className="text-gray-500 dark:text-slate-400 text-xs sm:text-sm mt-2 line-clamp-2">
                    Explore Laravel background task handling: Jobs, Commands, and Queueable Listeners. Best examples and best practices Inside!
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Analytics Period */}
        <Card className="bg-white border-gray-200 dark:bg-slate-800 dark:border-slate-700 mb-6 sm:mb-8">
          <CardContent className="p-4">
            <div className="text-sm sm:text-base text-green-600">
              <strong>Analytics Period</strong>
            </div>
            <div className="text-gray-600 dark:text-slate-300 text-xs sm:text-sm">
              Analytics available for last 7 days.<br />
              Created: Aug 20, 2025 at 1:06 PM
            </div>
          </CardContent>
        </Card>

        {/* Clicks Chart */}
        <Card className="bg-white border-gray-200 dark:bg-slate-800 dark:border-slate-700 mb-6 sm:mb-8">
          <CardHeader className="pb-4 border-b border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <div className="flex flex-col gap-4">
              <CardTitle className="text-gray-900 dark:text-white flex items-center gap-2 text-lg sm:text-xl">
                <BarChart3 className="w-5 h-5" />
                <span className="leading-tight">Clicks per Day (Last 7 Days)</span>
              </CardTitle>
              <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div className="flex gap-4 sm:gap-6">
                  <div className="text-center">
                    <div className="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">1</div>
                    <div className="text-xs text-gray-500 dark:text-slate-400">Total Clicks</div>
                  </div>
                  <div className="text-center">
                    <div className="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">1</div>
                    <div className="text-xs text-gray-500 dark:text-slate-400">Last 7 Days</div>
                  </div>
                </div>
                <Button variant="secondary" size="sm" className="self-start sm:self-auto bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600">
                  <RefreshCcw className="w-4 h-4 mr-2" />
                  Refresh
                </Button>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="text-gray-500 dark:text-slate-400 text-xs sm:text-sm mb-4">Interactive chart showing click trends</div>
            <div className="h-48 sm:h-64">
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={chartData}>
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
              </ResponsiveContainer>
            </div>
            <div className="flex gap-2 mt-4">
              <Button size="sm" className="text-xs sm:text-sm text-white bg-green-600 hover:bg-green-700">Area</Button>
              <Button size="sm" variant="outline" className="text-xs sm:text-sm border-gray-300 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:border-slate-600 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700">Line</Button>
              <Button size="sm" variant="outline" className="text-xs sm:text-sm border-gray-300 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:border-slate-600 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700">Bar</Button>
            </div>
          </CardContent>
        </Card>

        {/* Recent Clicks Table */}
        <Card className="bg-white border-gray-200 dark:bg-slate-800 dark:border-slate-700 mb-6 sm:mb-8">
          <CardHeader className="pb-4 border-b border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <div className="flex flex-col gap-4">
              <CardTitle className="text-gray-900 dark:text-white flex items-start gap-2 text-lg sm:text-xl">
                <div className="w-8 h-8 rounded-full flex items-center justify-center mt-0.5" style={{ backgroundColor: 'rgba(35, 134, 54, 0.2)' }}>
                  <BarChart3 className="w-4 h-4" style={{ color: '#4ade80' }} />
                </div>
                <span className="leading-tight">Recent Clicks (Last 7 Days)</span>
              </CardTitle>
              <div className="text-gray-500 dark:text-slate-400 text-xs sm:text-sm">
                Showing 1 of 1 clicks (Last 7 Days)
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="overflow-x-auto">
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
                  {recentClicks.map((click, index) => (
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
                            Firefox
                          </Badge>
                          <Badge className="text-xs bg-gray-100 text-gray-600 border-gray-300 dark:bg-slate-600/50 dark:text-slate-300 dark:border-slate-500/30">
                            macOS
                          </Badge>
                        </div>
                      </td>
                      <td className="py-4 text-gray-600 dark:text-slate-300 text-xs sm:text-sm">{click.source}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
            <div className="mt-4 text-center">
              <div className="flex items-center justify-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-slate-400">
                <div className="w-2 h-2 rounded-full bg-green-400"></div>
                <span>All clicks loaded</span>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* CTA Section */}
        <Card className="border mb-6 sm:mb-8 bg-green-50 border-green-200 dark:bg-green-900/10 dark:border-green-900/30">
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
              <Button className="text-white bg-green-600 hover:bg-green-700">
                <Home className="w-4 h-4 mr-2" />
                Back to Home
              </Button>
              <Button variant="secondary" className="bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600"> 
                Share Analytics
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </PublicLayout>
  ); 
}