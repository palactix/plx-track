<?php

namespace App\Livewire;

use App\Models\Link;
use App\Models\Click;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // Date range properties
    public $dateRange = '7'; // Default to 7 days
    public $startDate;
    public $endDate;
    public $customDateRange = false;
    
    // Modal properties
    public $showLinkGeneratorModal = false;
    
    // Stats properties
    public $totalLinks = 0;
    public $activeLinks = 0;
    public $totalClicks = 0;
    public $todayClicks = 0;
    public $averageClicksPerLink = 0;
    public $mostClickedLink = null;
    
    // Chart data
    public $dailyClicksChart = [];
    public $topLinksChart = [];
    public $clickSourcesChart = [];
    
    // Tables data
    public $recentLinks = [];
    public $recentClicksActivity = [];
    
    protected $listeners = [
        'linkGenerated' => 'refreshDashboard',
        'refreshDashboard' => 'refreshDashboard'
    ];

    public function mount()
    {
        $this->initializeDateRange();
        $this->loadDashboardData();
    }

    public function updatedDateRange()
    {
        if ($this->dateRange === 'custom') {
            $this->customDateRange = true;
            // Set default custom dates if not set
            if (!$this->startDate) {
                $this->startDate = now()->subDays(30)->format('Y-m-d');
            }
            if (!$this->endDate) {
                $this->endDate = now()->format('Y-m-d');
            }
        } else {
            $this->customDateRange = false;
            $this->initializeDateRange();
        }
        $this->loadDashboardData();
    }

    public function updatedStartDate()
    {
        if ($this->customDateRange) {
            $this->loadDashboardData();
        }
    }

    public function updatedEndDate()
    {
        if ($this->customDateRange) {
            $this->loadDashboardData();
        }
    }

    protected function initializeDateRange()
    {
        $days = (int) $this->dateRange;
        $this->startDate = now()->subDays($days)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    protected function getDateRangeForQuery()
    {
        return [
            'start' => Carbon::parse($this->startDate)->startOfDay(),
            'end' => Carbon::parse($this->endDate)->endOfDay()
        ];
    }

    public function loadDashboardData()
    {
        $this->loadBasicStats();
        $this->loadChartData();
        $this->loadTableData();
    }

    protected function loadBasicStats()
    {
        $user = Auth::user();
        $dateRange = $this->getDateRangeForQuery();

        // Basic link stats
        $this->totalLinks = $user->links()->count();
        $this->activeLinks = $user->links()->where('is_active', true)->count();

        // Click stats for date range
        $this->totalClicks = Click::whereHas('link', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereBetween('clicked_at', [$dateRange['start'], $dateRange['end']])->count();

        // Today's clicks
        $this->todayClicks = Click::whereHas('link', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereDate('clicked_at', today())->count();

        // Average clicks per link
        $this->averageClicksPerLink = $this->totalLinks > 0 ? round($this->totalClicks / $this->totalLinks, 1) : 0;

        // Most clicked link in date range
        $this->mostClickedLink = $user->links()
            ->withCount(['clicks' => function($query) use ($dateRange) {
                $query->whereBetween('clicked_at', [$dateRange['start'], $dateRange['end']]);
            }])
            ->orderBy('clicks_count', 'desc')
            ->first();
    }

    protected function loadChartData()
    {
        $user = Auth::user();
        $dateRange = $this->getDateRangeForQuery();

        // Daily clicks chart
        $this->dailyClicksChart = [];
        $days = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1;
        
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::parse($this->startDate)->addDays($i);
            $clickCount = Click::whereHas('link', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->whereDate('clicked_at', $date)->count();

            $this->dailyClicksChart[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('M j'),
                'count' => $clickCount
            ];
        }

        // Top performing links
        $this->topLinksChart = $user->links()
            ->withCount(['clicks' => function($query) use ($dateRange) {
                $query->whereBetween('clicked_at', [$dateRange['start'], $dateRange['end']]);
            }])
            ->orderBy('clicks_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($link) {
                return [
                    'short_code' => $link->short_code,
                    'title' => $link->title ?: parse_url($link->original_url, PHP_URL_HOST),
                    'clicks' => $link->clicks_count
                ];
            })->toArray();

        // Click sources distribution
        $clickSources = Click::whereHas('link', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereBetween('clicked_at', [$dateRange['start'], $dateRange['end']])
        ->selectRaw('
            CASE 
                WHEN referrer IS NULL OR referrer = "" THEN "Direct"
                WHEN referrer LIKE "%google%" THEN "Google"
                WHEN referrer LIKE "%facebook%" THEN "Facebook"
                WHEN referrer LIKE "%twitter%" OR referrer LIKE "%t.co%" THEN "Twitter"
                WHEN referrer LIKE "%linkedin%" THEN "LinkedIn"
                ELSE "Other"
            END as source_type,
            COUNT(*) as count
        ')
        ->groupBy('source_type')
        ->get();

        $this->clickSourcesChart = $clickSources->map(function($source) {
            return [
                'source' => $source->source_type,
                'count' => $source->count
            ];
        })->toArray();
    }

    protected function loadTableData()
    {
        $user = Auth::user();
        $dateRange = $this->getDateRangeForQuery();

        // Recent links (latest 10)
        $this->recentLinks = $user->links()
            ->withCount(['clicks' => function($query) use ($dateRange) {
                $query->whereBetween('clicked_at', [$dateRange['start'], $dateRange['end']]);
            }])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($link) {
                return [
                    'id' => $link->id,
                    'short_code' => $link->short_code,
                    'original_url' => $link->original_url,
                    'title' => $link->title,
                    'image' => $link->image,
                    'is_active' => $link->is_active,
                    'clicks_count' => $link->clicks_count,
                    'created_at' => $link->created_at,
                    'created_at_human' => $link->created_at->diffForHumans()
                ];
            })->toArray();

        // Recent clicks activity (latest 20)
        $this->recentClicksActivity = Click::whereHas('link', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with('link:id,short_code,title,original_url')
        ->whereBetween('clicked_at', [$dateRange['start'], $dateRange['end']])
        ->orderBy('clicked_at', 'desc')
        ->limit(20)
        ->get()
        ->map(function($click) {
            return [
                'id' => $click->id,
                'link_short_code' => $click->link->short_code,
                'link_title' => $click->link->title ?: parse_url($click->link->original_url, PHP_URL_HOST),
                'ip_address' => $click->ip_address,
                'user_agent' => $click->user_agent,
                'referrer' => $click->referrer,
                'clicked_at' => $click->clicked_at,
                'clicked_at_human' => $click->clicked_at->diffForHumans()
            ];
        })->toArray();
    }

    public function openLinkGenerator()
    {
        $this->showLinkGeneratorModal = true;
    }

    public function closeLinkGenerator()
    {
        $this->showLinkGeneratorModal = false;
    }

    public function refreshDashboard()
    {
        $this->loadDashboardData();
        $this->dispatch('dashboard-refreshed');
    }

    public function getMaxDailyClicksProperty()
    {
        return max(array_column($this->dailyClicksChart, 'count')) ?: 1;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'maxDailyClicks' => $this->maxDailyClicks
        ]);
    }
}
