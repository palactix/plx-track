import { Button } from '@/components/ui/button';
import { DashboardFilters as DashboardFiltersType } from '@/queries/dashboard/dashboard-interface';

interface DashboardFiltersProps {
    filters: DashboardFiltersType;
    onPeriodChange: (period: string) => void;
    showCustomDatePicker: boolean;
    customStartDate: string;
    customEndDate: string;
    onCustomStartDateChange: (date: string) => void;
    onCustomEndDateChange: (date: string) => void;
    onCustomDateApply: () => void;
}

export function DashboardFilters({
    filters,
    onPeriodChange,
    showCustomDatePicker,
    customStartDate,
    customEndDate,
    onCustomStartDateChange,
    onCustomEndDateChange,
    onCustomDateApply,
}: DashboardFiltersProps) {
    return (
        <div className="flex justify-between items-center mb-6">
            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <div className="flex items-center gap-4">
                <select
                    value={filters.period || '7days'}
                    onChange={(e) => onPeriodChange(e.target.value)}
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                >
                    <option value="7days">Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="custom">Custom Range</option>
                </select>

                {showCustomDatePicker && (
                    <div className="flex gap-2 items-center">
                        <input
                            type="date"
                            value={customStartDate}
                            onChange={(e) => onCustomStartDateChange(e.target.value)}
                            className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                            placeholder="Start date"
                        />
                        <input
                            type="date"
                            value={customEndDate}
                            onChange={(e) => onCustomEndDateChange(e.target.value)}
                            className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                            placeholder="End date"
                        />
                        <Button
                            onClick={onCustomDateApply}
                            size="sm"
                            className="px-4 py-2"
                        >
                            Apply
                        </Button>
                    </div>
                )}
            </div>
        </div>
    );
}
