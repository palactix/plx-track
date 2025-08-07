<?php

namespace App\Livewire;

use App\Models\Link;
use Livewire\Component;
use Carbon\Carbon;

class PublicAnalyticChart extends Component
{
    public Link $link;
    public array $dailyClicks = [];
    public int $totalClicks = 0;
    public int $last7DaysClicks = 0;

    public function mount(Link $link)
    {
        $this->link = $link;
        $this->calculateStatistics();
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

    public function getMaxClicksProperty(): int
    {
        return max(array_column($this->dailyClicks, 'count')) ?: 1;
    }

    public function refreshAnalytics()
    {
        $this->calculateStatistics();
        $this->dispatch('analytics-refreshed');
    }

    public function render()
    {
        return view('livewire.public-analytic-chart', [
            'maxClicks' => $this->maxClicks
        ]);
    }
}
