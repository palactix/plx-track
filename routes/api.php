<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/overview-stats', [DashboardController::class, 'getOverviewStats']);
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData']);
    Route::get('/dashboard/recent-links', [DashboardController::class, 'getRecentLinks']);
});
