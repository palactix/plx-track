import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { DashboardFilters as DashboardFiltersComponent, OverviewStats, ClickTrendsChart, RecentLinks } from '@/components/dashboard';
import { DashboardFilters } from '@/queries/dashboard/dashboard-interface';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard() {
    const [filters, setFilters] = useState<DashboardFilters>({
        period: '7days'
    });
    const [showCustomDatePicker, setShowCustomDatePicker] = useState(false);
    const [customStartDate, setCustomStartDate] = useState('');
    const [customEndDate, setCustomEndDate] = useState('');

    const handlePeriodChange = (period: string) => {
        if (period === 'custom') {
            setShowCustomDatePicker(true);
            setFilters({ period: 'custom' });
        } else if (period === '7days' || period === '30days') {
            setShowCustomDatePicker(false);
            setFilters({ period });
        }
    };

    const handleCustomDateApply = () => {
        if (customStartDate && customEndDate) {
            setFilters({
                period: 'custom',
                start_date: customStartDate,
                end_date: customEndDate
            });
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
                <DashboardFiltersComponent
                    filters={filters}
                    onPeriodChange={handlePeriodChange}
                    showCustomDatePicker={showCustomDatePicker}
                    customStartDate={customStartDate}
                    customEndDate={customEndDate}
                    onCustomStartDateChange={setCustomStartDate}
                    onCustomEndDateChange={setCustomEndDate}
                    onCustomDateApply={handleCustomDateApply}
                />

                <OverviewStats filters={filters} />

                <ClickTrendsChart filters={filters} />

                <RecentLinks />
            </div>
        </AppLayout>
    );
}
