// Dashboard interfaces

export interface OverviewStat {
  title: string;
  value: string;
  change: string;
  icon: string;
}

export interface ChartDataPoint {
  date: string;
  clicks: number;
}

export interface RecentLink {
  id: string;
  title: string;
  originalUrl: string;
  shortUrl: string;
  clicks: number;
  created: string;
  status: string;
}

export interface DashboardFilters {
  period?: '7days' | '30days' | 'custom';
  start_date?: string;
  end_date?: string;
}

export interface OverviewStatsResponse {
  success: boolean;
  data: OverviewStat[];
}

export interface ChartDataResponse {
  success: boolean;
  data: ChartDataPoint[];
}

export interface RecentLinksResponse {
  success: boolean;
  data: RecentLink[];
}
