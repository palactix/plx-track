import { ImageWithFallback } from '@/components/figma/ImageWithFallback';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { BarChart3, Eye, RefreshCw, Globe, ExternalLink } from 'lucide-react';
import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Link } from '@inertiajs/react';
import { recentLinksQueryFn, recentLinksQueryKey } from '@/queries/links/use-links';
import { CopyButton } from '@/components/common/links/copy';

export function RecentLinks() {
  const [page, setPage] = useState(1);
  
  const perPage = 10;
  const query = useQuery({
    queryKey: recentLinksQueryKey(page, perPage),
    queryFn: recentLinksQueryFn,
    staleTime: 30_000,
    keepPreviousData: true,
  });

  const items = query.data?.data.items || [];
  const meta = query.data?.data.meta;
  const isEmpty = !query.isLoading && items.length === 0;


  return (
    <section className="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 mb-8">
      <div className="max-w-4xl mx-auto">
        {/* Section Header */}
        <div className="mb-6 sm:mb-8">
          <div className="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between">
            <h2 className="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white flex-shrink-0">Recent Public Links</h2>
            <Button 
              variant="secondary" 
              size="sm" 
              className="self-start sm:self-auto bg-gray-100 border-gray-300 text-gray-900 hover:bg-gray-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600"
              onClick={() => { setPage(1); query.refetch(); }}
              disabled={query.isFetching}
            >
              <RefreshCw className={"w-4 h-4 mr-2 " + (query.isFetching ? 'animate-spin' : '')} />
              {query.isFetching ? 'Refreshing' : 'Refresh'}
            </Button>
          </div>
        </div>
        {query.isLoading && (
          <div className="text-sm text-gray-500 dark:text-slate-400">Loading recent links...</div>
        )}
        {query.isError && (
          <div className="text-sm text-red-500">Failed to load links.</div>
        )}
        {isEmpty && (
          <div className="text-sm text-gray-500 dark:text-slate-400">No public links yet.</div>
        )}
        <div className="space-y-4">
          {items.map((link) => (
            <div key={link.id} className="group">
              <Card className=" py-0  hover:border-slate-300 dark:hover:border-slate-600 transition-colors rounded-xl">
                <CardContent className="p-4">
                  <div className="flex items-center gap-4">
                    {/* Icon */}
                    <div className="flex-shrink-0 w-10 h-10 flex items-center justify-center">
                      <ImageWithFallback src={link.og_image_url} alt={link.title} className="" />
                    </div>
                    
                    {/* Main content */}
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center gap-3 mb-1">
                        <span className="font-medium text-sm text-slate-900 dark:text-white">{`${window.location.host}/${link.short_code}`}</span>
                        <CopyButton link={link.short_code} />
                      </div>
                      <div className="flex items-center text-sm text-slate-500 dark:text-slate-400">
                        <ExternalLink className="h-3 w-3 mr-1 flex-shrink-0" />
                        <a
                          href={`/${link.short_code}`}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="truncate hover:text-slate-700 dark:hover:text-slate-300 transition-colors"
                        >
                          {link.original_url.replace(/^https?:\/\//, '')}
                        </a>
                      </div>
                    </div>
                    
                    {/* Clicks counter */}
                    <div className="flex-shrink-0 flex items-center gap-1 text-sm text-slate-600 dark:text-slate-300">
                      <BarChart3 className="h-4 w-4 text-primary dark:text-primary-400" />
                      <span className="font-medium">{link.clicks_count}</span>
                      <span className="text-slate-500 dark:text-slate-400">clicks</span>
                    </div>
                    
                    {/* Actions */}
                    <div className="flex-shrink-0 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                      <a href={`/${link.short_code}`}>
                        <Button
                          variant="ghost"
                          size="sm"
                          className="h-8 px-2 text-primary dark:text-primary-400 hover:bg-primary/20 dark:hover:bg-primary/20"
                        >
                          <Eye className="h-3 w-3" />
                        </Button>
                      </a>
                      <Link href={`links/analytics/${link.short_code}`}>
                        <Button
                          variant="ghost"
                          size="sm"
                          className="h-8 px-2 text-primary dark:text-primary-400 hover:bg-primary/20 dark:hover:bg-primary/20"
                        >
                          <BarChart3 className="h-3 w-3" />
                        </Button>
                      </Link>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          ))}

          
        </div>
        {meta && meta.last_page > 1 && (
          <div className="mt-6 flex items-center justify-center gap-3">
            <Button size="sm" variant="outline" disabled={page === 1 || query.isFetching} onClick={() => setPage(p => Math.max(1, p - 1))}>Prev</Button>
            <span className="text-xs text-gray-600 dark:text-slate-300">Page {meta.current_page} / {meta.last_page}</span>
            <Button size="sm" variant="outline" disabled={page === meta.last_page || query.isFetching} onClick={() => setPage(p => Math.min(meta.last_page, p + 1))}>Next</Button>
          </div>
        )}
      </div>
    </section>
  );
}