// add all links interface heres

export interface Link {
  id: string;
  title: string;
  url: string;
  short_code: string;
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
