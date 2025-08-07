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
        
        {{-- Chart.js visualization --}}
        <div class="h-80 w-full backdrop-blur-sm bg-gradient-to-r from-cyan-500/5 to-purple-500/5 rounded-lg border border-cyan-500/20 p-6 shadow-[inset_0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_12px_rgba(0,0,0,0.2)]">
            {{-- Chart Type Toggle --}}
            <div class="flex justify-between items-center mb-4">
                <div class="text-sm text-muted-foreground">
                    Interactive chart showing click trends
                </div>
                <div x-data="{ chartType: 'line' }" class="flex bg-background/50 rounded-lg p-1 border border-border/30">
                    <button @click="chartType = 'line'; updateChart('line')" 
                            :class="chartType === 'line' ? 'bg-purple-500 text-white shadow-lg' : 'text-muted-foreground hover:text-foreground'" 
                            class="px-3 py-1 text-xs rounded-md transition-all duration-200">
                        📈 Line
                    </button>
                    <button @click="chartType = 'bar'; updateChart('bar')" 
                            :class="chartType === 'bar' ? 'bg-purple-500 text-white shadow-lg' : 'text-muted-foreground hover:text-foreground'" 
                            class="px-3 py-1 text-xs rounded-md transition-all duration-200">
                        📊 Bar
                    </button>
                    <button @click="chartType = 'area'; updateChart('area')" 
                            :class="chartType === 'area' ? 'bg-purple-500 text-white shadow-lg' : 'text-muted-foreground hover:text-foreground'" 
                            class="px-3 py-1 text-xs rounded-md transition-all duration-200">
                        📊 Area
                    </button>
                </div>
            </div>
            
            {{-- Chart Container --}}
            <div class="relative h-64">
                <canvas id="clicksChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Detect dark mode
                const isDarkMode = document.documentElement.classList.contains('dark') || 
                                 window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                // Colors based on theme
                const colors = {
                    text: isDarkMode ? 'rgb(156, 163, 175)' : 'rgb(107, 114, 128)',
                    grid: isDarkMode ? 'rgba(147, 51, 234, 0.1)' : 'rgba(147, 51, 234, 0.15)',
                    border: isDarkMode ? 'rgba(147, 51, 234, 0.2)' : 'rgba(147, 51, 234, 0.3)'
                };

                // Chart data from Laravel
                const chartData = {
                    labels: {!! json_encode(array_column($dailyClicks, 'day')) !!},
                    datasets: [{
                        label: 'Daily Clicks',
                        data: {!! json_encode(array_column($dailyClicks, 'count')) !!},
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            
                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
                            gradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.3)');
                            gradient.addColorStop(1, 'rgba(6, 182, 212, 0.1)');
                            return gradient;
                        },
                        borderWidth: 3,
                        pointBackgroundColor: 'rgb(255, 255, 255)',
                        pointBorderColor: 'rgb(99, 102, 241)',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 10,
                        pointHoverBorderWidth: 4,
                        tension: 0.4,
                        fill: true
                    }]
                };

                // Chart configuration
                const config = {
                    type: 'line',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: isDarkMode ? 'rgba(0, 0, 0, 0.9)' : 'rgba(255, 255, 255, 0.95)',
                                titleColor: isDarkMode ? 'rgb(255, 255, 255)' : 'rgb(0, 0, 0)',
                                bodyColor: isDarkMode ? 'rgb(255, 255, 255)' : 'rgb(0, 0, 0)',
                                borderColor: 'rgb(99, 102, 241)',
                                borderWidth: 2,
                                cornerRadius: 12,
                                padding: 12,
                                displayColors: false,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                callbacks: {
                                    title: function(context) {
                                        return `📅 ${context[0].label}`;
                                    },
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        return `👆 ${value} click${value !== 1 ? 's' : ''}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: colors.grid,
                                    borderColor: colors.border,
                                    lineWidth: 1
                                },
                                ticks: {
                                    color: colors.text,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    padding: 8
                                },
                                border: {
                                    color: colors.border
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: colors.grid,
                                    borderColor: colors.border,
                                    lineWidth: 1
                                },
                                ticks: {
                                    color: colors.text,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    stepSize: Math.max(1, Math.ceil({!! max(array_column($dailyClicks, 'count')) ?: 1 !!} / 5)),
                                    padding: 8,
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : '';
                                    }
                                },
                                border: {
                                    color: colors.border
                                }
                            }
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeInOutQuart'
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        elements: {
                            point: {
                                hoverBackgroundColor: 'rgb(255, 255, 255)',
                                hoverBorderColor: 'rgb(99, 102, 241)'
                            }
                        }
                    }
                };

                // Create chart
                const ctx = document.getElementById('clicksChart').getContext('2d');
                let chart = new Chart(ctx, config);

                // Global function to update chart type
                window.updateChart = function(type) {
                    chart.destroy(); // Destroy existing chart
                    
                    // Update dataset based on type
                    switch(type) {
                        case 'line':
                            config.type = 'line';
                            config.data.datasets[0].fill = true;
                            config.data.datasets[0].tension = 0.4;
                            config.data.datasets[0].borderWidth = 3;
                            config.data.datasets[0].pointRadius = 6;
                            break;
                        case 'bar':
                            config.type = 'bar';
                            config.data.datasets[0].fill = false;
                            config.data.datasets[0].borderWidth = 0;
                            config.data.datasets[0].pointRadius = 0;
                            config.data.datasets[0].backgroundColor = function(context) {
                                const chart = context.chart;
                                const {ctx, chartArea} = chart;
                                if (!chartArea) return null;
                                
                                const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                gradient.addColorStop(0, 'rgba(6, 182, 212, 0.8)');
                                gradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.8)');
                                gradient.addColorStop(1, 'rgba(99, 102, 241, 0.8)');
                                return gradient;
                            };
                            break;
                        case 'area':
                            config.type = 'line';
                            config.data.datasets[0].fill = 'origin';
                            config.data.datasets[0].tension = 0.4;
                            config.data.datasets[0].borderWidth = 0;
                            config.data.datasets[0].pointRadius = 0;
                            config.data.datasets[0].backgroundColor = function(context) {
                                const chart = context.chart;
                                const {ctx, chartArea} = chart;
                                if (!chartArea) return null;
                                
                                const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                gradient.addColorStop(0, 'rgba(99, 102, 241, 0.6)');
                                gradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.4)');
                                gradient.addColorStop(1, 'rgba(6, 182, 212, 0.2)');
                                return gradient;
                            };
                            break;
                    }
                    
                    // Recreate chart with new config
                    chart = new Chart(ctx, config);
                };

                // Handle Livewire events
                document.addEventListener('livewire:init', function() {
                    Livewire.on('analytics-refreshed', function() {
                        // Refresh chart data
                        setTimeout(() => {
                            chart.destroy();
                            location.reload();
                        }, 300);
                    });
                });
            });
        </script>
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
