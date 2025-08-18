<div class="relative">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-foreground">Recent Public Links</h2>
        <button 
            wire:click="refreshTable"
            class="text-purple-500 hover:text-purple-400 text-sm transition-colors flex items-center gap-2"
            wire:loading.attr="disabled"
            wire:target="refreshTable"
        >
            <span wire:loading.remove wire:target="refreshTable">🔄 Refresh</span>
            <span wire:loading wire:target="refreshTable">🔄 Refreshing...</span>
        </button>
    </div>
    
    <div class="space-y-3">
        @if($links && count($links) > 0)
            @foreach ($links as $link)
                <div class="backdrop-blur-xl bg-card/60 border border-border/30 rounded-lg p-4 transition-all duration-500 hover:bg-card/80 hover:scale-[1.01] hover:border-purple-500/30 shadow-[0_4px_16px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_16px_rgba(0,0,0,0.2)] group dark:border-gray-700"
                     wire:key="link-card-{{ $link['id'] }}">
                    
                    {{-- Main Content Row --}}
                    <div class="flex items-center gap-4">
                        {{-- Image Thumbnail --}}
                        @if($link['image'])
                            <div class="flex-shrink-0">
                                <img src="{{ $link['image'] }}" 
                                     alt="{{ $link['title'] ?: 'Preview' }}"
                                     class="w-12 h-12 object-cover rounded-md border border-border/50 shadow-sm group-hover:scale-105 transition-transform duration-300"
                                     onerror="this.style.display='none';">
                            </div>
                        @endif
                        
                        {{-- Content Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                {{-- Left Content --}}
                                <div class="flex-1 min-w-0">
                                    {{-- Short URL and Title --}}
                                    <div class="flex items-center gap-2 mb-1">
                                        <a href="{{ route('link.redirect', ['shortCode' => $link['short_code']]) }}" 
                                           target="_blank"
                                           class="text-cyan-500 hover:text-cyan-400 transition-all duration-300 hover:scale-105 font-bold">
                                            {{ str_replace(config('app.url') . '/', '', $link['short_url']) }}
                                        </a>
                                        
                                        {{-- Copy Button --}}
                                        <button type="button"
                                                x-data="{}"
                                                x-on:click="
                                                    navigator.clipboard.writeText('{{ route('link.redirect', ['shortCode' => $link['short_code']]) }}');
                                                    $el.innerHTML = '✓';
                                                    $el.classList.add('bg-green-500', 'text-white');
                                                    setTimeout(() => {
                                                        $el.innerHTML = '📋';
                                                        $el.classList.remove('bg-green-500', 'text-white');
                                                    }, 2000);
                                                "
                                                class="w-6 h-6 rounded bg-purple-500/10 hover:bg-purple-500/20 text-purple-500 hover:text-purple-400 transition-all duration-300 hover:scale-110 flex items-center justify-center text-xs"
                                                title="Copy short URL">
                                            📋
                                        </button>
                                        
                                        {{-- Popular Indicator --}}
                                        @if($link['total_clicks'] >= 10)
                                            <span class="bg-gradient-to-r from-orange-500/20 to-red-500/20 rounded-full px-2 py-0.5 border border-orange-500/30 text-orange-500 text-xs font-medium">
                                                🔥 Popular
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Title --}}
                                    @if($link['has_custom_title'] && $link['title'] !== parse_url($link['original_url'], PHP_URL_HOST))
                                        <h3 class="text-foreground font-medium mb-1 line-clamp-1 text-sm" title="{{ $link['title'] }}">
                                            {{ $link['title'] }}
                                        </h3>
                                    @endif
                                    
                                    {{-- URL Info --}}
                                    <div class="flex items-center gap-2 text-xs text-muted-foreground/80">
                                        <span>🌐</span>
                                        <span class="text-blue-400 font-medium">{{ parse_url($link['original_url'], PHP_URL_HOST) }}</span>
                                        <span>•</span>
                                        <span>{{ $link['created_at_human'] }}</span>
                                    </div>
                                    
                                    {{-- Description --}}
                                    @if($link['has_description'])
                                        <p class="text-muted-foreground text-xs line-clamp-1 mt-1" title="{{ $link['description'] }}">
                                            {{ $link['description'] }}
                                        </p>
                                    @endif
                                </div>
                                
                                {{-- Right Side: Stats and Actions --}}
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    {{-- Clicks Counter --}}
                                    <div class="text-center bg-gradient-to-r from-cyan-500/10 to-blue-500/10 rounded-lg px-3 py-1 border border-cyan-500/30">
                                        <div class="text-lg font-bold text-cyan-500 leading-tight">{{ $link['clicks_last_7_days'] }}</div>
                                        <div class="text-xs text-muted-foreground leading-tight">clicks</div>
                                    </div>
                                    
                                    {{-- Action Buttons --}}
                                    <div class="flex items-center gap-1">
                                        <a wire:navigate href="{{ route('analytics', $link['short_code']) }}" 
                                           class="inline-flex items-center gap-1 bg-gradient-to-r from-purple-500/20 to-blue-500/20 hover:from-purple-500/30 hover:to-blue-500/30 text-purple-500 hover:text-purple-400 transition-all duration-300 hover:scale-105 rounded px-2 py-1 text-xs font-medium border border-purple-500/30"
                                           title="View Analytics">
                                            📊
                                        </a>
                                        
                                        <a href="{{ route('link.redirect', ['shortCode' => $link['short_code']]) }}" 
                                           target="_blank"
                                           class="inline-flex items-center gap-1 bg-gradient-to-r from-cyan-500/20 to-green-500/20 hover:from-cyan-500/30 hover:to-green-500/30 text-cyan-500 hover:text-cyan-400 transition-all duration-300 hover:scale-105 rounded px-2 py-1 text-xs font-medium border border-cyan-500/30"
                                           title="Visit Link">
                                            🔗
                                        </a>
                                        
                                        {{-- QR Code Button --}}
                                        <div class="relative"
                                             x-data="{ showQR: false }">
                                            <button type="button"
                                                    x-on:click="showQR = !showQR"
                                                    class="inline-flex items-center gap-1 bg-gradient-to-r from-gray-500/20 to-slate-500/20 hover:from-gray-500/30 hover:to-slate-500/30 text-gray-500 hover:text-gray-400 transition-all duration-300 hover:scale-105 rounded px-2 py-1 text-xs font-medium border border-gray-500/30"
                                                    title="Show QR Code">
                                                📱
                                            </button>
                                            
                                            {{-- QR Code Modal --}}
                                            <div x-show="showQR" 
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 scale-90"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-150"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-90"
                                                 x-on:click.away="showQR = false"
                                                 class="absolute bottom-full right-0 mb-2 bg-white border border-border/50 rounded-lg p-3 shadow-2xl z-50 min-w-40">
                                                <div class="text-center">
                                                    <div class="text-xs font-medium text-foreground mb-2">QR Code</div>
                                                    <div class="bg-white p-1 rounded border">
                                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('link.redirect', ['shortCode' => $link['short_code']])) }}" 
                                                             alt="QR Code" 
                                                             class="w-20 h-20 mx-auto">
                                                    </div>
                                                    <div class="text-xs text-muted-foreground mt-1">
                                                        Scan to visit
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            {{-- Load More Button --}}
            @if($hasMoreLinks)
                <div class="text-center mt-8">
                    <button wire:click="loadMore" 
                            wire:loading.attr="disabled"
                            wire:target="loadMore"
                            class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-500/20 to-cyan-500/20 hover:from-purple-500/30 hover:to-cyan-500/30 text-purple-500 hover:text-purple-400 transition-all duration-300 hover:scale-105 rounded-xl px-6 py-3 font-medium border border-purple-500/30 shadow-lg">
                        <span wire:loading.remove wire:target="loadMore">📄 Load More Links</span>
                        <span wire:loading wire:target="loadMore" class="flex items-center gap-2">
                            <div class="w-4 h-4 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                            Loading...
                        </span>
                    </button>
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-r from-purple-500/20 to-cyan-500/20 flex items-center justify-center border border-purple-500/30 shadow-lg">
                    <span class="text-3xl">🔗</span>
                </div>
                <h3 class="text-xl font-bold text-foreground mb-3">No public links yet</h3>
                <p class="text-muted-foreground max-w-md mx-auto">
                    Be the first to create a public short link!
                </p>
                <div class="mt-6">
                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-500/10 to-cyan-500/10 rounded-lg px-4 py-2 border border-purple-500/30">
                        <span class="text-purple-500">✨</span>
                        <span class="text-sm text-muted-foreground">Cards will show images, titles, and click stats</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    {{-- Real-time Update Indicator --}}
    <div class="mt-6 text-center">
        <div class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500/10 to-emerald-500/10 rounded-lg px-4 py-2 border border-green-500/30">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-green-500 text-sm font-medium">Live</span>
            </div>
            <span class="text-xs text-muted-foreground">
                Auto-updates • Card view • {{ count($links) }} links shown @if($hasMoreLinks)• More available @endif
            </span>
        </div>
    </div>
    
    {{-- Loading Overlay with Card Skeletons --}}
    <div wire:loading.flex wire:target="loadLinks,refreshTable,loadMore" 
         class="absolute inset-0 bg-background/80 backdrop-blur-sm items-start justify-center rounded-lg z-40 pt-8">
        <div class="w-full space-y-3 px-4">
            {{-- Skeleton Cards --}}
            @for($i = 0; $i < 5; $i++)
                <div class="animate-pulse backdrop-blur-xl bg-card/40 border border-border/20 rounded-lg p-4">
                    <div class="flex items-center gap-4">
                        {{-- Image placeholder --}}
                        <div class="w-12 h-12 bg-gray-500/20 rounded-md flex-shrink-0"></div>
                        
                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                {{-- Left content --}}
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="h-4 bg-cyan-500/20 rounded w-20"></div>
                                        <div class="h-4 bg-purple-500/20 rounded w-6"></div>
                                    </div>
                                    <div class="h-3 bg-gray-500/20 rounded w-3/4"></div>
                                    <div class="h-3 bg-gray-500/20 rounded w-1/2"></div>
                                </div>
                                
                                {{-- Right content --}}
                                <div class="flex items-center gap-2">
                                    <div class="h-12 w-16 bg-cyan-500/20 rounded-lg"></div>
                                    <div class="flex gap-1">
                                        <div class="h-6 w-6 bg-purple-500/20 rounded"></div>
                                        <div class="h-6 w-6 bg-cyan-500/20 rounded"></div>
                                        <div class="h-6 w-6 bg-gray-500/20 rounded"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
