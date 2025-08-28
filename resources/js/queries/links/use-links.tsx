import type { QueryFunctionContext } from '@tanstack/react-query';

import { fetchJson, PaginatedResponse } from "@/lib/apiClient";
import { Link } from "./link-interface";



export function recentLinksQueryKey(page: number, per_page: number = 10): RecentLinksQueryKey {
  return ['recentLinks', { page, per_page }];
}

export type RecentLinksQueryKey = ['recentLinks', { page: number; per_page: number }];


export async function recentLinksQueryFn({ queryKey }: QueryFunctionContext<RecentLinksQueryKey>): Promise<PaginatedResponse<Link>> {
  const [, { page, per_page }] = queryKey;
  const params = new URLSearchParams({ page: String(page), per_page: String(per_page) });
  return fetchJson<PaginatedResponse<Link>>(`/links?${params.toString()}`);
}
