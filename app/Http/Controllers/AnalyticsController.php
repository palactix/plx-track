<?php

namespace App\Http\Controllers;

class AnalyticsController extends Controller
{
    public function show(string $shortCode)
    {
         return view('analytics', compact('shortCode'));
    }
}
