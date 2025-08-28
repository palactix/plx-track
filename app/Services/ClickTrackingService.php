<?php

namespace App\Services;

use App\Models\Link;
use App\Models\Click;
use App\Models\ClickAnalytics;
use Torann\GeoIP\Facades\GeoIP;
use Illuminate\Support\Facades\Queue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Jobs\ProcessClickAnalytics;

class ClickTrackingService
{

    /**
     * Track a click on a link
     */
    public function trackClick(Link $link, Request $request): Click
    {
        // Check if link can be accessed
        if (!$link->canBeAccessed()) {
            throw new \Exception('Link is not accessible');
        }

        // Create minimal click record with basic data
        $click = Click::create([
            'link_id' => $link->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'clicked_at' => now(),
        ]);

        // Queue the job to process all analytics and detailed data gathering
        Queue::push(new ProcessClickAnalytics($click, $request->all()));

        return $click;
    }

    /**
     * Get click analytics for a link
     */
    public function getLinkAnalytics(Link $link, Carbon $start, Carbon $end): array
    {
        $analytics = ClickAnalytics::forLink($link->id)
                                 ->inPeriod($start, $end)
                                 ->get();

        return [
            'total_clicks' => $analytics->sum('total_clicks'),
            'unique_clicks' => $analytics->sum('unique_clicks'),
            'daily_breakdown' => $this->getDailyBreakdown($analytics),
            'countries' => $this->aggregateCountries($analytics),
            'devices' => $this->aggregateDevices($analytics),
            'browsers' => $this->aggregateBrowsers($analytics),
            'referrers' => $this->aggregateReferrers($analytics),
            'hourly_pattern' => $this->getHourlyPattern($analytics),
        ];
    }

    /**
     * Get real-time click statistics
     */
    public function getRealTimeStats(Link $link): array
    {
        $last24Hours = now()->subDay();
        $last1Hour = now()->subHour();

        return [
            'clicks_last_24h' => $link->clicks()->where('clicked_at', '>=', $last24Hours)->count(),
            'clicks_last_1h' => $link->clicks()->where('clicked_at', '>=', $last1Hour)->count(),
            'unique_visitors_24h' => $link->clicks()
                                         ->where('clicked_at', '>=', $last24Hours)
                                         ->distinct('ip_address')
                                         ->count(),
            'latest_clicks' => $link->clicks()
                                   ->with([])
                                   ->latest('clicked_at')
                                   ->limit(5)
                                   ->get()
                                   ->map(function ($click) {
                                       return [
                                           'country' => $click->country,
                                           'device' => $click->device_type,
                                           'browser' => $click->browser,
                                           'time' => $click->clicked_at->diffForHumans(),
                                       ];
                                   }),
        ];
    }

    /**
     * Aggregate analytics data
     */
    private function getDailyBreakdown($analytics): array
    {
        return $analytics->groupBy('date')
                        ->map(function ($dayAnalytics) {
                            return [
                                'total_clicks' => $dayAnalytics->sum('total_clicks'),
                                'unique_clicks' => $dayAnalytics->sum('unique_clicks'),
                            ];
                        })
                        ->toArray();
    }

    private function aggregateCountries($analytics): array
    {
        $countries = [];
        foreach ($analytics as $analytic) {
            if ($analytic->countries) {
                foreach ($analytic->countries as $country => $count) {
                    $countries[$country] = ($countries[$country] ?? 0) + $count;
                }
            }
        }
        arsort($countries);
        return array_slice($countries, 0, 10, true);
    }

    private function aggregateDevices($analytics): array
    {
        $devices = [];
        foreach ($analytics as $analytic) {
            if ($analytic->devices) {
                foreach ($analytic->devices as $device => $count) {
                    $devices[$device] = ($devices[$device] ?? 0) + $count;
                }
            }
        }
        arsort($devices);
        return $devices;
    }

    private function aggregateBrowsers($analytics): array
    {
        $browsers = [];
        foreach ($analytics as $analytic) {
            if ($analytic->browsers) {
                foreach ($analytic->browsers as $browser => $count) {
                    $browsers[$browser] = ($browsers[$browser] ?? 0) + $count;
                }
            }
        }
        arsort($browsers);
        return array_slice($browsers, 0, 10, true);
    }

    private function aggregateReferrers($analytics): array
    {
        $referrers = [];
        foreach ($analytics as $analytic) {
            if ($analytic->referrers) {
                foreach ($analytic->referrers as $referrer => $count) {
                    $referrers[$referrer] = ($referrers[$referrer] ?? 0) + $count;
                }
            }
        }
        arsort($referrers);
        return array_slice($referrers, 0, 10, true);
    }

    private function getHourlyPattern($analytics): array
    {
        $hourlyPattern = array_fill(0, 24, 0);
        
        foreach ($analytics as $analytic) {
            if ($analytic->hourly_distribution) {
                foreach ($analytic->hourly_distribution as $hour => $count) {
                    $hourlyPattern[$hour] += $count;
                }
            }
        }
        
        return $hourlyPattern;
    }
}
