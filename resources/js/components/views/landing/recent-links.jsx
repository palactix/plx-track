import { ImageWithFallback } from '@/components/figma/ImageWithFallback';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { BarChart3, Check, Copy, Eye, RefreshCw } from 'lucide-react';
import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Link } from '@inertiajs/react';
import { toast } from 'react-hot-toast';
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
            <Card key={link.id} className="bg-white border-gray-200 dark:bg-slate-800 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-all duration-300">
              <CardContent className="">
                <div className="flex flex-col sm:flex-row sm:items-center gap-4">
                  {/* Main content (image + info) */}
                  <div className="flex flex-1 gap-4 max-w-[70vw] sm:max-w-[600px]">
                    <div className="w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden flex-shrink-0 bg-gray-200 dark:bg-slate-700 flex items-center justify-center">
                      <ImageWithFallback
                        src={link.og_image_url}
                        alt={`${link.domain} preview`}
                        className="w-full h-full object-cover"
                      />
                    </div>
                    <div className="flex-1 min-w-0">
                      <h3 className="font-medium text-gray-900 dark:text-white mb-1 truncate overflow-hidden whitespace-nowrap max-w-full">{link.title}</h3>
                      <div className="flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-slate-400 mb-2">
                        <span className="flex items-center gap-1">
                          <span className="w-2 h-2 rounded-full bg-secondary"></span>
                          {new URL(link.original_url).host}
                        </span>
                        <span>•</span>
                        <span>{new Date(link.created_at).toLocaleString()}</span>
                        <span>•</span>
                        <span className="font-mono text-xs text-primary bg-gray-100 dark:bg-slate-700 px-2 py-0.5 rounded">
                          {link.short_code}
                        </span>
                      </div>
                      <div className="text-sm text-gray-500 dark:text-slate-400 truncate overflow-hidden whitespace-nowrap max-w-full">
                        {link.description || link.original_url}
                      </div>
                    </div>
                  </div>
                  {/* Stats/actions row, stacks below on mobile, right on desktop */}
                  <div className="flex items-center gap-2 mt-2 sm:mt-0 sm:ml-auto order-last sm:order-none">
                    <span className="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-slate-700/50 text-xs text-gray-700 dark:text-slate-300 font-medium">
                      {link.clicks_count} clicks
                    </span>
                    <a href={`/${link.short_code}`}>
                      <Button 
                        size="icon"
                        variant="ghost"
                        className="w-8 h-8 p-0 text-gray-500 dark:text-slate-400 hover:text-primary hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg cursor-pointer"
                        title="Preview"
                      >
                        <Eye className="w-4 h-4" />
                      </Button>
                    </a>
                    <CopyButton link={link} />
                    <Link href={`links/analytics/${link.short_code}`}>
                      <Button 
                        size="icon"
                        variant="ghost"
                        className="w-8 h-8 p-0 text-secondary hover:text-white rounded-lg bg-primary/20 hover:bg-primary/30 cursor-pointer"
                        title="View Analytics"
                      >
                        <BarChart3 className="w-4 h-4 text-secondary" />
                      </Button>
                    </Link>
                  </div>
                </div>
                
              </CardContent>
            </Card>
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