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
                    wire:loading.attr="disabled"
                    wire:target="refreshAnalytics"
                    onclick="showChartLoading()">
                <span wire:loading.remove wire:target="refreshAnalytics">🔄 Refresh</span>
                <span wire:loading wire:target="refreshAnalytics" class="flex items-center gap-2">
                    <div class="w-4 h-4 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                    Refreshing...
                </span>
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
                <button @click="chartType = 'line'; window.updateChart('line')" 
                        :class="chartType === 'line' ? 'bg-purple-500 text-white shadow-lg' : 'text-muted-foreground hover:text-foreground'" 
                        class="px-3 py-1 text-xs rounded-md transition-all duration-200">
                    📈 Line
                </button>
                <button @click="chartType = 'bar'; window.updateChart('bar')" 
                        :class="chartType === 'bar' ? 'bg-purple-500 text-white shadow-lg' : 'text-muted-foreground hover:text-foreground'" 
                        class="px-3 py-1 text-xs rounded-md transition-all duration-200">
                    📊 Bar
                </button>
                <button @click="chartType = 'area'; window.updateChart('area')" 
                        :class="chartType === 'area' ? 'bg-purple-500 text-white shadow-lg' : 'text-muted-foreground hover:text-foreground'" 
                        class="px-3 py-1 text-xs rounded-md transition-all duration-200">
                    📊 Area
                </button>
            </div>
        </div>
        
        {{-- Chart Container --}}
        <div class="relative h-64">
            {{-- Chart loading indicator (controlled entirely by JS) --}}
            <div id="chartLoading" 
                 class="absolute inset-0 flex items-center justify-center bg-gradient-to-r from-purple-500/5 to-cyan-500/5 rounded-lg z-10">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-purple-500 font-medium">Loading chart...</span>
                </div>
            </div>
            <canvas id="clicksChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <script>
        // Make functions unique to this component instance
        window.showChartLoading = function() {
            const loadingIndicator = document.getElementById('chartLoading');
            if (loadingIndicator) {
                loadingIndicator.style.display = 'flex';
            }
        };

        window.hideChartLoading = function() {
            const loadingIndicator = document.getElementById('chartLoading');
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
        };

        function initializeChart() {
            // Check if Chart.js is loaded
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js not loaded yet, retrying...');
                setTimeout(initializeChart, 100);
                return;
            }

            // Check if canvas exists
            const canvas = document.getElementById('clicksChart');
            if (!canvas) {
                console.warn('Chart canvas not found, retrying...');
                setTimeout(initializeChart, 100);
                return;
            }

            // Show loading indicator when initializing
            window.showChartLoading();

            // Destroy existing chart if it exists
            if (window.analyticsChart && typeof window.analyticsChart.destroy === 'function') {
                window.analyticsChart.destroy();
            }

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
                                stepSize: Math.max(1, Math.ceil({!! $maxClicks !!} / 5)),
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
            const ctx = canvas.getContext('2d');
            window.analyticsChart = new Chart(ctx, config);

            // Hide loading indicator after chart is created
            setTimeout(() => {
               window.hideChartLoading();
            }, 500);

            // Global function to update chart type
            window.updateChart = function(type) {
                if (!window.analyticsChart) return;
                
                window.analyticsChart.destroy(); // Destroy existing chart
                
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
                window.analyticsChart = new Chart(ctx, config);
            };
        }

        // Update the chart loading function override for the onclick
        document.addEventListener('DOMContentLoaded', function() {
            // Override the showChartLoading function for the onclick
            window.showChartLoading = window.showChartLoading;
        });

        // Initialize chart on different events
        // Handle Livewire events - This is the main initialization
        document.addEventListener('livewire:init', function() {
            // Initialize chart when Livewire is ready
            initializeChart();
            
            // Listen for analytics refresh events
            Livewire.on('analytics-refreshed', function() {
                // Refresh chart data by reinitializing
                setTimeout(initializeChart, 300);
            });
        });

        // Also handle livewire:navigated for wire:navigate
        document.addEventListener('livewire:navigated', function() {
            setTimeout(initializeChart, 100);
        });
        
        // Fallback for direct page loads (when Livewire isn't involved)
        if (document.readyState === 'complete') {
            setTimeout(initializeChart, 100);
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initializeChart, 100);
            });
        }
    </script>
</div>
