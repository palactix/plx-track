<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;
use App\Services\MetadataFetcher;
use Illuminate\Support\Facades\Auth;

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
}
