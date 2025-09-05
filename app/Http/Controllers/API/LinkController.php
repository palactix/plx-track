<?php
namespace App\Http\Controllers\API;

use App\Models\Link;
use App\Services\LinkAnalyticsService;
use App\Http\Resources\LinkAnalyticsResource;
use Illuminate\Http\JsonResponse;

class LinkController extends ApiController
{
    protected LinkAnalyticsService $analyticsService;

    public function __construct(LinkAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
        parent::__construct();
    }

    public function index()
    {
        $query = Link::myLink();

        // Filtering
        if ($search = request('search')) {
            $query->where('title', 'like', "%$search%");
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        if ($from = request('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = request('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Sorting
        $sortBy = request('sortBy', 'created_at');
        $sortOrder = request('sortOrder', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        #$perPage = request('perPage', $this->perPage);
        $links = $query->paginate($this->perPage);

        return response()->json($links);
    }


    /**
     * Return analytics for a link by short code.
     *
     * @param string $code
     * @return JsonResponse
     */
    public function analytics(string $code): JsonResponse
    {
        $link = Link::myLink()
            ->where('short_code', $code)
            ->where('is_active', true)
            ->firstOrFail();
        
        $this->authorize('view', $link);

        $analytics = $this->analyticsService->getAnalytics($link);

        return response()->json(new LinkAnalyticsResource($analytics));
    }


    public function delete(Link $link)
    {
        $this->authorize('delete', $link);

        $link->delete();

        return response()->json(['message' => 'Link deleted successfully.'], 200);

    }
}