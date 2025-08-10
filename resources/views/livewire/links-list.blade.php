<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Links Management</h1>
            <p class="text-muted-foreground">Manage and monitor all your short links</p>
        </div>
        
        {{-- Quick Stats & Test Button --}}
        <div class="flex items-center gap-4">
            <div class="grid grid-cols-3 gap-4 lg:gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-500">{{ number_format($totalLinks) }}</div>
                    <div class="text-xs text-muted-foreground">Total Links</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-500">{{ number_format($activeLinks) }}</div>
                    <div class="text-xs text-muted-foreground">Active</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-cyan-500">{{ number_format($totalClicks) }}</div>
                    <div class="text-xs text-muted-foreground">Total Clicks</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filter Bar --}}
    <div class="bg-background/30 border border-border/20 dark:border-gray-700 rounded-lg p-4">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Search Input --}}
            <div class="flex-1">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search links by title, URL, short code, or custom alias..."
                        class="w-full bg-background/60 border border-border/30 rounded-lg px-4 py-2 pl-10 text-foreground focus:border-purple-500/40 transition-colors dark:border-gray-700"
                    >
                    <svg class="w-4 h-4 text-muted-foreground absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            {{-- Quick Filters --}}
            <div class="flex items-center gap-2">
                <select wire:model.live="status" 
                        class="bg-background/60 border border-border/30 rounded-lg px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors dark:border-gray-700">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="expired">Expired</option>
                    <option value="deleted">Deleted</option>
                </select>

                <select wire:model.live="perPage" 
                        class="bg-background/60 border border-border/30 rounded-lg px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors dark:border-gray-700">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </select>

                <button wire:click="toggleFilters" 
                        class="bg-purple-500/20 hover:bg-purple-500/30 text-purple-500 hover:text-purple-400 transition-all duration-300 rounded-lg px-4 py-2 border border-purple-500/30">
                    {{ $showFilters ? 'Hide Filters' : 'More Filters' }}
                </button>
            </div>
        </div>

        {{-- Advanced Filters (Collapsible) --}}
        @if($showFilters)
            <div class="mt-4 pt-4 border-t border-border/30 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Date Filter --}}
                    <div>
                        <label class="block text-sm text-muted-foreground mb-1">Created Date</label>
                        <select wire:model.live="dateFilter" 
                                class="w-full bg-background/60 border border-border/30 rounded-lg px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors dark:border-gray-700">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">Last 7 Days</option>
                            <option value="month">Last 30 Days</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    {{-- Custom Date Range --}}
                    @if($dateFilter === 'custom')
                        <div>
                            <label class="block text-sm text-muted-foreground mb-1">Start Date</label>
                            <input type="date" 
                                   wire:model.live="startDate" 
                                   class="w-full bg-background/60 border border-border/30 rounded-lg px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors dark:border-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm text-muted-foreground mb-1">End Date</label>
                            <input type="date" 
                                   wire:model.live="endDate" 
                                   class="w-full bg-background/60 border border-border/30 rounded-lg px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors dark:border-gray-700">
                        </div>
                    @endif
                </div>

                <div class="mt-4 flex justify-end">
                    <button wire:click="resetFilters" 
                            class="bg-gray-500/20 hover:bg-gray-500/30 text-gray-500 hover:text-gray-400 transition-all duration-300 rounded-lg px-4 py-2 border border-gray-500/30">
                        Reset Filters
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Bulk Actions Bar --}}
    @if($showBulkActions)
        <div class="bg-cyan-500/10 border border-cyan-500/30 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-cyan-700 dark:text-cyan-300 font-medium">
                        {{ count($selectedLinks) }} link(s) selected
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="bulkActivate" 
                            class="bg-green-500/20 hover:bg-green-500/30 text-green-600 hover:text-green-700 transition-all duration-300 rounded px-3 py-1 border border-green-500/30 text-sm">
                        Activate
                    </button>
                    <button wire:click="bulkDeactivate" 
                            class="bg-orange-500/20 hover:bg-orange-500/30 text-orange-600 hover:text-orange-700 transition-all duration-300 rounded px-3 py-1 border border-orange-500/30 text-sm">
                        Deactivate
                    </button>
                    <button wire:click="bulkDelete" 
                            wire:confirm="Are you sure you want to delete the selected links?"
                            class="bg-red-500/20 hover:bg-red-500/30 text-red-600 hover:text-red-700 transition-all duration-300 rounded px-3 py-1 border border-red-500/30 text-sm">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Links Table --}}
    <div class="bg-background/30 border border-border/20 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                {{-- Table Header --}}
                <thead class="bg-background/50 border-b border-border/30 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" 
                                   wire:model.live="selectAll"
                                   class="rounded border-border/30 text-purple-500 focus:ring-purple-500/20">
                        </th>
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sortBy('title')" class="flex items-center gap-1 text-muted-foreground hover:text-foreground transition-colors">
                                Link
                                @if($sortBy === 'title')
                                    <span class="text-purple-500">
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    </span>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sortBy('clicks_count')" class="flex items-center gap-1 text-muted-foreground hover:text-foreground transition-colors">
                                Clicks
                                @if($sortBy === 'clicks_count')
                                    <span class="text-purple-500">
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    </span>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sortBy('created_at')" class="flex items-center gap-1 text-muted-foreground hover:text-foreground transition-colors">
                                Created
                                @if($sortBy === 'created_at')
                                    <span class="text-purple-500">
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    </span>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody>
                    @forelse($links as $link)
                        <tr class="border-b border-border/20 dark:border-gray-700 hover:bg-background/50 transition-colors">
                            {{-- Checkbox --}}
                            <td class="px-4 py-4">
                                <input type="checkbox" 
                                       value="{{ $link->id }}" 
                                       wire:model.live="selectedLinks"
                                       class="rounded border-border/30 text-purple-500 focus:ring-purple-500/20">
                            </td>

                            {{-- Link Info --}}
                            <td class="px-4 py-4">
                                <div class="flex items-start gap-3">
                                    {{-- Thumbnail --}}
                                    @if($link->image)
                                        <img src="{{ $link->image }}" 
                                             alt="Preview" 
                                             class="w-12 h-12 object-cover rounded-lg border border-border/30 shadow-sm"
                                             onerror="this.style.display='none';">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500/20 to-cyan-500/20 rounded-lg border border-purple-500/30 flex items-center justify-center">
                                            <span class="text-purple-500 text-lg">🔗</span>
                                        </div>
                                    @endif

                                    {{-- Link Details --}}
                                    <div class="flex-1 min-w-0">
                                        {{-- Title --}}
                                        <div class="font-medium text-foreground mb-1 truncate">
                                            {{ $link->title ?: 'Untitled Link' }}
                                        </div>
                                        
                                        {{-- Short Code --}}
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-mono text-cyan-500 text-sm bg-cyan-500/10 px-2 py-1 rounded border border-cyan-500/20">
                                                {{ $link->short_code }}
                                            </span>
                                            @if($link->custom_alias)
                                                <span class="font-mono text-purple-500 text-sm bg-purple-500/10 px-2 py-1 rounded border border-purple-500/20">
                                                    {{ $link->custom_alias }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        {{-- Original URL --}}
                                        <div class="text-sm text-muted-foreground truncate">
                                            🌐 {{ parse_url($link->original_url, PHP_URL_HOST) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Clicks --}}
                            <td class="px-4 py-4">
                                <div class="text-lg font-bold text-purple-500">{{ number_format($link->clicks_count) }}</div>
                                <div class="text-xs text-muted-foreground">clicks</div>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($link->trashed())
                                        <span class="inline-flex items-center gap-1 bg-red-500/10 text-red-600 dark:text-red-400 px-2 py-1 rounded-full text-xs border border-red-500/20">
                                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                            Deleted
                                        </span>
                                    @elseif($link->is_active)
                                        @if($link->expires_at && $link->expires_at <= now())
                                            <span class="inline-flex items-center gap-1 bg-orange-500/10 text-orange-600 dark:text-orange-400 px-2 py-1 rounded-full text-xs border border-orange-500/20">
                                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                                Expired
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded-full text-xs border border-green-500/20">
                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                Active
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-gray-500/10 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full text-xs border border-gray-500/20">
                                            <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                            Inactive
                                        </span>
                                    @endif

                                    {{-- Password Protection --}}
                                    @if($link->password)
                                        <span class="inline-flex items-center gap-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 px-2 py-1 rounded-full text-xs border border-amber-500/20">
                                            🔒 Protected
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Created Date --}}
                            <td class="px-4 py-4">
                                <div class="text-sm text-foreground">{{ $link->created_at->format('M j, Y') }}</div>
                                <div class="text-xs text-muted-foreground">{{ $link->created_at->format('g:i A') }}</div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    @if($link->trashed())
                                        {{-- Restore Button --}}
                                        <button wire:click="restoreLink({{ $link->id }})" 
                                                class="p-2 rounded-lg bg-green-500/10 hover:bg-green-500/20 text-green-500 hover:text-green-600 transition-all duration-200 border border-green-500/20 hover:border-green-500/30" 
                                                title="Restore Link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>

                                        {{-- Permanent Delete Button --}}
                                        <button wire:click="forceDeleteLink({{ $link->id }})" 
                                                onclick="return confirm('Are you sure? This action cannot be undone!')"
                                                class="p-2 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-500 hover:text-red-600 transition-all duration-200 border border-red-500/20 hover:border-red-500/30" 
                                                title="Permanently Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @else
                                        {{-- Analytics Button --}}
                                        <button wire:click="$dispatch('openAnalytics', { linkId: {{ $link->id }} })" 
                                                class="p-2 rounded-lg bg-purple-500/10 hover:bg-purple-500/20 text-purple-500 hover:text-purple-600 transition-all duration-200 border border-purple-500/20 hover:border-purple-500/30" 
                                                title="View Analytics">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                        </button>

                                        {{-- Copy Button --}}
                                        <button onclick="copyLinkToClipboard('{{ config('app.url') }}/{{ $link->short_code }}', this)" 
                                                class="p-2 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-500 hover:text-cyan-600 transition-all duration-200 border border-cyan-500/20 hover:border-cyan-500/30" 
                                                title="Copy Link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>

                                        {{-- Toggle Status Button --}}
                                        <button wire:click="toggleLinkStatus({{ $link->id }})" 
                                                class="p-2 rounded-lg {{ $link->is_active ? 'bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 hover:text-orange-600 border-orange-500/20 hover:border-orange-500/30' : 'bg-green-500/10 hover:bg-green-500/20 text-green-500 hover:text-green-600 border-green-500/20 hover:border-green-500/30' }} transition-all duration-200 border" 
                                                title="{{ $link->is_active ? 'Deactivate' : 'Activate' }}">
                                            @if($link->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-8 0V9a3 3 0 013-3h2a3 3 0 013 3v5"></path>
                                                </svg>
                                            @endif
                                        </button>

                                        {{-- Delete Button --}}
                                        <button 
                                            x-data
                                            @click="
                                                $dispatch('open-modal', {
                                                    title: 'Delete Link',
                                                    message: 'Are you sure you want to delete this link? This action cannot be undone.',
                                                    confirmText: 'Delete',
                                                    confirmAction: () => $wire.call('deleteLink', {{ $link->id }})
                                                })
                                            "
                                            class="p-2 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-500 hover:text-red-600 transition-all duration-200 border border-red-500/20 hover:border-red-500/30" 
                                            title="Delete Link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-500/20 to-cyan-500/20 border border-purple-500/30 flex items-center justify-center">
                                    <span class="text-3xl">🔗</span>
                                </div>
                                <h3 class="text-lg font-medium text-foreground mb-2">No links found</h3>
                                <p class="text-muted-foreground">
                                    @if($search || $status !== 'all' || $dateFilter !== 'all')
                                        Try adjusting your search criteria or filters.
                                    @else
                                        Create your first short link to get started!
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($links->hasPages())
            <div class="px-4 py-3 border-t border-border/30 dark:border-gray-700">
                {{ $links->links() }}
            </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-background border border-border/30 rounded-lg p-6 w-full max-w-md">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-red-500/20 border border-red-500/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-foreground">Confirm Deletion</h3>
                        <p class="text-muted-foreground text-sm">This action cannot be undone.</p>
                    </div>
                </div>
                
                <p class="text-foreground mb-6">
                    Are you sure you want to delete this link? All associated analytics data will be preserved but the link will no longer be accessible.
                </p>
                
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelDelete" 
                            class="bg-gray-500/20 hover:bg-gray-500/30 text-gray-600 hover:text-gray-700 transition-all duration-300 rounded px-4 py-2 border border-gray-500/30">
                        Cancel
                    </button>
                    <button wire:click="deleteLink" 
                            class="bg-red-500 hover:bg-red-600 text-white transition-all duration-300 rounded px-4 py-2">
                        Delete Link
                    </button>
                </div>
            </div>
        </div>
    @endif


</div>

<script>
    // This function is now replaced by the global one in head.blade.php
    // But keeping it here for backward compatibility if needed
</script>
