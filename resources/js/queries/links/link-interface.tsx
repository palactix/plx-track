// add all links interface heres

export interface Link {
  id: number;
  short_code: string;
  original_url: string;
  title: string;
  description: string;
  og_image_url: string;
  clicks_count: number;
  created_at: string;
  short_url: string;
  platform: string;
  createdAt: string;
  updatedAt: string;
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
