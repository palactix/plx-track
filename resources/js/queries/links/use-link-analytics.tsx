import { useQuery } from '@tanstack/react-query';
import { fetchJson } from '@/lib/apiClient';
import { LinkAnalyticsApi } from './link-analytics-interface';

export const linkAnalyticsKey = (code: string) => ['links', code, 'analytics'];

export async function linkAnalyticsFn(code: string): Promise<LinkAnalyticsApi> {
  return fetchJson<LinkAnalyticsApi>(`/api/links/${code}/analytics`);
}

export function useLinkAnalytics(code?: string) {
  return useQuery<LinkAnalyticsApi, Error>({
    queryKey: linkAnalyticsKey(code || 'unknown'),
    queryFn: () => linkAnalyticsFn(code || ''),
    enabled: Boolean(code),
    staleTime: 1000 * 60, // 1 minute,
  });
}

