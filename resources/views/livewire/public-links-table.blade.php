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
    
    <div class="overflow-x-auto">
        @if($links && count($links) > 0)
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border/50">
                        <th class="text-left py-3 text-muted-foreground">Short URL</th>
                        <th class="text-left py-3 text-muted-foreground">Original URL</th>
                        <th class="text-center py-3 text-muted-foreground">Clicks (7d)</th>
                        <th class="text-right py-3 text-muted-foreground">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($links as $link)
                        <tr class="border-b border-border/30 hover:bg-purple-500/5 transition-all duration-300 group"
                            wire:key="link-{{ $link['id'] }}">
                            <td class="py-4">
                                <div class="flex flex-col">
                                    <a href="{{ $link['short_url'] }}" 
                                       target="_blank"
                                       class="text-cyan-500 hover:text-cyan-400 transition-all duration-300 hover:scale-105 group-hover:underline font-medium">
                                        {{ str_replace(config('app.url') . '/', '', $link['short_url']) }}
                                    </a>
                                    @if($link['has_custom_title'] && $link['title'] !== parse_url($link['original_url'], PHP_URL_HOST))
                                        <span class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium">
                                            📄 {{ $link['title'] }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 text-muted-foreground group-hover:text-foreground/70 transition-colors duration-300">
                                <div class="space-y-1">
                                    <div class="max-w-xs truncate font-medium" title="{{ $link['original_url'] }}">
                                        {{ $link['original_url'] }}
                                    </div>
                                    <div class="text-xs text-muted-foreground/60">
                                        🌐 {{ parse_url($link['original_url'], PHP_URL_HOST) }}
                                    </div>
                                    @if($link['has_description'])
                                        <div class="text-xs text-muted-foreground/80 mt-1 max-w-xs truncate" title="{{ $link['description'] }}">
                                            💬 {{ $link['description'] }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 text-center text-foreground group-hover:text-foreground/90 transition-colors duration-300">
                                <div class="flex flex-col items-center">
                                    <span class="font-medium">{{ $link['clicks_last_7_days'] }}</span>
                                    @if($link['total_clicks'] != $link['clicks_last_7_days'])
                                        <span class="text-xs text-muted-foreground">({{ $link['total_clicks'] }} total)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 text-right text-muted-foreground group-hover:text-foreground/70 transition-colors duration-300">
                                <div class="flex flex-col items-end">
                                    <span class="text-sm">{{ $link['created_at_human'] }}</span>
                                    <span class="text-xs text-muted-foreground/60">
                                        {{ $link['created_at']->format('M j, Y') }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-purple-500/10 flex items-center justify-center">
                    <span class="text-2xl">🔗</span>
                </div>
                <h3 class="text-lg font-medium text-foreground mb-2">No public links yet</h3>
                <p class="text-muted-foreground">
                    Be the first to create a public short link! Generated links will appear here.
                </p>
            </div>
        @endif
    </div>
    
    {{-- Real-time Update Indicator --}}
    <div class="mt-4 text-center">
        <p class="text-xs text-muted-foreground">
            🔴 Live updates • Shows last 10 public links • Auto-refreshes when new links are created
        </p>
    </div>
    
    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="loadLinks,refreshTable" 
         class="absolute inset-0 bg-background/50 backdrop-blur-sm items-center justify-center rounded-lg">
        <div class="bg-card border border-border/30 rounded-lg p-4 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-5 h-5 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-foreground">Updating links...</span>
            </div>
        </div>
    </div>
</div>
