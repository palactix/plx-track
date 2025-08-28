

export interface PaginatedMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface PaginatedResponse<T> {
  success: boolean;
  data: {
    items: T[];
    meta: PaginatedMeta;
  };
}

const baseHeaders: HeadersInit = {
  'Accept': 'application/json',
  'X-Requested-With': 'XMLHttpRequest'
};

export async function fetchJson<T>(url: string, init?: RequestInit): Promise<T> {
  const resp = await fetch(url, { ...init, headers: { ...baseHeaders, ...(init?.headers || {}) } });
  if (!resp.ok) {
    const text = await resp.text();
    throw new Error(`Request failed ${resp.status}: ${text.slice(0,200)}`);
  }
  return resp.json() as Promise<T>;
}

