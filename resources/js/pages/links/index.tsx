import React, { useCallback, useMemo, useState } from 'react';
import AppLayout from "@/layouts/app-layout";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogTrigger,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { LinkGenerator } from '@/components/common/links/link-generator';
import { useLinks } from '@/queries/links/use-links';
import { SearchAndFilter, FilterOptions } from '@/components/common/search-and-filter';
import { LinksList } from '@/components/links/links-list';
import { Pagination } from '@/components/common/pagination';
import { Link } from '@/queries/links/link-interface';

export default function LinkPage() {
  const [filters, setFilters] = useState<FilterOptions>({
    search: '',
    status: '',
    sortBy: 'created_at',
    sortOrder: 'desc',
  });

  const { data, isLoading, error } = useLinks({
    page: 1,
    perPage: 12,
    search: filters.search,
    status: filters.status,
    sortBy: filters.sortBy,
    sortOrder: filters.sortOrder,
  });

  const handleFiltersChange = useCallback((newFilters: FilterOptions) => {
    setFilters(newFilters);
  }, []);

  const handleBulkDelete = useCallback(async (linkIds: number[]) => {
    // TODO: Implement bulk delete
    console.log('Bulk delete:', linkIds);
  }, []);

  const handleBulkActivate = useCallback(async (linkIds: number[]) => {
    // TODO: Implement bulk activate
    console.log('Bulk activate:', linkIds);
  }, []);

  const handleBulkDeactivate = useCallback(async (linkIds: number[]) => {
    // TODO: Implement bulk deactivate
    console.log('Bulk deactivate:', linkIds);
  }, []);

  const handleEdit = useCallback((link: Link) => {
    // TODO: Implement edit functionality
    console.log('Edit link:', link);
  }, []);

  const handleDelete = useCallback((link: Link) => {
    fetch(`/api/links/${link.id}`, { 
      method: 'DELETE',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
     })
      .then(response => {
        if (response.ok) {
          // Optionally refetch links or update state
          console.log('Link deleted successfully');
        } else {
          console.error('Failed to delete link');
        }
      })
      .catch(error => {
        console.error('Error deleting link:', error);
      });
  }, []);

  const handleCopy = useCallback((link: Link) => {
    navigator.clipboard.writeText(link.short_url);
    // TODO: Show toast notification
  }, []);


  const links = useMemo(() => data?.data || [], [data]);
  const pagination = useMemo(() => ({
    currentPage: data?.current_page || 1,
    lastPage: data?.last_page || 1,
    total: data?.total || 0,
    perPage: data?.per_page || 12,
  }), [data]);

  return (
    <AppLayout>
      <div className="container mx-auto px-4 py-8 dark:bg-gray-900 dark:text-white">
        {/* Header Section */}
        <div className="flex justify-between items-center mb-8">
          <div>
            <h1 className="text-3xl font-bold text-gray-900 dark:text-white">Links</h1>
            <p className="text-gray-600 dark:text-gray-400 mt-2">Manage and track your shortened links</p>
          </div>
          <Dialog>
            <DialogTrigger asChild>
              <Button>
                Create New Link
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Create New Link</DialogTitle>
              </DialogHeader>
              <div className="pt-2">
                <LinkGenerator />
              </div>
            </DialogContent>
          </Dialog>
        </div>

        {/* Search and Filter */}
        <SearchAndFilter
          filters={filters}
          onFiltersChange={handleFiltersChange}
          isLoading={isLoading}
        />

        {/* Links List */}
        <LinksList
          links={links}
          isLoading={isLoading}
          onBulkDelete={handleBulkDelete}
          onBulkActivate={handleBulkActivate}
          onBulkDeactivate={handleBulkDeactivate}
          onEdit={handleEdit}
          onDelete={handleDelete}
          onCopy={handleCopy}
        />

        {/* Pagination */}
        {pagination.lastPage > 1 && (
          <div className="mt-8">
            <Pagination
              currentPage={pagination.currentPage}
              lastPage={pagination.lastPage}
              total={pagination.total}
              perPage={pagination.perPage}
              onPageChange={(page) => {
                // TODO: Implement page change
                console.log('Change to page:', page);
              }}
              isLoading={isLoading}
            />
          </div>
        )}

        {/* Error State */}
        {error && (
          <div className="mt-8 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div className="flex">
              <div className="flex-shrink-0">
                <svg className="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                </svg>
              </div>
              <div className="ml-3">
                <h3 className="text-sm font-medium text-red-800 dark:text-red-200">
                  Error loading links
                </h3>
                <div className="mt-2 text-sm text-red-700 dark:text-red-300">
                  {error.message || 'Something went wrong while loading your links. Please try again.'}
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </AppLayout>
  );
}