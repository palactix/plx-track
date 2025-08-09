<?php

namespace App\Livewire;

use App\Models\Link;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LinksList extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $status = 'all'; // all, active, inactive, expired
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $dateFilter = 'all'; // all, today, week, month, custom
    public $startDate = '';
    public $endDate = '';
    
    // UI State
    public $showFilters = false;
    public $selectedLinks = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $linkToDelete = null;
    public $showBulkActions = false;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedLinks = $this->getLinks()->pluck('id')->toArray();
        } else {
            $this->selectedLinks = [];
        }
        $this->showBulkActions = count($this->selectedLinks) > 0;
    }

    public function updatedSelectedLinks()
    {
        $this->showBulkActions = count($this->selectedLinks) > 0;
        $totalLinks = $this->getLinks()->count();
        $this->selectAll = count($this->selectedLinks) === $totalLinks && $totalLinks > 0;
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->status = 'all';
        $this->dateFilter = 'all';
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function toggleLinkStatus($linkId)
    {
        $link = Link::where('user_id', Auth::id())->findOrFail($linkId);
        $link->update(['is_active' => !$link->is_active]);
        
        $status = $link->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Link {$status} successfully!");
    }

    public function confirmDelete($linkId)
    {
        $this->linkToDelete = $linkId;
        $this->showDeleteModal = true;
    }

    public function deleteLink()
    {
        if ($this->linkToDelete) {
            $link = Link::where('user_id', Auth::id())->findOrFail($this->linkToDelete);
            $link->delete(); // Soft delete
            
            session()->flash('message', 'Link deleted successfully!');
            $this->showDeleteModal = false;
            $this->linkToDelete = null;
            
            // Remove from selected if it was selected
            $this->selectedLinks = array_filter($this->selectedLinks, fn($id) => $id !== $this->linkToDelete);
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->linkToDelete = null;
    }

    public function bulkDelete()
    {
        Link::whereIn('id', $this->selectedLinks)
            ->where('user_id', Auth::id())
            ->delete();
        
        $count = count($this->selectedLinks);
        session()->flash('message', "{$count} links deleted successfully!");
        
        $this->selectedLinks = [];
        $this->selectAll = false;
        $this->showBulkActions = false;
    }

    public function bulkActivate()
    {
        Link::whereIn('id', $this->selectedLinks)
            ->where('user_id', Auth::id())
            ->update(['is_active' => true]);
        
        $count = count($this->selectedLinks);
        session()->flash('message', "{$count} links activated successfully!");
        
        $this->selectedLinks = [];
        $this->selectAll = false;
        $this->showBulkActions = false;
    }

    public function bulkDeactivate()
    {
        Link::whereIn('id', $this->selectedLinks)
            ->where('user_id', Auth::id())
            ->update(['is_active' => false]);
        
        $count = count($this->selectedLinks);
        session()->flash('message', "{$count} links deactivated successfully!");
        
        $this->selectedLinks = [];
        $this->selectAll = false;
        $this->showBulkActions = false;
    }

    public function restoreLink($linkId)
    {
        $link = Link::withTrashed()->where('user_id', Auth::id())->findOrFail($linkId);
        $link->restore();
        
        session()->flash('message', 'Link restored successfully!');
    }

    public function forceDeleteLink($linkId)
    {
        $link = Link::withTrashed()->where('user_id', Auth::id())->findOrFail($linkId);
        $link->forceDelete();
        
        session()->flash('message', 'Link permanently deleted!');
    }

    private function getLinks()
    {
        $query = Link::where('user_id', Auth::id())->withCount('clicks');

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('short_code', 'like', '%' . $this->search . '%')
                  ->orWhere('custom_alias', 'like', '%' . $this->search . '%')
                  ->orWhere('original_url', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        switch ($this->status) {
            case 'active':
                $query->where('is_active', true)
                      ->where(function($q) {
                          $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                      });
                break;
            case 'inactive':
                $query->where('is_active', false);
                break;
            case 'expired':
                $query->where('expires_at', '<=', now());
                break;
            case 'deleted':
                $query->onlyTrashed();
                break;
            case 'all':
            default:
                $query->withTrashed();
                break;
        }

        // Date filter
        if ($this->dateFilter !== 'all') {
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->subWeek(), now()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [now()->subMonth(), now()]);
                    break;
                case 'custom':
                    if ($this->startDate && $this->endDate) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($this->startDate)->startOfDay(),
                            Carbon::parse($this->endDate)->endOfDay()
                        ]);
                    }
                    break;
            }
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query;
    }

    public function render()
    {
        $links = $this->getLinks()->paginate($this->perPage);
        
        return view('livewire.links-list', [
            'links' => $links,
            'totalLinks' => Link::where('user_id', Auth::id())->count(),
            'activeLinks' => Link::where('user_id', Auth::id())->where('is_active', true)->count(),
            'totalClicks' => Link::where('user_id', Auth::id())->withSum('clicks', 'id')->get()->sum('clicks_sum_id') ?? 0,
        ]);
    }
}
        