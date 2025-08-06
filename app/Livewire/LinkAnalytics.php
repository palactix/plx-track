<?php

namespace App\Livewire;

use App\Models\Link;
use App\Models\Click;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LinkAnalytics extends Component
{
    use WithPagination;

    public Link $link;
    public string $shortCode;
    public array $dailyClicks = [];
    public int $totalClicks = 0;
    public int $last7DaysClicks = 0;
    
    protected $paginationTheme = 'tailwind';

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
        
        // Calculate daily clicks for chart
        $this->dailyClicks = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $clickCount = $this->link->clicks()
                ->whereDate('clicked_at', $date)
                ->count();
            
            $this->dailyClicks[] = [
                'date' => $date,
                'count' => $clickCount,
                'day' => $date->format('M j')
            ];
        }
    }

    public function getRecentClicksProperty()
    {
        // For public analytics, limit to essential data and paginate
        return $this->link->clicks()
            ->where('clicked_at', '>=', now()->subDays(7))
            ->select([
                'id',
                'clicked_at',
                'ip_address',
                'user_agent',
                'referrer'
            ])
            ->orderBy('clicked_at', 'desc')
            ->paginate(10); // Limit to 10 per page for performance
    }

    public function getMaxClicksProperty(): int
    {
        return max(array_column($this->dailyClicks, 'count')) ?: 1;
    }

    public function copyShortUrl()
    {
        // This will be handled by JavaScript
        $this->dispatch('url-copied');
    }

    public function refreshAnalytics()
    {
        $this->calculateStatistics();
        $this->resetPage(); // Reset pagination
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
        return view('livewire.link-analytics', [
            'recentClicks' => $this->recentClicks,
            'maxClicks' => $this->maxClicks
        ]);
    }
}
