import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { useChartData } from '@/queries/dashboard/use-dashboard';
import { DashboardFilters } from '@/queries/dashboard/dashboard-interface';
import { BarChart3 } from 'lucide-react';
import { XAxis, YAxis, CartesianGrid, ResponsiveContainer, Area, AreaChart } from 'recharts';

interface ClickTrendsChartProps {
    filters: DashboardFilters;
}

export function ClickTrendsChart({ filters }: ClickTrendsChartProps) {
    const { data: chartData = [], isLoading } = useChartData(filters);

    const getChartTitle = () => {
        switch (filters.period) {
            case '7days':
                return 'Click Trends (Last 7 Days)';
            case '30days':
                return 'Click Trends (Last 30 Days)';
            case 'custom':
                return 'Click Trends (Custom Range)';
            default:
                return 'Click Trends';
        }
    };

    if (isLoading) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <BarChart3 className="w-5 h-5" />
                        {getChartTitle()}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="h-64">
                        <Skeleton className="w-full h-full" />
                    </div>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2 text-gray-900 dark:text-white">
                    <BarChart3 className="w-5 h-5" />
                    {getChartTitle()}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div className="h-64">
                    <ResponsiveContainer width="100%" height="100%">
                        <AreaChart data={chartData}>
                            <defs>
                                <linearGradient id="colorClicks" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="5%" stopColor="#238636" stopOpacity={0.3}/>
                                    <stop offset="95%" stopColor="#238636" stopOpacity={0}/>
                                </linearGradient>
                            </defs>
                            <CartesianGrid strokeDasharray="3 3" stroke="#374151" />
                            <XAxis dataKey="date" stroke="#9ca3af" />
                            <YAxis stroke="#9ca3af" />
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
            </CardContent>
        </Card>
    );
}
