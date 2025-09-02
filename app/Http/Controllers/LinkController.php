<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;
use App\Services\MetadataFetcher;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LinkController extends Controller
{
    /**
     * Store a new link.
     */
    public function store(StoreLinkRequest $request, MetadataFetcher $metadataFetcher)
    {
        try {
            $validatedData = $request->validated();
            // Auto-fetch meta data if title or description are empty (or always fetch for meta_* fields)
            $metaData = $metadataFetcher->fetch($validatedData['url']);
            
            // Prepare link data with explicit defaults
            $linkData = [
                'original_url' => $validatedData['url'],
                'title' => $validatedData['title'] ?? ($metaData['title'] ?? null),
                'description' => $validatedData['description'] ?? ($metaData['description'] ?? null),
                'meta_title' => $metaData['title'] ?? null,
                'meta_description' => $metaData['description'] ?? null,
                'og_image_url' => $metaData['image'] ?? null,
                'meta_fetched' => (bool) ($metaData['fetched'] ?? false),
                'expires_at' => $validatedData['expires_at'] ?? null,
                'is_active' => true,
                'clicks_count' => 0,
                'unique_clicks_count' => 0,
            ];
            
            // Handle password if provided
           
            if (!empty($validatedData['password'] ?? null)) {
                $linkData['password'] = $validatedData['password'];
            }
            
            // Handle custom alias
            if (!empty($validatedData['custom_alias'] ?? null)) {
                $linkData['short_code'] = $validatedData['custom_alias'];
                $linkData['custom_alias'] = true;
            } else {
                $linkData['custom_alias'] = false;
            }
            
            // Add user_id if authenticated
            if (Auth::check()) {
                $linkData['user_id'] = Auth::id();
            }
            
            $link = Link::create($linkData);
            
            return response()->json([
                'success' => true,
                'message' => 'Short link created successfully!',
                'data' => [
                    'short_url' => config('app.url') . '/' . $link->short_code,
                    'short_code' => $link->short_code,
                    'original_url' => $link->original_url,
                    'title' => $link->title,
                    'description' => $link->description,
                    'expires_at' => $link->expires_at,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create short link. Please try again.',
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }
    /**
     * List recent public (non-deleted, active) links with pagination.
     */
    public function index()
    {
        $perPage = (int) request()->input('per_page', 10);
        $perPage = $perPage > 0 && $perPage <= 50 ? $perPage : 10;
        $links = Link::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->latest()
            ->select(['id','short_code','original_url','title','description','clicks_count','created_at','og_image_url'])
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $links->items(),
                'meta' => [
                    'current_page' => $links->currentPage(),
                    'last_page' => $links->lastPage(),
                    'per_page' => $links->perPage(),
                    'total' => $links->total(),
                ]
            ]
        ]);
    }


    public function publicAnalytics($code)
    {
        $link = Link::where('short_code', $code)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();

        if (!$link) {
            abort(404, 'Link not found');
        }

        // Get click analytics for the last 7 days
        $clicksData = $link->clicks()
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as clicks')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => \Carbon\Carbon::parse($item->date)->format('M j'),
                    'clicks' => (int) $item->clicks
                ];
            });

        // Get recent clicks (last 7 days)
        $recentClicks = $link->clicks()
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($click) {
                return [
                    'dateTime' => $click->created_at->format('M d, Y g:i A'),
                    'location' => $click->ip_address,
                    'browser' => $click->user_agent ? $this->parseBrowser($click->user_agent) : 'Unknown',
                    'source' => $click->referrer ?: 'Direct',
                    "platform" => $click->platform ?: 'Unknown',
                ];
            });

        return Inertia::render('analytics', [
            'link' => [
                'id' => $link->id,
                'short_code' => $link->short_code,
                'original_url' => $link->original_url,
                'title' => $link->title,
                'description' => $link->description,
                'og_image_url' => $link->og_image_url,
                'clicks_count' => $link->clicks_count,
                'created_at' => $link->created_at->format('M d, Y \a\t g:i A'),
                'short_url' => route("link.redirect", $link->short_code)
            ],
            'analytics' => [
                'total_clicks' => $link->clicks_count,
                'last_7_days_clicks' => $clicksData->sum('clicks'),
                'chart_data' => $clicksData,
                'recent_clicks' => $recentClicks
            ]
        ]);
    }

    private function parseBrowser($userAgent)
    {
        if (stripos($userAgent, needle: 'firefox') !== false) {
            return 'Firefox';
        } elseif (stripos($userAgent, 'chrome') !== false) {
            return 'Chrome';
        } elseif (stripos($userAgent, 'safari') !== false) {
            return 'Safari';
        } elseif (stripos($userAgent, 'edge') !== false) {
            return 'Edge';
        } elseif (stripos($userAgent, 'opera') !== false) {
            return 'Opera';
        } else {
            return 'Unknown';
        }
    }
}
