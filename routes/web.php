<?php

use App\Http\Controllers\AnalyticsController;
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

// Short link redirection route (must be last to catch all remaining routes)
Route::get('/{shortCode}', function (string $shortCode) {
    $link = Link::where('short_code', $shortCode)
                ->orWhere('custom_alias', $shortCode)
                ->active()
                ->firstOrFail();
    
    // Check if link is password protected
    if ($link->is_password_protected) {
        // For now, just redirect to home with error - password protection can be implemented later
        return redirect()->route('home')->with('error', 'This link requires a password.');
    }
    
    // Track the click
    $clickData = [
        'link_id' => $link->id,
        'session_id' => session()->getId(),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'referrer' => request()->header('referer'),
        'clicked_at' => now(),
        // Add more tracking data as needed
    ];
    
    Click::create($clickData);
    $link->incrementClicks();
    
    // Redirect to the original URL
    return redirect($link->original_url, 302);
})->where('shortCode', '[a-zA-Z0-9-_]+');

require __DIR__.'/auth.php';
