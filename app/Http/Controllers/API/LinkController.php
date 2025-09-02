<?php
namespace App\Http\Controllers\API;

use App\Models\Link;
use Illuminate\Support\Facades\Auth;

class LinkController extends ApiController
{
    public function index()
    {
        $query = Link::where('user_id', $this->userId);

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
}
?>