<div class="space-y-8">
    {{-- Link Info Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Short Link Card --}}
        <div class="backdrop-blur-xl bg-card/60 border-purple-500/20 p-6 transition-all duration-500 hover:bg-card/70 hover:scale-[1.02] hover:border-purple-500/30 shadow-[0_20px_60px_rgba(147,51,234,0.15)] dark:shadow-[0_20px_60px_rgba(147,51,234,0.2)]">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-foreground">Short Link:</h2>
                <div class="flex items-center gap-2 bg-green-500/10 rounded-full px-3 py-1 backdrop-blur-sm border border-green-500/20">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-green-500 text-sm font-medium">{{ $link->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500/10 to-cyan-500/10 rounded-lg p-4 mb-4 border border-purple-500/20 backdrop-blur-sm shadow-[inset_0_4px_8px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_8px_rgba(0,0,0,0.2)]">
                <code class="text-cyan-500 text-lg font-mono">{{ $link->short_code }}</code>
            </div>
            <div class="flex gap-2">
                <button type="button" 
                        wire:click="copyShortUrl"
                        x-data="{}"
                        x-on:click="
                            navigator.clipboard.writeText('{{ config('app.url') }}/{{ $link->short_code }}');
                            $el.textContent = '✓ Copied!';
                            $el.classList.add('bg-green-500');
                            setTimeout(() => {
                                $el.textContent = 'Copy Short URL';
                                $el.classList.remove('bg-green-500');
                            }, 2000);
                        "
                        class="flex-1 bg-gradient-to-r from-purple-500 to-cyan-500 hover:from-purple-600 hover:to-cyan-600 text-white transition-all duration-300 hover:scale-105 rounded-md shadow-[0_8px_24px_rgba(147,51,234,0.3)] dark:shadow-[0_8px_24px_rgba(147,51,234,0.4)] px-4 py-2 text-sm font-medium">
                    Copy Short URL
                </button>
                <a href="{{ config('app.url') }}/{{ $link->short_code }}" 
                   target="_blank"
                   class="bg-gray-500/20 hover:bg-gray-500/30 text-gray-300 hover:text-white transition-all duration-300 hover:scale-105 rounded-md px-4 py-2 text-sm font-medium">
                    Visit
                </a>
            </div>
        </div>

        {{-- Original URL Card --}}
        <div class="backdrop-blur-xl bg-card/60 border-blue-500/20 p-6 transition-all duration-500 hover:bg-card/70 hover:scale-[1.02] hover:border-blue-500/30 shadow-[0_20px_60px_rgba(59,130,246,0.15)] dark:shadow-[0_20px_60px_rgba(59,130,246,0.2)]">
            <h2 class="text-xl font-bold text-foreground mb-4">Original URL:</h2>
            <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-lg p-4 mb-4 border border-blue-500/20 backdrop-blur-sm shadow-[inset_0_4px_8px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_8px_rgba(0,0,0,0.2)]">
                <div class="flex items-center gap-2">
                    <span class="text-muted-foreground text-sm break-all">{{ $link->original_url }}</span>
                </div>
                @if($link->title)
                    <div class="mt-2 text-blue-400 font-medium">{{ $link->title }}</div>
                @endif
                @if($link->description)
                    <div class="mt-1 text-muted-foreground text-sm">{{ $link->description }}</div>
                @endif
            </div>
            <div class="bg-gradient-to-r from-blue-500/15 to-cyan-500/10 rounded-lg p-4 border border-blue-500/30 backdrop-blur-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-blue-500 font-medium">Analytics Period</span>
                </div>
                <p class="text-muted-foreground text-sm">
                    Analytics available for last 7 days.
                    <br />
                    Created: {{ $link->created_at->format('M j, Y \a\t g:i A') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Analytics Chart --}}
    <div class="backdrop-blur-xl bg-card/60 border-cyan-500/20 p-6 transition-all duration-500 hover:bg-card/70 shadow-[0_20px_60px_rgba(6,182,212,0.15)] dark:shadow-[0_20px_60px_rgba(6,182,212,0.2)]">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-full bg-gradient-to-r from-cyan-500/20 to-blue-500/20 shadow-inner border border-cyan-500/30">
                    📊
                </div>
                <h2 class="text-2xl font-bold text-foreground">Clicks per Day (Last 7 Days)</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right backdrop-blur-sm bg-cyan-500/10 rounded-lg px-4 py-2 border border-cyan-500/30 shadow-[0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_12px_rgba(0,0,0,0.2)]">
                    <div class="text-2xl font-bold text-cyan-500">{{ $totalClicks }}</div>
                    <div class="text-muted-foreground text-sm">Total Clicks</div>
                </div>
                <div class="text-right backdrop-blur-sm bg-gradient-to-r from-green-500/10 to-emerald-500/10 rounded-lg px-4 py-2 border border-green-500/30 shadow-[0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_12px_rgba(0,0,0,0.2)]">
                    <div class="text-2xl font-bold text-green-500">{{ $last7DaysClicks }}</div>
                    <div class="text-muted-foreground text-sm">Last 7 Days</div>
                </div>
                <button wire:click="refreshAnalytics" 
                        class="text-purple-500 hover:text-purple-400 text-sm transition-colors flex items-center gap-2"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>🔄 Refresh</span>
                    <span wire:loading>🔄 Refreshing...</span>
                </button>
            </div>
        </div>
        
        {{-- Simple visualization of daily clicks --}}
        <div class="h-64 w-full backdrop-blur-sm bg-gradient-to-r from-cyan-500/5 to-purple-500/5 rounded-lg border border-cyan-500/20 p-4 shadow-[inset_0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_12px_rgba(0,0,0,0.2)]">
            <div class="flex items-end justify-between h-full gap-2">
                @foreach($dailyClicks as $day)
                    <div class="flex-1 flex flex-col items-center h-full">
                        <div class="flex-1 flex flex-col justify-end">
                            <div class="bg-gradient-to-t from-cyan-500 to-purple-500 rounded-t-lg w-full transition-all duration-500 hover:scale-105"
                                 style="height: {{ $maxClicks > 0 ? ($day['count'] / $maxClicks) * 100 : 0 }}%; min-height: {{ $day['count'] > 0 ? '8px' : '2px' }}">
                            </div>
                        </div>
                        <div class="text-xs text-muted-foreground mt-2 text-center">
                            <div class="font-medium">{{ $day['count'] }}</div>
                            <div>{{ $day['day'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Clicks Table with Pagination --}}
    <div class="backdrop-blur-xl bg-card/60 border-border/30 p-6 transition-all duration-500 hover:bg-card/70 shadow-[0_20px_60px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.3)]">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-full bg-gradient-to-r from-purple-500/20 to-pink-500/20 shadow-inner border border-purple-500/30">
                    🌐
                </div>
                <h2 class="text-2xl font-bold text-foreground">Recent Clicks (Last 7 Days)</h2>
            </div>
            <div class="text-sm text-muted-foreground">
                Showing {{ $recentClicks->firstItem() ?? 0 }}-{{ $recentClicks->lastItem() ?? 0 }} of {{ $recentClicks->total() }} clicks
            </div>
        </div>
        
        <div class="overflow-x-auto backdrop-blur-sm bg-gradient-to-r from-purple-500/5 to-blue-500/5 rounded-lg border border-purple-500/20 shadow-[inset_0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_12px_rgba(0,0,0,0.2)]">
            @if($recentClicks->count() > 0)
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border/50">
                            <th class="text-left py-3 px-4 text-muted-foreground">Date & Time</th>
                            <th class="text-left py-3 px-4 text-muted-foreground">Location</th>
                            <th class="text-left py-3 px-4 text-muted-foreground">Browser</th>
                            <th class="text-left py-3 px-4 text-muted-foreground">Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentClicks as $click)
                            @php
                                $browserInfo = $this->extractBrowserInfo($click->user_agent ?? 'Unknown');
                            @endphp
                            <tr class="border-b border-border/30 hover:bg-purple-500/5 transition-all duration-300 group">
                                <td class="py-4 px-4 text-foreground group-hover:text-foreground/90 transition-colors duration-300">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $click->clicked_at->format('M j, Y') }}</span>
                                        <span class="text-sm text-muted-foreground">{{ $click->clicked_at->format('g:i A') }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-blue-500/20 to-cyan-500/20 flex items-center justify-center border border-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                                            <span class="text-blue-500 font-bold text-xs">🌍</span>
                                        </div>
                                        <span class="text-foreground text-sm">{{ Str::limit($click->ip_address ?? 'Unknown', 15) }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-2 bg-green-500/10 rounded-full px-2 py-1 border border-green-500/20">
                                            <span class="text-green-500 text-xs">{{ $browserInfo['browser'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-blue-500/10 rounded-full px-2 py-1 border border-blue-500/20">
                                            <span class="text-blue-500 text-xs">{{ $browserInfo['platform'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    @if($click->referrer)
                                        <div class="max-w-xs">
                                            <div class="text-sm text-purple-500 font-medium">{{ parse_url($click->referrer, PHP_URL_HOST) }}</div>
                                        </div>
                                    @else
                                        <span class="text-muted-foreground text-sm">Direct</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                {{-- Pagination --}}
                <div class="mt-6 px-4 py-3 border-t border-border/30">
                    {{ $recentClicks->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-purple-500/10 flex items-center justify-center">
                        <span class="text-2xl">📊</span>
                    </div>
                    <h3 class="text-lg font-medium text-foreground mb-2">No clicks yet</h3>
                    <p class="text-muted-foreground">
                        Share your short link to start collecting analytics data.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Upgrade Notice for Public Analytics --}}
    <div class="backdrop-blur-xl bg-gradient-to-r from-amber-500/10 via-orange-500/5 to-red-500/10 border-amber-500/30 p-6 rounded-lg">
        <div class="flex items-start gap-4">
            <div class="p-2 rounded-full bg-amber-500/20 border border-amber-500/30">
                ⚠️
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-foreground mb-2">Limited Public Analytics</h3>
                <p class="text-muted-foreground text-sm mb-3">
                    This public analytics view shows limited data for the last 7 days only. 
                    For comprehensive analytics including geographic data, device details, full referrer information, and unlimited history, please register for a free account.
                </p>
                <div class="flex gap-3">
                    <a href="{{ route('home') }}" 
                       class="text-amber-500 hover:text-amber-400 text-sm font-medium transition-colors">
                        ← Back to Home
                    </a>
                    <span class="text-muted-foreground">•</span>
                    <button class="text-purple-500 hover:text-purple-400 text-sm font-medium transition-colors">
                        Sign Up for Full Analytics
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
