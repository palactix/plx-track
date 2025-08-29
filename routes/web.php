<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::get('/links/analytics/{code}', [LinkController::class, 'publicAnalytics'])->name('links.analytics');
// API routes for AJAX requests
Route::middleware('api')->group(function () {
    Route::post('/links', [LinkController::class, 'store'])->name('links.store');
    Route::get('/links', [LinkController::class, 'index'])->name('links.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::get('/{shortCode}', [RedirectController::class, 'redirect'])->where('shortCode', expression: '[a-zA-Z0-9-_]+')->name('link.redirect');

