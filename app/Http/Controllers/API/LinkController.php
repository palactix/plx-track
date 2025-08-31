<?php
namespace App\Http\Controllers\API;

use App\Models\Link;
use Illuminate\Support\Facades\Auth;

class LinkController extends ApiController
{
    public function index()
    {
        $links = Link::where('user_id', $this->userId)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return response()->json($links);
    }
}
?>