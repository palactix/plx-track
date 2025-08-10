<?php

namespace App\Livewire;

use App\Models\Link;
use App\Models\Click;
use App\Models\ClickAnalytics;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsPopup extends Component
{
    public $link;
    public $linkId;
    public $isOpen = false;
    public $dateRange = '30'; // Default to last 30 days
    public $startDate;
    public $endDate;
    
    // Analytics data
    public $totalClicks = 0;
    public $uniqueClicks = 0;
    public $clicksToday = 0;
    public $avgClicksPerDay = 0;
    public $peakDay = '';
    public $chartData = [];
    public $countryData = [];
    public $deviceData = [];
    public $browserData = [];
    public $referrerData = [];
    public $hourlyData = [];
    public $recentClicks = [];

    protected $queryString = [
        'linkId' => ['except' => null],
        'isOpen' => ['except' => false]
    ];

    protected $listeners = [
        'openAnalytics' => 'openAnalytics',
        'closeAnalytics' => 'closeAnalytics'
    ];

    public function mount($linkId = null)
    {
       # dd($linkId);
        if ($linkId) {
            $this->linkId = $linkId;
            try {
                $this->link = Link::where('user_id', Auth::id())->findOrFail($linkId);
                $this->isOpen = true;
                $this->initializeDateRange();
                $this->loadAnalyticsData();
            } catch (\Exception $e) {
                // Link not found or access denied
                $this->linkId = null;
                $this->isOpen = false;
            }
        } else {
            $this->initializeDateRange();
        }
    }

    public function openAnalytics($linkId)
    {
        $this->linkId = $linkId;
        $this->link = Link::where('user_id', Auth::id())->findOrFail($linkId);
        $this->isOpen = true;
        $this->loadAnalyticsData();
        
        // Update URL
        $this->dispatch('update-url', url("/links/analytics/{$linkId}"));
        
        // Notify JavaScript to initialize charts
        $this->dispatch('analytics-opened');
    }

    public function closeAnalytics()
    {
        $this->isOpen = false;
        $this->linkId = null;
        $this->link = null;
        
        // Update URL back to links listing
        $this->dispatch('update-url', url('/links'));
    }

    public function updatedDateRange()
    {
        $this->initializeDateRange();
        $this->loadAnalyticsData();
        $this->dispatch('analytics-updated');
    }

    public function updatedStartDate()
    {
        if ($this->startDate && $this->endDate) {
            $this->dateRange = 'custom';
            $this->loadAnalyticsData();
            $this->dispatch('analytics-updated');
        }
    }

    public function updatedEndDate()
    {
        if ($this->startDate && $this->endDate) {
            $this->dateRange = 'custom';
            $this->loadAnalyticsData();
            $this->dispatch('analytics-updated');
        }
    }

    private function initializeDateRange()
    {
        switch ($this->dateRange) {
            case '7':
                $this->startDate = now()->subDays(7)->format('Y-m-d');
                $this->endDate = now()->format('Y-m-d');
                break;
            case '30':
                $this->startDate = now()->subDays(30)->format('Y-m-d');
                $this->endDate = now()->format('Y-m-d');
                break;
            case '90':
                $this->startDate = now()->subDays(90)->format('Y-m-d');
                $this->endDate = now()->format('Y-m-d');
                break;
            case 'custom':
                // Keep existing dates
                break;
            default:
                $this->startDate = now()->subDays(30)->format('Y-m-d');
                $this->endDate = now()->format('Y-m-d');
        }
    }

    private function loadAnalyticsData()
    {
        if (!$this->link) return;

        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // Basic statistics
        $this->totalClicks = Click::where('link_id', $this->link->id)
            ->whereBetween('clicked_at', [$startDate, $endDate->endOfDay()])
            ->count();

        $this->uniqueClicks = Click::where('link_id', $this->link->id)
            ->whereBetween('clicked_at', [$startDate, $endDate->endOfDay()])
            ->distinct('session_id')
            ->count();

        $this->clicksToday = Click::where('link_id', $this->link->id)
            ->whereDate('clicked_at', today())
            ->count();

        $daysDiff = $startDate->diffInDays($endDate) + 1;
        $this->avgClicksPerDay = $daysDiff > 0 ? round($this->totalClicks / $daysDiff, 1) : 0;

        // Chart data for daily clicks
        $this->loadChartData($startDate, $endDate);
        
        // Geographic data
        $this->loadGeographicData($startDate, $endDate);
        
        // Device and browser data
        $this->loadDeviceData($startDate, $endDate);
        
        // Referrer data
        $this->loadReferrerData($startDate, $endDate);
        
        // Hourly distribution
        $this->loadHourlyData($startDate, $endDate);
        
        // Recent clicks
        $this->loadRecentClicks();
    }

    private function loadChartData($startDate, $endDate)
    {
        $clicks = Click::where('link_id', $this->link->id)
            ->whereBetween('clicked_at', [$startDate, $endDate->endOfDay()])
            ->selectRaw('DATE(clicked_at) as date, COUNT(*) as clicks')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->chartData = [];
        $currentDate = $startDate->copy();
        $maxClicks = 0;
        $peakDate = '';

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $clickCount = $clicks->where('date', $dateStr)->first()->clicks ?? 0;
            
            $this->chartData[] = [
                'date' => $currentDate->format('M j'),
                'clicks' => $clickCount
            ];
            
            if ($clickCount > $maxClicks) {
                $maxClicks = $clickCount;
                $peakDate = $currentDate->format('M j, Y');
            }
            
            $currentDate->addDay();
        }

        $this->peakDay = $maxClicks > 0 ? $peakDate : 'No clicks yet';
    }

    private function loadGeographicData($startDate, $endDate)
    {
        $countries = Click::where('link_id', $this->link->id)
            ->whereBetween('clicked_at', [$startDate, $endDate->endOfDay()])
            ->whereNotNull('country')
            ->selectRaw('country, COUNT(*) as clicks')
            ->groupBy('country')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get();

        $this->countryData = $countries->map(function ($item) {
            return [
                'name' => $this->getCountryName($item->country ?: 'Unknown'),
                'clicks' => $item->clicks,
                'percentage' => $this->totalClicks > 0 ? round(($item->clicks / $this->totalClicks) * 100, 1) : 0
            ];
        })->toArray();
    }

    private function loadDeviceData($startDate, $endDate)
    {
        $devices = Click::where('link_id', $this->link->id)
            ->whereBetween('clicked_at', [$startDate, $endDate->endOfDay()])
            ->selectRaw('device_type, COUNT(*) as clicks')
            ->groupBy('device_type')
            ->orderByDesc('clicks')
            ->get();

        $this->deviceData = $devices->map(function ($item) {
            return [
                'name' => ucfirst($item->device_type ?: 'Unknown'),
                'clicks' => $item->clicks,
                'percentage' => $this->totalClicks > 0 ? round(($item->clicks / $this->totalClicks) * 100, 1) : 0
            ];
        })->toArray();

        // Browser data
        $browsers = Click::where('link_id', $this->link->id)
            ->whereBetween('clicked_at', [$startDate, $endDate->endOfDay()])
            ->whereNotNull('browser')
            ->selectRaw('browser, COUNT(*) as clicks')
            ->groupBy('browser')
            ->orderByDesc('clicks')
            ->limit(5)
            ->get();

        $this->browserData = $browsers->map(function ($item) {
            return [
                'name' => $item->browser ?: 'Unknown',
                'clicks' => $item->clicks,
                'percentage' => $this->totalClicks > 0 ? round(($item->clicks / $this->totalClicks) * 100, 1) : 0
            ];
        })->toArray();
    }

    private function loadReferrerData($startDate, $endDate)
    {
        $referrers = Click::where('link_id', $this->link->id)
            ->whereBetween('clicked_at', [$startDate, $endDate->endOfDay()])
            ->selectRaw('CASE 
                WHEN referrer IS NULL OR referrer = "" THEN "Direct"
                WHEN referrer LIKE "%google%" THEN "Google"
                WHEN referrer LIKE "%facebook%" THEN "Facebook"
                WHEN referrer LIKE "%twitter%" THEN "Twitter"
                WHEN referrer LIKE "%linkedin%" THEN "LinkedIn"
                WHEN referrer LIKE "%instagram%" THEN "Instagram"
                ELSE "Other"
            END as referrer_source, COUNT(*) as clicks')
            ->groupBy('referrer_source')
            ->orderByDesc('clicks')
            ->get();

        $this->referrerData = $referrers->map(function ($item) {
            return [
                'name' => $item->referrer_source,
                'clicks' => $item->clicks,
                'percentage' => $this->totalClicks > 0 ? round(($item->clicks / $this->totalClicks) * 100, 1) : 0
            ];
        })->toArray();
    }

    private function loadHourlyData($startDate, $endDate)
    {
        $hourlyClicks = Click::where('link_id', $this->link->id)
            ->whereBetween('clicked_at', [$startDate, $endDate->endOfDay()])
            ->selectRaw('HOUR(clicked_at) as hour, COUNT(*) as clicks')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $this->hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $clicks = $hourlyClicks->where('hour', $i)->first()->clicks ?? 0;
            $this->hourlyData[] = [
                'hour' => $i,
                'label' => sprintf('%02d:00', $i),
                'clicks' => $clicks
            ];
        }
    }

    private function loadRecentClicks()
    {
        $this->recentClicks = Click::where('link_id', $this->link->id)
            ->with(['link:id,title,short_code'])
            ->orderByDesc('clicked_at')
            ->limit(10)
            ->get()
            ->map(function ($click) {
                return [
                    'clicked_at' => $click->clicked_at->diffForHumans(),
                    'country' => $this->getCountryName($click->country ?: 'Unknown'),
                    'device_type' => ucfirst($click->device_type ?: 'Unknown'),
                    'browser' => $click->browser ?: 'Unknown',
                    'referrer' => $this->formatReferrer($click->referrer)
                ];
            })->toArray();
    }

    private function formatReferrer($referrer)
    {
        if (!$referrer) return 'Direct';
        
        $domain = parse_url($referrer, PHP_URL_HOST);
        return $domain ? str_replace('www.', '', $domain) : 'Unknown';
    }

    private function getCountryName($countryCode)
    {
        $countries = [
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
            'DE' => 'Germany',
            'FR' => 'France',
            'JP' => 'Japan',
            'AU' => 'Australia',
            'BR' => 'Brazil',
            'IN' => 'India',
            'CN' => 'China',
            'MX' => 'Mexico',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'NL' => 'Netherlands',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
        ];

        return $countries[$countryCode] ?? $countryCode;
    }

    public function render()
    {
        return view('livewire.analytics-popup');
    }
}
