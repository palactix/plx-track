<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Services\ClickTrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class RedirectController extends Controller
{
    private ClickTrackingService $clickTrackingService;

    public function __construct(ClickTrackingService $clickTrackingService)
    {
        $this->clickTrackingService = $clickTrackingService;
    }

    /**
     * Handle short link redirects
     */
    public function redirect(Request $request, string $code): RedirectResponse|View
    {
        // Find link by short code
        $link = Link::where('short_code', $code)
                   ->first();
        if (!$link) {
            abort(404, 'Short link not found');
        }

        // Check if link is active and not expired
        if (!$link->canBeAccessed()) {
            return $this->handleInactiveLink($link);
        }

        // Check for password protection
        if ($link->isPasswordProtected() && !$this->isPasswordProvided($request, $link)) {
            return $this->showPasswordForm($link);
        }

        try {
            // Track the click
            $this->clickTrackingService->trackClick($link, $request);
        } catch (\Exception $e) {
            // Log error but don't prevent redirect
            Log::error('Click tracking failed: ' . $e->getMessage());
        }

        // Perform redirect
        return redirect()->to($link->original_url, 302);
    }

    /**
     * Handle password-protected links
     */
    public function verifyPassword(Request $request, string $code): RedirectResponse|View
    {
        $link = Link::where('short_code', $code)
                   ->orWhere('custom_alias', $code)
                   ->first();

        if (!$link || !$link->canBeAccessed()) {
            abort(404);
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        if (!password_verify($request->password, $link->password)) {
            return $this->showPasswordForm($link, 'Invalid password');
        }

        // Store password verification in session
        session(['verified_links.' . $link->id => true]);

        // Track click and redirect
        try {
            $this->clickTrackingService->trackClick($link, $request);
        } catch (\Exception $e) {
            Log::error('Click tracking failed: ' . $e->getMessage());
        }

        return redirect()->to($link->original_url, 302);
    }

    /**
     * Show link preview/info page
     */
    public function preview(string $code): View
    {
        $link = Link::where('short_code', $code)
                   ->orWhere('custom_alias', $code)
                   ->with(['user', 'clicks' => function ($query) {
                       $query->latest()->limit(5);
                   }])
                   ->first();

        if (!$link) {
            abort(404, 'Short link not found');
        }

        // Get basic analytics (last 7 days)
        $analytics = $this->clickTrackingService->getRealTimeStats($link);

        return view('links.preview', compact('link', 'analytics'));
    }

    /**
     * Check if password is provided and valid
     */
    private function isPasswordProvided(Request $request, Link $link): bool
    {
        // Check if password was already verified in this session
        if (session('verified_links.' . $link->id)) {
            return true;
        }

        // Check if password is provided in request
        if ($request->has('password')) {
            return password_verify($request->password, $link->password);
        }

        return false;
    }

    /**
     * Show password form for protected links
     */
    private function showPasswordForm(Link $link, ?string $error = null): View
    {
        return view('links.password', compact('link', 'error'));
    }

    /**
     * Handle inactive/expired links
     */
    private function handleInactiveLink(Link $link): View
    {
        $reason = 'inactive';
        
        if ($link->is_expired) {
            $reason = 'expired';
        } elseif (!$link->is_active) {
            $reason = 'disabled';
        }

        return view('links.inactive', compact('link', 'reason'));
    }
}
