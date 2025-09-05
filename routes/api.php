<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LinkController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard/overview-stats', [DashboardController::class, 'getOverviewStats']);
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData']);
    Route::get('/dashboard/recent-links', [DashboardController::class, 'getRecentLinks']);

    Route::get('/links', [LinkController::class, 'index']);
    Route::get('/links/{code}/analytics', [LinkController::class, 'analytics']);
    Route::delete('/links/{link}', [LinkController::class, 'delete']);
});

