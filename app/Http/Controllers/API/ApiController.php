<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    protected $perPage = 10;

    protected $maxPerPage = 100;

    protected $userId = null;

    public function __construct()
    {
        $this->userId = Auth::id();
    }
}
?>