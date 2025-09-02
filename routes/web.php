<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RenderController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::get('/links/analytics/{code}', [LinkController::class, 'publicAnalytics'])->name('links.analytics');
// API routes for AJAX requests
Route::get("public-links", [LinkController::class, "index"])->name('public-links.index');
Route::middleware('api')->group(function () {
    Route::post('/links', [LinkController::class, 'store'])->name('links.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get("links", RenderController::class)->name('links.index');

});

Route::get('/{shortCode}', [RedirectController::class, 'redirect'])->where('shortCode', expression: '[a-zA-Z0-9-_]+')->name('link.redirect');

