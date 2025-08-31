import { Card, CardContent } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { useOverviewStats } from '@/queries/dashboard/use-dashboard';
import { DashboardFilters } from '@/queries/dashboard/dashboard-interface';
import { Link2, Activity, TrendingUp, Calendar } from 'lucide-react';

const iconMap = {
    Link2,
    Activity,
    TrendingUp,
    Calendar,
};

interface OverviewStatsProps {
    filters: DashboardFilters;
}

export function OverviewStats({ filters }: OverviewStatsProps) {
    const { data: stats = [], isLoading } = useOverviewStats(filters);

    if (isLoading) {
        return (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {Array.from({ length: 4 }).map((_, index) => (
                    <Card key={index} className="transition-all duration-300">
                        <CardContent className="p-6">
                            <div className="flex items-center justify-between mb-4">
                                <Skeleton className="w-10 h-10 rounded-full" />
                                <Skeleton className="w-12 h-4" />
                            </div>
                            <div>
                                <Skeleton className="w-16 h-8 mb-1" />
                                <Skeleton className="w-20 h-4" />
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>
        );
    }

    return (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {stats.map((stat, index) => {
                const Icon = iconMap[stat.icon as keyof typeof iconMap];
                return (
                    <Card key={index} className="transition-all duration-300">
                        <CardContent className="">
                            <div className="flex items-center justify-between mb-4">
                                <div className="w-10 h-10 rounded-full flex items-center justify-center bg-green-100 dark:bg-green-900/20">
                                    <Icon className="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                                <span className={`text-sm ${stat.change.startsWith('+') ? 'text-green-500' : 'text-red-500'}`}>
                                    {stat.change}
                                </span>
                            </div>
                            <div>
                                <p className="text-2xl font-bold text-gray-900 dark:text-white mb-1">{stat.value}</p>
                                <p className="text-sm text-gray-500 dark:text-gray-400">{stat.title}</p>
                            </div>
                        </CardContent>
                    </Card>
                );
            })}
        </div>
    );
}
