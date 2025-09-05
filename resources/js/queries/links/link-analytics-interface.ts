export interface ChartPoint {
  date: string; // label e.g. 'Sep 4'
  iso: string; // iso date string yyyy-mm-dd
  clicks: number;
}

export interface BrowserDatum {
  name: string;
  value: number;
  percentage: number;
  color?: string;
}

export interface RecentClick {
  id: number;
  ip_address?: string | null;
  user_agent?: string | null;
  browser: string;
  platform: string;
  country: string;
  referrer: string;
  created_at: string; // ISO
  date: string;
  time: string;
}

export interface LinkAnalyticsApi {
  total_clicks: number;
  clicks_7_days: number;
  clicks_30_days: number;
  chart_data: ChartPoint[];
  browsers: BrowserDatum[];
  recent_clicks: RecentClick[];
}
