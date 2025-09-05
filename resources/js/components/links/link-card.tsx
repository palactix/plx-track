import React, { useCallback } from 'react';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Link } from '@/queries/links/link-interface';
import { CopyButton } from '../common/links/copy';
import { ImageWithFallback } from '../figma/ImageWithFallback';
import { Link as LinkHref } from '@inertiajs/react';

interface LinkCardProps {
  link: Link;
  isSelected: boolean;
  onSelect: (linkId: number, selected: boolean) => void;
  onEdit: (link: Link) => void;
  onDelete: (link: Link) => void;
  onCopy: (link: Link) => void;
}

export const LinkCard: React.FC<LinkCardProps> = React.memo(({
  link,
  isSelected,
  onSelect,
  onEdit,
  onDelete,
  onCopy
}) => {
  const handleSelectChange = useCallback((checked: boolean) => {
    onSelect(link.id, checked);
  }, [link.id, onSelect]);

  const handleEdit = useCallback(() => {
    onEdit(link);
  }, [link, onEdit]);

  const handleDelete = useCallback(() => {
    onDelete(link);
  }, [link, onDelete]);

  const handleCopy = useCallback(() => {
    onCopy(link);
  }, [link, onCopy]);


  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-2 hover:shadow-md transition-shadow duration-300">
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-3">
          <Checkbox
            checked={isSelected}
            onCheckedChange={handleSelectChange}
            className="border-gray-300 dark:border-gray-600 data-[state=checked]:bg-blue-600 data-[state=checked]:border-blue-600"
          />
          <div className="flex-shrink-0 h-10 w-10">
            {link.og_image_url ? (
              <div className="h-10 w-10 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <ImageWithFallback src={link.og_image_url} />
              </div>
            ) : (
              <div className="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                <span className="text-white font-bold text-sm">L{link.id}</span>
              </div>
            )}
          </div>
        </div>
        <div className="flex items-center space-x-3 flex-1 min-w-0 ml-3">
          <div className="flex-1 min-w-0">
            <h3 className="text-base font-semibold text-gray-900 dark:text-white truncate">
              {link.title || `Link ${link.id}`}
            </h3>
            <div className="flex items-center space-x-4 mt-1">
              <div className="flex items-center space-x-1">
                <p className="text-sm text-gray-700 dark:text-gray-300 truncate">{link.short_code}</p>
                <CopyButton link={link}  />
              </div>
              <span className="text-xs text-gray-500 dark:text-gray-400 hidden sm:inline">•</span>
              <p className="text-sm text-gray-600 dark:text-gray-400 truncate hidden sm:block">{link.original_url}</p>
            </div>
          </div>
        </div>
        <div className="flex items-center space-x-3 ml-4">
          <div className="flex items-center space-x-1">
            <svg className="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clipRule="evenodd" />
            </svg>
            <span className="text-sm font-medium text-gray-900 dark:text-white">{link.clicks_count}</span>
          </div>
          <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
            link.is_active
              ? 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200'
              : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200'
          }`}>
            {link.is_active ? 'Active' : 'Inactive'}
          </span>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="icon" className="h-8 w-8">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              {/* <DropdownMenuItem onClick={handleEdit}>
                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
              </DropdownMenuItem> */}
              <DropdownMenuItem>
                <LinkHref href={`/links/analytics/${link.short_code}`} className="flex">
                  <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                  Analytics
                </LinkHref>
              </DropdownMenuItem>
              <DropdownMenuItem variant="destructive" onClick={handleDelete}>
                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
      {/* Mobile-only original URL */}
      <div className="mt-2 sm:hidden">
        <p className="text-xs text-gray-600 dark:text-gray-400 truncate">{link.original_url}</p>
      </div>
    </div>
  );
});
