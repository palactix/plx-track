<div>
    {{-- Analytics Full Page Popup --}}
    @if($isOpen && $link)
        <div class="fixed inset-0 z-50 flex flex-col overflow-hidden bg-white dark:bg-gray-900"
             x-data="{ 
                 showPopup: @entangle('isOpen'),
                 linkTitle: '{{ $link->title ?: "Untitled Link" }}',
                 linkUrl: '{{ $link->url }}',
                 shortCode: '{{ $link->short_code }}'
             }"
             x-show="showPopup"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-on:transitionend="if (showPopup) { setTimeout(() => { if (window.initializeAnalyticsCharts) window.initializeAnalyticsCharts(); }, 200); }"
             x-init="$watch('showPopup', value => { if (value) { setTimeout(() => { if (window.initializeAnalyticsCharts) window.initializeAnalyticsCharts(); }, 500); } })">

            {{-- Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 sticky top-0 z-10 flex-shrink-0">
                <div class="flex items-center gap-4">
                    {{-- Back Button --}}
                    <button wire:click="closeAnalytics" 
                            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Links
                    </button>
                    
                    {{-- Link Info --}}
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-lg border border-blue-500/30 flex items-center justify-center">
                            <span class="text-blue-500 text-lg">📊</span>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $link->title ?: 'Untitled Link' }}
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ config('app.url') }}/{{ $link->short_code }}
                            </p>
                        </div>
                    </div>
                </div>
                
                {{-- Header Actions --}}
                <div class="flex items-center gap-3">
                    {{-- Date Range Selector --}}
                    <select wire:model.live="dateRange" 
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="custom">Custom range</option>
                    </select>
                    
                    {{-- Copy URL Button --}}
                    <button onclick="copyLinkToClipboard('{{ config('app.url') }}/{{ $link->short_code }}', this)"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy Link
                    </button>
                </div>
            </div>

            {{-- Custom Date Range Inputs --}}
            @if($dateRange === 'custom')
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <div class="flex items-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                            <input type="date" wire:model.blur="startDate" 
                                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                            <input type="date" wire:model.blur="endDate" 
                                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Analytics Content --}}
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50 dark:bg-gray-900">
                {{-- Summary Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                    {{-- Total Clicks --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Clicks</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalClicks) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Unique Clicks --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unique Clicks</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($uniqueClicks) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-500/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Today's Clicks --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Today's Clicks</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($clicksToday) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-500/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Average Clicks Per Day --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg/Day</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $avgClicksPerDay }}</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-500/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Charts Section --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    {{-- Daily Clicks Chart --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Clicks</h3>
                        @if(!empty($chartData) && count($chartData) > 0)
                            <div class="h-64 relative">
                                <div id="dailyChartLoading" 
                                     class="absolute inset-0 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg z-10" 
                                     style="display: none;">
                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                                        <span class="text-blue-500 font-medium">Loading chart...</span>
                                    </div>
                                </div>
                                <canvas id="dailyClicksChart" class="w-full h-full"></canvas>
                            </div>
                        @else
                            <div class="h-64 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <p>No clicks data available</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Hourly Distribution --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Hourly Distribution</h3>
                        @if(!empty($hourlyData) && count($hourlyData) > 0)
                            <div class="h-64 relative">
                                <div id="hourlyChartLoading" 
                                     class="absolute inset-0 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg z-10" 
                                     style="display: none;">
                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 border-2 border-green-500 border-t-transparent rounded-full animate-spin"></div>
                                        <span class="text-green-500 font-medium">Loading chart...</span>
                                    </div>
                                </div>
                                <canvas id="hourlyDistributionChart" class="w-full h-full"></canvas>
                            </div>
                        @else
                            <div class="h-64 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>No hourly data available</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Data Tables Section --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                    {{-- Geographic Data --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Countries</h3>
                        @if(!empty($countryData))
                            <div class="space-y-3">
                                @foreach($countryData as $country)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-blue-500/10 dark:bg-blue-500/20 rounded-lg flex items-center justify-center">
                                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ strtoupper(substr($country['name'], 0, 2)) }}</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $country['name'] }}</span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $country['clicks'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $country['percentage'] }}%</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">No geographic data available</p>
                        @endif
                    </div>

                    {{-- Device Data --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Device Types</h3>
                        @if(!empty($deviceData))
                            <div class="space-y-3">
                                @foreach($deviceData as $device)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-green-500/10 dark:bg-green-500/20 rounded-lg flex items-center justify-center">
                                                @if($device['name'] === 'Desktop')
                                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                @elseif($device['name'] === 'Mobile')
                                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $device['name'] }}</span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $device['clicks'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $device['percentage'] }}%</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">No device data available</p>
                        @endif
                    </div>

                    {{-- Browser Data --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Browsers</h3>
                        @if(!empty($browserData))
                            <div class="space-y-3">
                                @foreach($browserData as $browser)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-purple-500/10 dark:bg-purple-500/20 rounded-lg flex items-center justify-center">
                                                <span class="text-xs font-medium text-purple-600 dark:text-purple-400">{{ strtoupper(substr($browser['name'], 0, 2)) }}</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $browser['name'] }}</span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $browser['clicks'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $browser['percentage'] }}%</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">No browser data available</p>
                        @endif
                    </div>
                </div>

                {{-- Recent Activity --}}
                @if(!empty($recentClicks))
                    <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left py-3 px-4 font-medium text-gray-600 dark:text-gray-300">Time</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-600 dark:text-gray-300">Country</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-600 dark:text-gray-300">Device</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-600 dark:text-gray-300">Browser</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-600 dark:text-gray-300">Referrer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentClicks as $click)
                                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="py-3 px-4 text-gray-900 dark:text-gray-100">{{ $click['clicked_at'] }}</td>
                                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">{{ $click['country'] }}</td>
                                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">{{ $click['device_type'] }}</td>
                                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">{{ $click['browser'] }}</td>
                                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">{{ $click['referrer'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- URL Management and Chart.js Scripts --}}
<script>
    // Chart instances for cleanup
    let dailyClicksChartInstance = null;
    let hourlyDistributionChartInstance = null;

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('update-url', (url) => {
            window.history.pushState({}, '', url);
        });
        
        // Initialize charts when component loads
        initializeAnalyticsCharts();
        
        // Re-initialize charts when data updates
        Livewire.on('analytics-updated', () => {
            setTimeout(() => {
                initializeAnalyticsCharts();
            }, 100);
        });

        // Initialize charts when popup is opened via listing click
        Livewire.on('analytics-opened', () => {
            console.log('Analytics popup opened, initializing charts...');
            setTimeout(() => {
                initializeAnalyticsCharts();
            }, 300); // Give time for DOM elements to render
        });
    });
    
    // Handle browser back button
    window.addEventListener('popstate', function(event) {
        if (window.location.pathname.includes('/analytics/')) {
            // We're on an analytics URL, keep the popup open
        } else {
            // We're not on analytics URL, close the popup
            Livewire.dispatch('closeAnalytics');
        }
    });

    function initializeAnalyticsCharts() {
        console.log('Initializing analytics charts...');
        
        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded yet, retrying...');
            setTimeout(initializeAnalyticsCharts, 100);
            return;
        }

        console.log('Chart.js is loaded, initializing charts...');
        initializeDailyClicksChart();
        initializeHourlyDistributionChart();
    }

    // Make function globally available for Alpine.js
    window.initializeAnalyticsCharts = initializeAnalyticsCharts;

    function initializeDailyClicksChart() {
        console.log('Initializing daily clicks chart...');
        const dailyCanvas = document.getElementById('dailyClicksChart');
        if (!dailyCanvas) {
            console.warn('Daily canvas not found');
            return;
        }

        console.log('Daily canvas found, creating chart...');

        // Show loading indicator
        const loadingElement = document.getElementById('dailyChartLoading');
        if (loadingElement) loadingElement.style.display = 'flex';

        // Destroy existing chart if it exists
        if (dailyClicksChartInstance) {
            dailyClicksChartInstance.destroy();
        }

        // Get theme colors
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? 'rgb(229, 231, 235)' : 'rgb(55, 65, 81)';
        const gridColor = isDarkMode ? 'rgba(55, 65, 81, 0.3)' : 'rgba(229, 231, 235, 0.8)';

        const dailyCtx = dailyCanvas.getContext('2d');
        const chartData = @json($chartData ?? []);
        
        if (chartData && chartData.length > 0) {
            dailyClicksChartInstance = new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: chartData.map(item => item.date),
                    datasets: [{
                        label: 'Daily Clicks',
                        data: chartData.map(item => item.clicks),
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return 'rgba(59, 130, 246, 0.8)';
                            
                            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
                            gradient.addColorStop(0.5, 'rgba(99, 102, 241, 0.8)');
                            gradient.addColorStop(1, 'rgba(147, 51, 234, 0.8)');
                            return gradient;
                        },
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: isDarkMode ? 'rgba(17, 24, 39, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            cornerRadius: 8,
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
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                stepSize: 1,
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                }
                            },
                            grid: {
                                color: gridColor,
                                borderColor: gridColor
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor,
                                maxRotation: 45
                            },
                            grid: {
                                color: gridColor,
                                borderColor: gridColor
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    animation: {
                        duration: 750,
                        easing: 'easeInOutQuart',
                        onComplete: function() {
                            // Hide loading indicator after animation
                            setTimeout(() => {
                                if (loadingElement) loadingElement.style.display = 'none';
                            }, 100);
                        }
                    }
                }
            });
        } else {
            if (loadingElement) loadingElement.style.display = 'none';
        }
    }

    function initializeHourlyDistributionChart() {
        console.log('Initializing hourly distribution chart...');
        const hourlyCanvas = document.getElementById('hourlyDistributionChart');
        if (!hourlyCanvas) {
            console.warn('Hourly canvas not found');
            return;
        }

        console.log('Hourly canvas found, creating chart...');

        // Show loading indicator
        const loadingElement = document.getElementById('hourlyChartLoading');
        if (loadingElement) loadingElement.style.display = 'flex';

        // Destroy existing chart if it exists
        if (hourlyDistributionChartInstance) {
            hourlyDistributionChartInstance.destroy();
        }

        // Get theme colors
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? 'rgb(229, 231, 235)' : 'rgb(55, 65, 81)';
        const gridColor = isDarkMode ? 'rgba(55, 65, 81, 0.3)' : 'rgba(229, 231, 235, 0.8)';

        const hourlyCtx = hourlyCanvas.getContext('2d');
        const hourlyData = @json($hourlyData ?? []);
        
        if (hourlyData && hourlyData.length > 0) {
            hourlyDistributionChartInstance = new Chart(hourlyCtx, {
                type: 'line',
                data: {
                    labels: hourlyData.map(item => item.label),
                    datasets: [{
                        label: 'Hourly Clicks',
                        data: hourlyData.map(item => item.clicks),
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return 'rgba(34, 197, 94, 0.1)';
                            
                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(34, 197, 94, 0.3)');
                            gradient.addColorStop(0.5, 'rgba(16, 185, 129, 0.2)');
                            gradient.addColorStop(1, 'rgba(5, 150, 105, 0.1)');
                            return gradient;
                        },
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(255, 255, 255, 1)',
                        pointBorderColor: 'rgba(34, 197, 94, 1)',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: isDarkMode ? 'rgba(17, 24, 39, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2,
                            cornerRadius: 8,
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
                                    return `🕐 ${context[0].label}:00`;
                                },
                                label: function(context) {
                                    const value = context.parsed.y;
                                    return `👆 ${value} click${value !== 1 ? 's' : ''}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                stepSize: 1,
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                }
                            },
                            grid: {
                                color: gridColor,
                                borderColor: gridColor
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor,
                                callback: function(value, index) {
                                    // Show every 4th hour label to avoid crowding
                                    return index % 4 === 0 ? this.getLabelForValue(value) : '';
                                }
                            },
                            grid: {
                                color: gridColor,
                                borderColor: gridColor
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    animation: {
                        duration: 750,
                        easing: 'easeInOutQuart',
                        onComplete: function() {
                            // Hide loading indicator after animation
                            setTimeout(() => {
                                if (loadingElement) loadingElement.style.display = 'none';
                            }, 100);
                        }
                    }
                }
            });
        } else {
            if (loadingElement) loadingElement.style.display = 'none';
        }
    }

    // Copy link to clipboard function
    function copyLinkToClipboard(url, button) {
        navigator.clipboard.writeText(url).then(() => {
            const originalText = button.innerHTML;
            button.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Copied!
            `;
            button.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            button.classList.add('bg-green-500', 'hover:bg-green-600');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-500', 'hover:bg-green-600');
                button.classList.add('bg-blue-500', 'hover:bg-blue-600');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }

    // Initialize charts on different events for robustness
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initializeAnalyticsCharts, 100);
    });

    // Handle navigation events
    document.addEventListener('livewire:navigated', function() {
        setTimeout(initializeAnalyticsCharts, 100);
    });
</script>
