import React, { useCallback, useMemo, useState, useEffect, useRef } from 'react';

export interface FilterOptions {
  search: string;
  status: string;
  sortBy: string;
  sortOrder: string;
}

interface SearchAndFilterProps {
  filters: FilterOptions;
  onFiltersChange: (filters: FilterOptions) => void;
  isLoading?: boolean;
}

export const SearchAndFilter: React.FC<SearchAndFilterProps> = React.memo(({
  filters,
  onFiltersChange,
  isLoading = false,
}) => {
  const [searchValue, setSearchValue] = useState(filters.search);
  const timeoutRef = useRef<NodeJS.Timeout | null>(null);
  const filtersRef = useRef(filters);

  // Update filters ref when filters change
  useEffect(() => {
    filtersRef.current = filters;
  }, [filters]);

  // Update local search value when filters change externally
  useEffect(() => {
    setSearchValue(filters.search);
  }, [filters.search]);

  // Debounce search value
  useEffect(() => {
    // Clear existing timeout
    if (timeoutRef.current) {
      clearTimeout(timeoutRef.current);
    }

    // Set new timeout
    timeoutRef.current = setTimeout(() => {
      if (searchValue !== filtersRef.current.search) {
        onFiltersChange({
          ...filtersRef.current,
          search: searchValue,
        });
      }
    }, 300); // 300ms debounce

    return () => {
      if (timeoutRef.current) {
        clearTimeout(timeoutRef.current);
      }
    };
  }, [searchValue, onFiltersChange]);

  const handleSearchChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchValue(e.target.value);
  }, []);

  const handleStatusChange = useCallback((e: React.ChangeEvent<HTMLSelectElement>) => {
    onFiltersChange({
      ...filters,
      status: e.target.value,
    });
  }, [filters, onFiltersChange]);

  const handleSortByChange = useCallback((e: React.ChangeEvent<HTMLSelectElement>) => {
    onFiltersChange({
      ...filters,
      sortBy: e.target.value,
    });
  }, [filters, onFiltersChange]);

  const handleSortOrderChange = useCallback((e: React.ChangeEvent<HTMLSelectElement>) => {
    onFiltersChange({
      ...filters,
      sortOrder: e.target.value,
    });
  }, [filters, onFiltersChange]);

  const statusOptions = useMemo(() => [
    { value: '', label: 'All Status' },
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' },
  ], []);

  const sortByOptions = useMemo(() => [
    { value: 'created_at', label: 'Sort by Date' },
    { value: 'clicks_count', label: 'Sort by Clicks' },
    { value: 'title', label: 'Sort by Name' },
  ], []);

  const sortOrderOptions = useMemo(() => [
    { value: 'desc', label: 'Newest First' },
    { value: 'asc', label: 'Oldest First' },
  ], []);

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
      <div className="flex flex-col md:flex-row gap-4">
        <div className="flex-1">
          <div className="relative">
            <input
              type="text"
              placeholder="Search links..."
              value={filters.search}
              onChange={handleSearchChange}
              disabled={isLoading}
              className="w-full px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
            />
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
        </div>
        <div className="flex gap-2">
          <select
            value={filters.status}
            onChange={handleStatusChange}
            disabled={isLoading}
            className="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {statusOptions.map((option) => (
              <option key={option.value} value={option.value}>
                {option.label}
              </option>
            ))}
          </select>
          <select
            value={filters.sortBy}
            onChange={handleSortByChange}
            disabled={isLoading}
            className="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {sortByOptions.map((option) => (
              <option key={option.value} value={option.value}>
                {option.label}
              </option>
            ))}
          </select>
          <select
            value={filters.sortOrder}
            onChange={handleSortOrderChange}
            disabled={isLoading}
            className="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {sortOrderOptions.map((option) => (
              <option key={option.value} value={option.value}>
                {option.label}
              </option>
            ))}
          </select>
        </div>
      </div>
    </div>
  );
});
