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

            <div class="mt-5 bg-gradient-to-r from-blue-500/15 to-cyan-500/10 rounded-lg p-4 border border-blue-500/30 backdrop-blur-sm">
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

        {{-- Original URL Card --}}
        <div class="backdrop-blur-xl bg-card/60 border-blue-500/20 p-6 transition-all duration-500 hover:bg-card/70 hover:scale-[1.02] hover:border-blue-500/30 shadow-[0_20px_60px_rgba(59,130,246,0.15)] dark:shadow-[0_20px_60px_rgba(59,130,246,0.2)]">
            <h2 class="text-xl font-bold text-foreground mb-4">Original URL:</h2>
            <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-lg p-4 mb-4 border border-blue-500/20 backdrop-blur-sm shadow-[inset_0_4px_8px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_8px_rgba(0,0,0,0.2)]">
                <div class="flex items-start gap-4">
                    @if($link->image)
                        <div class="flex-shrink-0">
                            <img src="{{ $link->image }}" 
                                 alt="{{ $link->title ?: 'Preview' }}"
                                 class="w-16 h-16 object-cover rounded-lg border border-blue-500/30 shadow-lg"
                                 onerror="this.style.display='none';">
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-muted-foreground text-sm break-all">{{ $link->original_url }}</span>
                        </div>
                        @if($link->title)
                            <div class="text-blue-400 font-medium mb-1">{{ $link->title }}</div>
                        @endif
                        @if($link->description)
                            <div class="text-muted-foreground text-sm">{{ $link->description }}</div>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    {{-- Analytics Chart Component --}}
    <livewire:public-analytic-chart :link="$link" />

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
                Showing {{ $recentClicks->count() }} of {{ $totalClicksInPeriod }} clicks (Last 7 Days)
            </div>
        </div>
        
        <div class="relative overflow-x-auto backdrop-blur-sm bg-gradient-to-r from-purple-500/5 to-blue-500/5 rounded-lg border border-purple-500/20 shadow-[inset_0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_12px_rgba(0,0,0,0.2)]">
            {{-- Loading overlay for table during load more --}}
            <div wire:loading.flex wire:target="loadMoreClicks" 
                 class="absolute inset-0 bg-background/80 backdrop-blur-sm items-center justify-center rounded-lg z-10">
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 border-2 border-cyan-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-cyan-500 font-medium">Loading more clicks...</span>
                </div>
            </div>
            
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
                
                {{-- Load More Button --}}
                @if($hasMoreClicks)
                    <div class="mt-6 px-4 py-3 border-t border-border/30 text-center">
                        <button wire:click="loadMoreClicks" 
                                wire:loading.attr="disabled"
                                wire:target="loadMoreClicks"
                                class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-500/20 to-cyan-500/20 hover:from-purple-500/30 hover:to-cyan-500/30 text-purple-500 hover:text-purple-400 transition-all duration-300 hover:scale-105 rounded-xl px-6 py-3 font-medium border border-purple-500/30 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                            <span wire:loading.remove wire:target="loadMoreClicks">📄 Load More Clicks</span>
                            <span wire:loading wire:target="loadMoreClicks" class="flex items-center gap-2">
                                <div class="w-4 h-4 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                                Loading...
                            </span>
                        </button>
                    </div>
                @else
                    <div class="mt-6 px-4 py-3 border-t border-border/30 text-center">
                        <div class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500/10 to-emerald-500/10 rounded-lg px-4 py-2 border border-green-500/30">
                            <span class="text-green-500">✅</span>
                            <span class="text-sm text-muted-foreground">All clicks loaded</span>
                        </div>
                    </div>
                @endif
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
    <div class="backdrop-blur-xl bg-gradient-to-r from-purple-500/20 via-blue-500/10 to-cyan-500/20 border-purple-500/30 p-8 text-center transition-all duration-500 hover:scale-[1.02] shadow-[0_20px_60px_rgba(147,51,234,0.2)] dark:shadow-[0_20px_60px_rgba(147,51,234,0.3)]">
      <div class="flex items-center justify-center gap-3 mb-4">
        <div class="p-2 rounded-full bg-gradient-to-r from-purple-500/20 to-cyan-500/20 shadow-inner border border-purple-500/30">
          🏠
        </div>
        <h3 class="text-2xl font-bold text-foreground">Want to create more short links?</h3>
      </div>
      <p class="text-muted-foreground mb-6 max-w-md mx-auto">
        Go back to the homepage to create more short links or sign up for advanced features.
      </p>
      <div class="flex items-center justify-center gap-4">
        <a href="{{ route('home') }}" 
           class="bg-gradient-to-r from-purple-500 to-cyan-500 hover:from-purple-600 hover:to-cyan-600 text-white px-8 py-3 transition-all duration-300 hover:scale-105 rounded-md shadow-[0_8px_24px_rgba(147,51,234,0.3)] dark:shadow-[0_8px_24px_rgba(147,51,234,0.4)] font-medium">
          ← Back to Home
        </a>
        <button type="button" class="backdrop-blur-sm bg-background/50 border-purple-500/30 hover:bg-purple-500/10 transition-all duration-300 hover:scale-105 shadow-[0_4px_16px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_16px_rgba(0,0,0,0.3)] border outline-none px-6 py-3 rounded-md font-medium">
          Share Analytics
        </button>
      </div>
    </div>
</div>
