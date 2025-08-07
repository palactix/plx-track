<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\RedirectController;
use App\Models\Link;
use App\Models\Click;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/analytics/{shortCode}', [AnalyticsController::class, 'show'])->name('analytics');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Short link routes (must be near the end to catch remaining routes)
Route::get('/{shortCode}/preview', [RedirectController::class, 'preview'])->name('link.preview')->where('shortCode', '[a-zA-Z0-9-_]+');
Route::post('/{shortCode}/verify-password', [RedirectController::class, 'verifyPassword'])->name('link.verify-password')->where('shortCode', '[a-zA-Z0-9-_]+');

// Short link redirection route (must be last to catch all remaining routes)
Route::get('/{shortCode}', [RedirectController::class, 'redirect'])->where('shortCode', '[a-zA-Z0-9-_]+')->name('link.redirect');

require __DIR__.'/auth.php';
