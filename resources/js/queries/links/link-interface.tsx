// add all links interface heres

import { Pagination } from "@/types";

export interface Link {
  id: number;
  short_code: string;
  original_url: string;
  title: string;
  description: string;
  og_image_url: string;
  clicks_count: number;
  unique_clicks_count: number;

  meta_title: string;
  meta_description: string;

  expires_at: string;
  short_url: string;
  platform: string;
  
  created_at: string;
  updated_at: string;

  is_active: boolean;
}

export interface LinksResponse {
  data: {
    items: Link[];
    meta: {
      total: number;
      currentPage: number;
      lastPage: number;
    };
  };
}


// add laravel default paginated response items to this
export interface PaginatedLinksResponse extends Pagination<Link> {
  // extra items
  total_active: number;
  total_clicks: number;
  month_percent: number;
}
