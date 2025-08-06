<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Click;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function show(string $shortCode)
    {
         return view('analytics-page', compact('shortCode'));
    }
}
