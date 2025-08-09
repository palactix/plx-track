<div class="space-y-6">
    {{-- Header with Date Range Filter --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Dashboard</h1>
            <p class="text-muted-foreground">Monitor your link performance and analytics</p>
        </div>
        
        {{-- Date Range Filter --}}
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-sm text-muted-foreground">Date Range:</label>
                <select wire:model.live="dateRange" 
                        class="bg-background/60 border border-border/30 rounded px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors">
                    <option value="1">Today</option>
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 3 Months</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            
            {{-- Custom Date Range Inputs --}}
            @if($customDateRange)
                <div class="flex items-center gap-2">
                    <input type="date" 
                           wire:model.live="startDate" 
                           class="bg-background/60 border border-border/30 rounded px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors">
                    <span class="text-muted-foreground">to</span>
                    <input type="date" 
                           wire:model.live="endDate" 
                           class="bg-background/60 border border-border/30 rounded px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors">
                </div>
            @endif
            
            {{-- Refresh Button --}}
            <button wire:click="refreshDashboard" 
                    wire:loading.attr="disabled"
                    class="bg-purple-500/20 hover:bg-purple-500/30 text-purple-500 hover:text-purple-400 transition-all duration-300 hover:scale-105 rounded px-4 py-2 border border-purple-500/30">
                <span wire:loading.remove>🔄 Refresh</span>
                <span wire:loading>🔄 Refreshing...</span>
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Links --}}
        <x-dashboard.stat-card value="{{ number_format($totalLinks) }}" label="Total Links" icon="🔗" />

        {{-- Total Clicks --}}
        <x-dashboard.stat-card 
            value="{{ number_format($totalClicks) }}" 
            label="Total Clicks" 
            icon="👆" 
            text-color="text-cyan-500"
            icon-bg="from-cyan-500/20 to-cyan-500/30"
            icon-border="border-cyan-500/30" />

        {{-- Active Links --}}
        <x-dashboard.stat-card 
            value="{{ number_format($activeLinks) }}" 
            label="Active Links" 
            icon="✅" 
            text-color="text-green-500"
            icon-bg="from-green-500/20 to-green-500/30"
            icon-border="border-green-500/30" />

        {{-- Today's Clicks --}}
        <x-dashboard.stat-card 
            value="{{ number_format($todayClicks) }}" 
            label="Today's Clicks" 
            icon="📈" 
            text-color="text-orange-500"
            icon-bg="from-orange-500/20 to-orange-500/30"
            icon-border="border-orange-500/30" />
    </div>

    {{-- Quick Actions & Create Link Button --}}
    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Create New Link Button --}}
        <div class="lg:w-1/3">
            <x-dashboard.card class="text-center bg-gradient-to-r from-purple-500/20 to-cyan-500/20">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-r from-purple-500/30 to-cyan-500/30 flex items-center justify-center border border-purple-500/40">
                    <span class="text-2xl">➕</span>
                </div>
                <h3 class="text-lg font-bold text-foreground mb-2">Create Short Link</h3>
                <p class="text-muted-foreground text-sm mb-4">Generate a new short link with advanced options</p>
                <button wire:click="openLinkGenerator" 
                        class="bg-gradient-to-r from-purple-500 to-cyan-500 hover:from-purple-600 hover:to-cyan-600 text-white px-6 py-3 rounded transition-all duration-300 hover:scale-105 shadow-lg font-medium">
                    Create New Link
                </button>
            </x-dashboard.card>
        </div>

        {{-- Additional Quick Stats --}}
        <div class="lg:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Average Clicks per Link --}}
            <x-dashboard.stat-card 
                value="{{ $averageClicksPerLink }}" 
                label="Avg. Clicks/Link" 
                icon="📊" 
                text-color="text-blue-500"
                icon-bg="from-blue-500/20 to-blue-500/30"
                icon-border="border-blue-500/30" />

            {{-- Most Clicked Link --}}
            <x-dashboard.card>
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        @if($mostClickedLink)
                            <div class="text-lg font-bold text-pink-500">{{ $mostClickedLink->clicks_count }} clicks</div>
                            <div class="text-sm text-muted-foreground truncate">{{ $mostClickedLink->title ?: $mostClickedLink->short_code }}</div>
                        @else
                            <div class="text-lg font-bold text-pink-500">0</div>
                            <div class="text-sm text-muted-foreground">No clicks yet</div>
                        @endif
                    </div>
                    <div class="p-2 rounded-full bg-pink-500/20 border border-pink-500/30">
                        🏆
                    </div>
                </div>
            </x-dashboard.card>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Daily Clicks Chart --}}
        <x-dashboard.section>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-foreground">Daily Clicks</h3>
                <div class="p-2 rounded-full bg-cyan-500/20 border border-cyan-500/30">
                    📈
                </div>
            </div>
            
            <div class="h-64 w-full">
                @if(count($dailyClicksChart) > 0)
                    <div class="flex items-end justify-between h-full gap-2">
                        @foreach($dailyClicksChart as $day)
                            <div class="flex-1 flex flex-col items-center h-full">
                                <div class="flex-1 flex flex-col justify-end">
                                    <div class="bg-gradient-to-t from-cyan-500 to-purple-500 rounded-t-lg w-full transition-all duration-500 hover:scale-105"
                                         style="height: {{ $maxDailyClicks > 0 ? ($day['count'] / $maxDailyClicks) * 100 : 0 }}%; min-height: {{ $day['count'] > 0 ? '8px' : '2px' }}">
                                    </div>
                                </div>
                                <div class="text-xs text-muted-foreground mt-2 text-center">
                                    <div class="font-medium">{{ $day['count'] }}</div>
                                    <div>{{ $day['day'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <div class="text-4xl mb-2">📊</div>
                            <div class="text-muted-foreground">No clicks data available</div>
                        </div>
                    </div>
                @endif
            </div>
        </x-dashboard.section>

        {{-- Top Links Chart --}}
        <x-dashboard.section>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-foreground">Top Performing Links</h3>
                <div class="p-2 rounded-full bg-purple-500/20 border border-purple-500/30">
                    🏆
                </div>
            </div>
            
            <div class="space-y-3">
                @if(count($topLinksChart) > 0)
                    @foreach($topLinksChart as $index => $link)
                        <div class="flex items-center gap-3 p-3 bg-background/30 rounded-lg border border-border/20 dark:border-gray-700">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-r from-purple-500/30 to-cyan-500/30 flex items-center justify-center text-sm font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-foreground truncate">{{ $link['title'] }}</div>
                                <div class="text-sm text-purple-500 font-mono">{{ $link['short_code'] }}</div>
                            </div>
                            <div class="text-cyan-500 font-bold">{{ $link['clicks'] }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <div class="text-4xl mb-2">🔗</div>
                        <div class="text-muted-foreground">No link data available</div>
                    </div>
                @endif
            </div>
        </x-dashboard.section>
    </div>

    {{-- Click Sources Chart --}}
    @if(count($clickSourcesChart) > 0)
        <x-dashboard.section>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-foreground">Click Sources</h3>
                <div class="p-2 rounded-full bg-green-500/20 border border-green-500/30">
                    🌐
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($clickSourcesChart as $source)
                    <div class="text-center p-4 bg-background/30 rounded-lg border border-border/20 dark:border-gray-700">
                        <div class="text-lg font-bold text-green-500">{{ $source['count'] }}</div>
                        <div class="text-sm text-muted-foreground">{{ $source['source'] }}</div>
                    </div>
                @endforeach
            </div>
        </x-dashboard.section>
    @endif

    {{-- Data Tables Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Links Table --}}
        <x-dashboard.section>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-foreground">Recent Links</h3>
                <div class="p-2 rounded-full bg-purple-500/20 border border-purple-500/30">
                    🔗
                </div>
            </div>
            
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @if(count($recentLinks) > 0)
                    @foreach($recentLinks as $link)
                        <div class="group p-4 bg-background/30 rounded-lg border border-border/20 dark:border-gray-700 hover:bg-background/50 hover:border-purple-500/30 dark:hover:border-gray-600 transition-all duration-300">
                            <div class="flex items-start gap-4">
                                {{-- Link Preview Image --}}
                                @if($link['image'])
                                    <div class="flex-shrink-0">
                                        <img src="{{ $link['image'] }}" 
                                             alt="Preview" 
                                             class="w-12 h-12 object-cover rounded-lg border border-border/30 dark:border-gray-700 shadow-sm group-hover:scale-105 transition-transform duration-300"
                                             onerror="this.style.display='none';">
                                    </div>
                                @else
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500/20 to-cyan-500/20 rounded-lg border border-purple-500/30 flex items-center justify-center">
                                        <span class="text-purple-500 text-lg">🔗</span>
                                    </div>
                                @endif
                                
                                {{-- Link Information --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-mono text-cyan-500 text-sm font-medium">{{ $link['short_code'] }}</span>
                                        <div class="flex items-center gap-1">
                                            @if($link['is_active'])
                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                <span class="text-xs text-green-600 dark:text-green-400">Active</span>
                                            @else
                                                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                                <span class="text-xs text-red-600 dark:text-red-400">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <h4 class="font-medium text-foreground text-sm mb-1 truncate">
                                        {{ $link['title'] ?: parse_url($link['original_url'], PHP_URL_HOST) }}
                                    </h4>
                                    
                                    <p class="text-xs text-muted-foreground truncate mb-2">
                                        {{ parse_url($link['original_url'], PHP_URL_HOST) }} • {{ $link['created_at_human'] }}
                                    </p>
                                    
                                    {{-- Click Stats --}}
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-1">
                                            <span class="text-purple-500 text-lg font-bold">{{ $link['clicks_count'] }}</span>
                                            <span class="text-xs text-muted-foreground">clicks</span>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Action Buttons --}}
                                <div class="flex-shrink-0 flex items-center gap-2">
                                    <a href="{{ route('analytics', ['shortCode' => $link['short_code']]) }}" 
                                       class="p-2 rounded-lg bg-purple-500/10 hover:bg-purple-500/20 text-purple-500 hover:text-purple-600 transition-all duration-200 border border-purple-500/20 hover:border-purple-500/30" 
                                       title="View Analytics">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </a>
                                    <button onclick="copyLinkToClipboard('{{ config('app.url') }}/{{ $link['short_code'] }}', this)" 
                                            class="p-2 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-500 hover:text-cyan-600 transition-all duration-200 border border-cyan-500/20 hover:border-cyan-500/30" 
                                            title="Copy Link">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-500/20 to-cyan-500/20 border border-purple-500/30 flex items-center justify-center">
                            <span class="text-3xl">🔗</span>
                        </div>
                        <h4 class="text-lg font-medium text-foreground mb-2">No links created yet</h4>
                        <p class="text-muted-foreground text-sm">Create your first short link to get started!</p>
                    </div>
                @endif
            </div>
        </x-dashboard.section>

        {{-- Recent Clicks Activity --}}
        <x-dashboard.section>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-foreground">Recent Activity</h3>
                <div class="p-2 rounded-full bg-cyan-500/20 border border-cyan-500/30">
                    🎯
                </div>
            </div>
            
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @if(count($recentClicksActivity) > 0)
                    @foreach($recentClicksActivity as $click)
                        <div class="group p-4 bg-background/30 rounded-lg border border-border/20 dark:border-gray-700 hover:bg-background/50 hover:border-cyan-500/30 dark:hover:border-gray-600 transition-all duration-300">
                            <div class="flex items-center gap-4">
                                {{-- Activity Icon with Animation --}}
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-cyan-500/20 to-blue-500/20 border border-cyan-500/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <span class="text-cyan-500 text-lg">👆</span>
                                    </div>
                                </div>
                                
                                {{-- Click Information --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-mono text-cyan-500 text-sm font-medium bg-cyan-500/10 px-2 py-1 rounded border border-cyan-500/20">
                                            {{ $click['link_short_code'] }}
                                        </span>
                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">New Click</span>
                                    </div>
                                    
                                    <h4 class="font-medium text-foreground text-sm mb-1 truncate">
                                        {{ $click['link_title'] ?: 'Untitled Link' }}
                                    </h4>
                                    
                                    <div class="flex items-center gap-3 text-xs text-muted-foreground">
                                        <div class="flex items-center gap-1">
                                            <span class="text-blue-500">🕒</span>
                                            <span>{{ $click['clicked_at_human'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="text-purple-500">📍</span>
                                            <span>{{ Str::limit($click['ip_address'], 12) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Referrer Information --}}
                                <div class="flex-shrink-0 text-right">
                                    @if($click['referrer'])
                                        <div class="flex items-center gap-2 bg-purple-500/10 px-3 py-1 rounded-full border border-purple-500/20">
                                            <span class="text-purple-500 text-xs">🌐</span>
                                            <span class="text-purple-500 text-xs font-medium">
                                                {{ Str::limit(parse_url($click['referrer'], PHP_URL_HOST), 15) }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 bg-gray-500/10 px-3 py-1 rounded-full border border-gray-500/20">
                                            <span class="text-gray-500 text-xs">🎯</span>
                                            <span class="text-gray-500 text-xs font-medium">Direct</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-cyan-500/20 to-blue-500/20 border border-cyan-500/30 flex items-center justify-center">
                            <span class="text-3xl">🎯</span>
                        </div>
                        <h4 class="text-lg font-medium text-foreground mb-2">No activity yet</h4>
                        <p class="text-muted-foreground text-sm">Click activity will appear here as users visit your links!</p>
                    </div>
                @endif
            </div>
        </x-dashboard.section>
    </div>

    {{-- Link Generator Modal --}}
    @if($showLinkGeneratorModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" 
             wire:click.self="closeLinkGenerator">
            <div class="bg-background border border-border/30 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto" 
                 onclick="event.stopPropagation()">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-foreground">Create New Short Link</h2>
                    <button wire:click="closeLinkGenerator" 
                            class="text-muted-foreground hover:text-foreground transition-colors text-2xl">
                        ✕
                    </button>
                </div>
                
                {{-- Include the Link Generator Component --}}
                <livewire:link-generator key="dashboard-modal-generator" />
            </div>
        </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="loadDashboardData,refreshDashboard,updatedDateRange,updatedStartDate,updatedEndDate" 
         class="fixed inset-0 bg-background/80 backdrop-blur-sm items-center justify-center z-40">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-purple-500 font-medium">Loading dashboard data...</span>
        </div>
    </div>
</div>

<script>
    // Listen for link generation events to refresh dashboard
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('linkGenerated', (event) => {
            // Refresh dashboard data
            @this.refreshDashboard();
            // Close the modal
            @this.closeLinkGenerator();
            
            // Show success notification
            if (window.showNotification) {
                window.showNotification('Link created successfully!', 'success');
            }
        });
        
        // Also listen for the original event name
        Livewire.on('link-generated', (event) => {
            @this.refreshDashboard();
            @this.closeLinkGenerator();
        });
    });

    // Copy to clipboard function for the dashboard
    function copyLinkToClipboard(url, button) {
        navigator.clipboard.writeText(url).then(() => {
            const originalText = button.textContent;
            button.textContent = '✓';
            button.classList.add('text-green-500');
            
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('text-green-500');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
            alert('Failed to copy link. Please try again.');
        });
    }
</script>
