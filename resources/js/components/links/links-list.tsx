import React, { useCallback, useMemo, useState } from 'react';
import { LinkCard } from '../links/link-card';
import { Link } from '../../queries/links/link-interface';

interface LinksListProps {
  links: Link[];
  isLoading?: boolean;
  onBulkDelete?: (linkIds: number[]) => void;
  onBulkActivate?: (linkIds: number[]) => void;
  onBulkDeactivate?: (linkIds: number[]) => void;
  onEdit?: (link: Link) => void;
  onDelete?: (link: Link) => void;
  onCopy?: (link: Link) => void;
  onViewAnalytics?: (link: Link) => void;
}

export const LinksList: React.FC<LinksListProps> = React.memo(({
  links,
  isLoading = false,
  onBulkDelete,
  onBulkActivate,
  onBulkDeactivate,
  onEdit,
  onDelete,
  onCopy
}) => {
  const [selectedLinks, setSelectedLinks] = useState<Set<number>>(new Set());

  const handleSelectAll = useCallback((checked: boolean) => {
    if (checked) {
      setSelectedLinks(new Set(links.map(link => link.id)));
    } else {
      setSelectedLinks(new Set());
    }
  }, [links]);

  const handleSelectLink = useCallback((linkId: number, checked: boolean) => {
    setSelectedLinks(prev => {
      const newSet = new Set(prev);
      if (checked) {
        newSet.add(linkId);
      } else {
        newSet.delete(linkId);
      }
      return newSet;
    });
  }, []);

  const handleBulkDelete = useCallback(() => {
    if (onBulkDelete && selectedLinks.size > 0) {
      onBulkDelete(Array.from(selectedLinks));
      setSelectedLinks(new Set());
    }
  }, [onBulkDelete, selectedLinks]);

  const handleBulkActivate = useCallback(() => {
    if (onBulkActivate && selectedLinks.size > 0) {
      onBulkActivate(Array.from(selectedLinks));
      setSelectedLinks(new Set());
    }
  }, [onBulkActivate, selectedLinks]);

  const handleBulkDeactivate = useCallback(() => {
    if (onBulkDeactivate && selectedLinks.size > 0) {
      onBulkDeactivate(Array.from(selectedLinks));
      setSelectedLinks(new Set());
    }
  }, [onBulkDeactivate, selectedLinks]);

  const allSelected = useMemo(() => {
    return links.length > 0 && selectedLinks.size === links.length;
  }, [links.length, selectedLinks.size]);

  const someSelected = useMemo(() => {
    return selectedLinks.size > 0 && selectedLinks.size < links.length;
  }, [selectedLinks.size, links.length]);

  const selectedCount = selectedLinks.size;

  if (isLoading) {
    return (
      <div className="space-y-4">
        {Array.from({ length: 6 }).map((_, index) => (
          <div key={index} className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 animate-pulse">
            <div className="flex items-center space-x-4">
              <div className="w-4 h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
              <div className="flex-1 space-y-2">
                <div className="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
                <div className="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
              </div>
              <div className="w-20 h-8 bg-gray-300 dark:bg-gray-600 rounded"></div>
            </div>
          </div>
        ))}
      </div>
    );
  }

  if (links.length === 0) {
    return (
      <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
        <div className="w-16 h-16 mx-auto mb-4 text-gray-400">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
          </svg>
        </div>
        <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No links found</h3>
        <p className="text-gray-500 dark:text-gray-400">Get started by creating your first link.</p>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      {/* Select All Checkbox with Bulk Actions */}
      <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div className="flex items-center justify-between">
          <label className="flex items-center space-x-3 cursor-pointer">
            <input
              type="checkbox"
              checked={allSelected}
              ref={(el) => {
                if (el) el.indeterminate = someSelected;
              }}
              onChange={(e) => handleSelectAll(e.target.checked)}
              className="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            />
            <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
              Select all links
            </span>
            {selectedCount > 0 && (
              <span className="text-sm text-blue-600 dark:text-blue-400 font-medium">
                ({selectedCount} selected)
              </span>
            )}
          </label>

          {/* Bulk Actions */}
          {selectedCount > 0 && (
            <div className="flex items-center space-x-2">
              <button
                onClick={handleBulkActivate}
                className="px-3 py-1.5 text-sm font-medium text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-900/30 hover:bg-green-200 dark:hover:bg-green-900/50 rounded-md transition-colors"
              >
                Activate
              </button>
              <button
                onClick={handleBulkDeactivate}
                className="px-3 py-1.5 text-sm font-medium text-orange-700 dark:text-orange-300 bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 dark:hover:bg-orange-900/50 rounded-md transition-colors"
              >
                Deactivate
              </button>
              <button
                onClick={handleBulkDelete}
                className="px-3 py-1.5 text-sm font-medium text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-md transition-colors"
              >
                Delete
              </button>
            </div>
          )}
        </div>
      </div>

      {/* Links Grid */}
      
        {links.map((link) => (
          <LinkCard
            key={link.id}
            link={link}
            isSelected={selectedLinks.has(link.id)}
            onSelect={handleSelectLink}
            onEdit={onEdit || (() => {})}
            onDelete={onDelete || (() => {})}
            onCopy={onCopy || (() => {})}
          />
        ))}
      
    </div>
  );
});
