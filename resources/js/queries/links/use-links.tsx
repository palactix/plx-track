import { useQuery, type QueryFunctionContext } from '@tanstack/react-query';

import { fetchJson, PaginatedResponse } from "@/lib/apiClient";
import { Link, PaginatedLinksResponse } from "./link-interface";



export function recentLinksQueryKey(page: number, per_page: number = 10): RecentLinksQueryKey {
  return ['recentLinks', { page, per_page }];
}

export type RecentLinksQueryKey = ['recentLinks', { page: number; per_page: number }];


export async function recentLinksQueryFn({ queryKey }: QueryFunctionContext<RecentLinksQueryKey>): Promise<PaginatedResponse<Link>> {
  const [, { page, per_page }] = queryKey;
  const params = new URLSearchParams({ page: String(page), per_page: String(per_page) });
  return fetchJson<PaginatedResponse<Link>>(`/public-links?${params.toString()}`);
}



type Params = {
    search?: string;
    status?: string;
    from?: string;
    to?: string;
    sortBy?: string;
    sortOrder?: string;
    page?: number;
    perPage?: number;
};

export function useLinks(params: Params) {
    const queryKey = ["links", params];

    return useQuery<PaginatedLinksResponse, Error>({
        queryKey,
        queryFn: async () => {
            const url = new URL("/api/links", window.location.origin);
            Object.entries(params || {}).forEach(([k, v]) => {
                if (v !== undefined && v !== null && String(v) !== "") {
                    url.searchParams.set(k, String(v));
                }
            });

            const res = await fetch(url.toString(), { credentials: "same-origin" });
            if (!res.ok) {
                throw new Error("Failed to fetch links");
            }
            return (await res.json()) as PaginatedLinksResponse;
        },
    });
}
