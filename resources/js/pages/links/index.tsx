import AppLayout from "@/layouts/app-layout";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";

export default function LinkPage() {
  return (
    <AppLayout>
      <div className="container mx-auto px-4 py-8 dark:bg-gray-900 dark:text-white">
        {/* Header Section */}
        <div className="flex justify-between items-center mb-8">
          <div>
            <h1 className="text-3xl font-bold text-gray-900 dark:text-white">Links</h1>
            <p className="text-gray-600 dark:text-gray-400 mt-2">Manage and track your shortened links</p>
          </div>
          <Button>
            Create New Link
          </Button>
        </div>

        {/* Bulk Actions Bar */}
        <div className="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-4 mb-6 hidden" id="bulk-actions">
          <div className="flex justify-between items-center">
            <div className="flex items-center space-x-2">
              <svg className="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span className="text-sm font-medium text-blue-800 dark:text-blue-200">Selected 0 items</span>
            </div>
            <div className="flex space-x-2">
              <Button variant="destructive" size="sm">
                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Selected
              </Button>
              <Button variant="outline" size="sm">
                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Deactivate
              </Button>
            </div>
          </div>
        </div>

        {/* Search and Filter Section */}
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
          <div className="flex flex-col md:flex-row gap-4">
            <div className="flex-1">
              <input
                type="text"
                placeholder="Search links..."
                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
              />
            </div>
            <div className="flex gap-2">
              <select className="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option>All Status</option>
                <option>Active</option>
                <option>Inactive</option>
              </select>
              <select className="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option>Sort by Date</option>
                <option>Sort by Clicks</option>
                <option>Sort by Name</option>
              </select>
            </div>
          </div>
        </div>

        {/* Links List - Card-based */}
        <div className="space-y-3">
          {/* Header with Select All */}
          <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-3">
                <Checkbox className="border-gray-300 dark:border-gray-600 data-[state=checked]:bg-blue-600 data-[state=checked]:border-blue-600" />
                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Select All</span>
              </div>
              <span className="text-sm text-gray-500 dark:text-gray-400">6 links</span>
            </div>
          </div>

          {/* Placeholder cards */}
          {[
            { id: 1, title: "Summer Sale 2025", shortUrl: "plx.to/summer-sale", originalUrl: "https://example.com/summer-sale-2025", clicks: 1247, status: "Active" },
            { id: 2, title: "Product Launch Event", shortUrl: "plx.to/product-launch", originalUrl: "https://example.com/new-product-launch", clicks: 892, status: "Active" },
            { id: 3, title: "Black Friday Deals", shortUrl: "plx.to/black-friday", originalUrl: "https://example.com/black-friday-discounts", clicks: 2156, status: "Active" },
            { id: 4, title: "Newsletter Signup", shortUrl: "plx.to/newsletter", originalUrl: "https://example.com/subscribe-newsletter", clicks: 543, status: "Inactive" },
            { id: 5, title: "Customer Survey", shortUrl: "plx.to/survey", originalUrl: "https://example.com/customer-feedback-survey", clicks: 321, status: "Active" },
            { id: 6, title: "Holiday Promotion", shortUrl: "plx.to/holiday-promo", originalUrl: "https://example.com/holiday-special-offers", clicks: 987, status: "Active" }
          ].map((link) => (
            <div key={link.id} className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow duration-300">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <Checkbox className="border-gray-300 dark:border-gray-600 data-[state=checked]:bg-blue-600 data-[state=checked]:border-blue-600" />
                  <div className="flex-shrink-0 h-10 w-10">
                    <div className="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                      <span className="text-white font-bold text-sm">L{link.id}</span>
                    </div>
                  </div>
                </div>
                <div className="flex items-center space-x-3 flex-1 min-w-0 ml-3">
                  <div className="flex-1 min-w-0">
                    <h3 className="text-base font-semibold text-gray-900 dark:text-white truncate">{link.title}</h3>
                    <div className="flex items-center space-x-4 mt-1">
                      <div className="flex items-center space-x-1">
                        <p className="text-sm text-gray-700 dark:text-gray-300 truncate">{link.shortUrl}</p>
                        <button className="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                          <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                          </svg>
                        </button>
                      </div>
                      <span className="text-xs text-gray-500 dark:text-gray-400 hidden sm:inline">•</span>
                      <p className="text-sm text-gray-600 dark:text-gray-400 truncate hidden sm:block">{link.originalUrl}</p>
                    </div>
                  </div>
                </div>
                <div className="flex items-center space-x-3 ml-4">
                  <div className="flex items-center space-x-1">
                    <svg className="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clipRule="evenodd" />
                    </svg>
                    <span className="text-sm font-medium text-gray-900 dark:text-white">{link.clicks}</span>
                  </div>
                  <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                    link.status === 'Active' 
                      ? 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200' 
                      : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200'
                  }`}>
                    {link.status}
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
                      <DropdownMenuItem>
                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                      </DropdownMenuItem>
                      <DropdownMenuItem>
                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Copy Link
                      </DropdownMenuItem>
                      <DropdownMenuItem>
                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        View Analytics
                      </DropdownMenuItem>
                      <DropdownMenuItem variant="destructive">
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
                <p className="text-xs text-gray-600 dark:text-gray-400 truncate">{link.originalUrl}</p>
              </div>
            </div>
          ))}
        </div>

        {/* Pagination */}
        <div className="flex justify-between items-center mt-6">
          <div className="text-sm text-gray-700 dark:text-gray-300">
            Showing 1 to 5 of 25 results
          </div>
          <div className="flex space-x-2">
            <Button variant="outline" size="sm">Previous</Button>
            <Button variant="default" size="sm">1</Button>
            <Button variant="outline" size="sm">2</Button>
            <Button variant="outline" size="sm">3</Button>
            <Button variant="outline" size="sm">Next</Button>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}