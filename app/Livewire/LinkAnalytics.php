<?php

namespace App\Livewire;

use App\Models\Link;
use App\Models\Click;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LinkAnalytics extends Component
{
    public Link $link;
    public string $shortCode;
    public int $totalClicks = 0;
    public int $last7DaysClicks = 0;
    public int $clicksPerLoad = 10;
    public int $currentPage = 1;
    public bool $hasMoreClicks = true;

    public function mount(string $shortCode)
    {
        $this->shortCode = $shortCode;
        $this->loadLink();
        $this->calculateStatistics();
    }

    protected function loadLink()
    {
        $this->link = Link::where('short_code', $this->shortCode)
                         ->orWhere('custom_alias', $this->shortCode)
                         ->firstOrFail();
    }

    protected function calculateStatistics()
    {
        // Get total clicks
        $this->totalClicks = $this->link->clicks()->count();
        
        // Get last 7 days clicks
        $this->last7DaysClicks = $this->link->clicks()
            ->where('clicked_at', '>=', now()->subDays(7))
            ->count();
    }

    public function getRecentClicksProperty()
    {
        // Get clicks with infinite loading approach
        $totalItems = $this->clicksPerLoad * $this->currentPage;
        
        $clicks = $this->link->clicks()
            ->where('clicked_at', '>=', now()->subDays(7))
            ->select([
                'id',
                'clicked_at',
                'ip_address',
                'user_agent',
                'referrer'
            ])
            ->orderBy('clicked_at', 'desc')
            ->take($totalItems)
            ->get();

        // Check if there are more clicks to load
        $totalClicks = $this->link->clicks()
            ->where('clicked_at', '>=', now()->subDays(7))
            ->count();
            
        $this->hasMoreClicks = $clicks->count() < $totalClicks;

        return $clicks;
    }

    public function loadMoreClicks()
    {
        $this->currentPage++;
        // The property will automatically reload with more data
    }


    public function copyShortUrl()
    {
        // This will be handled by JavaScript
        $this->dispatch('url-copied');
    }

    public function refreshAnalytics()
    {
        $this->calculateStatistics();
        $this->currentPage = 1; // Reset to first page
        $this->hasMoreClicks = true; // Reset load more state
        $this->dispatch('analytics-refreshed');
    }

    protected function extractBrowserInfo(string $userAgent): array
    {
        $browser = 'Unknown';
        $platform = 'Unknown';
        
        // Simple browser detection
        if (str_contains($userAgent, 'Chrome')) $browser = 'Chrome';
        elseif (str_contains($userAgent, 'Firefox')) $browser = 'Firefox';
        elseif (str_contains($userAgent, 'Safari')) $browser = 'Safari';
        elseif (str_contains($userAgent, 'Edge')) $browser = 'Edge';
        elseif (str_contains($userAgent, 'Opera')) $browser = 'Opera';
        
        // Simple platform detection
        if (str_contains($userAgent, 'Windows')) $platform = 'Windows';
        elseif (str_contains($userAgent, 'Macintosh')) $platform = 'macOS';
        elseif (str_contains($userAgent, 'Linux')) $platform = 'Linux';
        elseif (str_contains($userAgent, 'iPhone')) $platform = 'iOS';
        elseif (str_contains($userAgent, 'Android')) $platform = 'Android';
        
        return ['browser' => $browser, 'platform' => $platform];
    }

    public function render()
    {
        $recentClicks = $this->recentClicks;
        $totalClicksInPeriod = $this->link->clicks()
            ->where('clicked_at', '>=', now()->subDays(7))
            ->count();
            
        return view('livewire.link-analytics', [
            'recentClicks' => $recentClicks,
            'totalClicksInPeriod' => $totalClicksInPeriod,
            'hasMoreClicks' => $this->hasMoreClicks
        ]);
    }
}
