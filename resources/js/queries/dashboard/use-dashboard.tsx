import { useQuery } from '@tanstack/react-query';
import { fetchJson } from '@/lib/apiClient';
import {
  OverviewStat,
  ChartDataPoint,
  RecentLink,
  DashboardFilters,
  OverviewStatsResponse,
  ChartDataResponse,
  RecentLinksResponse
} from './dashboard-interface';

// Query Keys
export function overviewStatsQueryKey(filters: DashboardFilters): OverviewStatsQueryKey {
  return ['dashboard', 'overview-stats', filters];
}

export function chartDataQueryKey(filters: DashboardFilters): ChartDataQueryKey {
  return ['dashboard', 'chart-data', filters];
}

export function recentLinksQueryKey(): RecentLinksQueryKey {
  return ['dashboard', 'recent-links'];
}

export type OverviewStatsQueryKey = ['dashboard', 'overview-stats', DashboardFilters];
export type ChartDataQueryKey = ['dashboard', 'chart-data', DashboardFilters];
export type RecentLinksQueryKey = ['dashboard', 'recent-links'];

// Query Functions
export async function overviewStatsQueryFn({ queryKey }: { queryKey: OverviewStatsQueryKey }): Promise<OverviewStat[]> {
  const [, , filters] = queryKey;
  const params = new URLSearchParams();

  if (filters.period) params.append('period', filters.period);
  if (filters.start_date) params.append('start_date', filters.start_date);
  if (filters.end_date) params.append('end_date', filters.end_date);

  const response = await fetchJson<OverviewStatsResponse>(`/api/dashboard/overview-stats?${params.toString()}`);
  return response.data;
}

export async function chartDataQueryFn({ queryKey }: { queryKey: ChartDataQueryKey }): Promise<ChartDataPoint[]> {
  const [, , filters] = queryKey;
  const params = new URLSearchParams();

  if (filters.period) params.append('period', filters.period);
  if (filters.start_date) params.append('start_date', filters.start_date);
  if (filters.end_date) params.append('end_date', filters.end_date);

  const response = await fetchJson<ChartDataResponse>(`/api/dashboard/chart-data?${params.toString()}`);
  return response.data;
}

export async function recentLinksQueryFn(): Promise<RecentLink[]> {
  const response = await fetchJson<RecentLinksResponse>('/api/dashboard/recent-links');
  return response.data;
}

// React Query Hooks
export function useOverviewStats(filters: DashboardFilters) {
  return useQuery({
    queryKey: overviewStatsQueryKey(filters),
    queryFn: overviewStatsQueryFn,
    staleTime: 5 * 60 * 1000, // 5 minutes
  });
}

export function useChartData(filters: DashboardFilters) {
  return useQuery({
    queryKey: chartDataQueryKey(filters),
    queryFn: chartDataQueryFn,
    staleTime: 5 * 60 * 1000, // 5 minutes
  });
}

export function useRecentLinks() {
  return useQuery({
    queryKey: recentLinksQueryKey(),
    queryFn: recentLinksQueryFn,
    staleTime: 5 * 60 * 1000, // 5 minutes
  });
}
