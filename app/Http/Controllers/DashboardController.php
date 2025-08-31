<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Click;
use App\Models\ClickAnalytics;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get overview stats
     */
    public function getOverviewStats(Request $request): JsonResponse
    {
        $period = $request->get('period', '7days');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Determine date range based on period
        if ($period === 'custom' && $startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } elseif ($period === '7days') {
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(7);
        } elseif ($period === '30days') {
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(30);
        } elseif (!$startDate || !$endDate) {
            // Default to last 30 days if no dates provided
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(30);
        } else {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        }

        // Total Links
        $totalLinks = Link::where('user_id', Auth::id())->count();

        // Total Clicks
        $totalClicks = Click::whereHas('link', function ($query) {
            $query->where('user_id', Auth::id());
        })->whereBetween('created_at', [$startDate, $endDate])->count();

        // Active Links (links with clicks in the period)
        $activeLinks = Link::where('user_id', Auth::id())->whereHas('clicks', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->count();

        // This Month clicks
        $thisMonthClicks = Click::whereHas('link', function ($query) {
            $query->where('user_id', Auth::id());
        })->whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();

        // Calculate changes (simplified - comparing to previous period)
        $previousPeriodStart = $startDate->copy()->subDays($startDate->diffInDays($endDate));
        $previousPeriodEnd = $startDate->copy()->subDay();

        $previousTotalClicks = Click::whereHas('link', function ($query) {
            $query->where('user_id', Auth::id());
        })->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();
        $previousActiveLinks = Link::where('user_id', Auth::id())->whereHas('clicks', function ($query) use ($previousPeriodStart, $previousPeriodEnd) {
            $query->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd]);
        })->count();

        $clicksChange = $previousTotalClicks > 0 ? (($totalClicks - $previousTotalClicks) / $previousTotalClicks) * 100 : 0;
        $activeLinksChange = $previousActiveLinks > 0 ? (($activeLinks - $previousActiveLinks) / $previousActiveLinks) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                [
                    'title' => 'Total Links',
                    'value' => number_format($totalLinks),
                    'change' => '+0%', // Total links don't have change typically
                    'icon' => 'Link2'
                ],
                [
                    'title' => 'Total Clicks',
                    'value' => number_format($totalClicks),
                    'change' => ($clicksChange >= 0 ? '+' : '') . number_format($clicksChange, 1) . '%',
                    'icon' => 'Activity'
                ],
                [
                    'title' => 'Active Links',
                    'value' => number_format($activeLinks),
                    'change' => ($activeLinksChange >= 0 ? '+' : '') . number_format($activeLinksChange, 1) . '%',
                    'icon' => 'TrendingUp'
                ],
                [
                    'title' => 'This Month',
                    'value' => number_format($thisMonthClicks),
                    'change' => '+0%', // Simplified
                    'icon' => 'Calendar'
                ]
            ]
        ]);
    }

    /**
     * Get chart data
     */
    public function getChartData(Request $request): JsonResponse
    {
        $period = $request->get('period', '7days');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $data = [];
        $now = Carbon::now();

        // Determine date ranges based on period
        $dateRanges = $this->getDateRangesForPeriod($period, $startDate, $endDate, $now);

        // Generate chart data for each date range
        foreach ($dateRanges as $dateRange) {
            $clicks = $this->getClicksForDateRange($dateRange['start'], $dateRange['end']);

            $data[] = [
                'date' => $dateRange['format'] === 'month' ? $dateRange['date']->format('M') : $dateRange['date']->format('M d'),
                'clicks' => $clicks
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get date ranges for different periods
     */
    private function getDateRangesForPeriod(string $period, ?string $startDate, ?string $endDate, Carbon $now): array
    {
        switch ($period) {
            case 'custom':
                if ($startDate && $endDate) {
                    $start = Carbon::parse($startDate);
                    $end = Carbon::parse($endDate);
                    $ranges = [];

                    for ($i = 0; $i <= $start->diffInDays($end); $i++) {
                        $date = $start->copy()->addDays($i);
                        $ranges[] = [
                            'date' => $date,
                            'start' => $date->copy()->startOfDay(),
                            'end' => $date->copy()->endOfDay(),
                            'format' => 'day'
                        ];
                    }
                    return $ranges;
                }
                break;

            case '7days':
                $ranges = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $ranges[] = [
                        'date' => $date,
                        'start' => $date->copy()->startOfDay(),
                        'end' => $date->copy()->endOfDay(),
                        'format' => 'day'
                    ];
                }
                return $ranges;

            case '30days':
                $ranges = [];
                for ($i = 29; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $ranges[] = [
                        'date' => $date,
                        'start' => $date->copy()->startOfDay(),
                        'end' => $date->copy()->endOfDay(),
                        'format' => 'day'
                    ];
                }
                return $ranges;
        }

        // Default to 30 days
        $ranges = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $ranges[] = [
                'date' => $date,
                'start' => $date->copy()->startOfDay(),
                'end' => $date->copy()->endOfDay(),
                'format' => 'day'
            ];
        }
        return $ranges;
    }

    /**
     * Get clicks count for a specific date range
     */
    private function getClicksForDateRange(Carbon $start, Carbon $end): int
    {
        return Click::whereHas('link', function ($query) {
            $query->where('user_id', Auth::id());
        })->whereBetween('created_at', [$start, $end])->count();
    }

    /**
     * Get recent links
     */
    public function getRecentLinks(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);

        $links = Link::where('user_id', Auth::id())->withCount('clicks')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'title' => $link->title ?? 'Untitled',
                    'originalUrl' => $link->original_url,
                    'shortUrl' => $link->short_url,
                    'clicks' => $link->clicks_count,
                    'created' => $link->created_at->diffForHumans(),
                    'status' => $link->is_active ? 'active' : 'inactive'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $links
        ]);
    }
}
