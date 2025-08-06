<?php

namespace App\Livewire;

use App\Models\Link;
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;

class PublicLinksTable extends Component
{
    public $links;
    
    public function mount()
    {
        $this->loadLinks();
    }
    
    #[On('link-generated')]
    public function onLinkGenerated($data)
    {
        // Refresh the links when a new link is generated
        $this->loadLinks();
    }
    
    public function loadLinks()
    {
        $this->links = Link::whereNull('user_id') // Only public links (not owned by users)
            ->where('is_active', true)
            ->whereNull('password') // Only non-password protected links for public display
            ->with('clicks') // Eager load clicks for counting
            ->latest('created_at')
            ->take(10)
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'short_code' => $link->short_code,
                    'short_url' => config('app.url') . '/' . $link->short_code,
                    'original_url' => $link->original_url,
                    'title' => $link->title ?: $this->getDomainFromUrl($link->original_url),
                    'description' => $link->description,
                    'clicks_last_7_days' => $this->getClicksLast7Days($link),
                    'total_clicks' => $link->clicks_count,
                    'created_at' => $link->created_at,
                    'created_at_human' => $link->created_at->diffForHumans(),
                    'has_custom_title' => !empty($link->title),
                    'has_description' => !empty($link->description),
                ];
            });
    }
    
    private function getDomainFromUrl($url)
    {
        $parsed = parse_url($url);
        return isset($parsed['host']) ? $parsed['host'] : 'Unknown';
    }
    
    private function getClicksLast7Days($link)
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        
        return $link->clicks()
            ->where('clicked_at', '>=', $sevenDaysAgo)
            ->count();
    }
    
    public function refreshTable()
    {
        $this->loadLinks();
        $this->dispatch('table-refreshed');
    }
    
    public function render()
    {
        return view('livewire.public-links-table');
    }
}
