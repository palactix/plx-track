<?php

namespace App\Services;

use App\Models\Link;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Support\Str;

class LinkAnalyticsService
{
    public function getAnalytics(Link $link): array
    {
        return [
            'total_clicks'   => $this->getTotalClicks($link),
            'clicks_7_days'  => $this->getClicksByDays($link, 7),
            'clicks_30_days' => $this->getClicksByDays($link, 30),
            'chart_data'     => $this->getChartData($link, 15),
            'browsers'       => $this->getBrowserData($link),
            'recent_clicks'  => $this->getRecentClicks($link, 10),
        ];
    }

    private function getTotalClicks(Link $link): int
    {
        return $link->clicks()->count();
    }

    private function getClicksByDays(Link $link, int $days): int
    {
        return $link->clicks()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }

    private function getChartData(Link $link, int $days): array
    {
        $start = now()->subDays($days - 1)->startOfDay();
        $end   = now()->endOfDay();

        $counts = $link->clicks()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as clicks')
            ->groupBy('date')
            ->pluck('clicks', 'date');

         return collect(CarbonPeriod::create($start, $end))
            ->map(fn ($d) => [
                'date'   => $d->format('M j'),
                'iso'    => $d->toDateString(),
                'clicks' => (int)($counts[$d->toDateString()] ?? 0),
            ])
            ->values()
            ->toArray();
    }

    private function getBrowserData(Link $link): array
    {
        $rows = $link->clicks()
            ->selectRaw('LOWER(browser) as browser, COUNT(*) as clicks')
            ->whereNotNull('browser')->where('browser', '!=', '')
            ->groupBy('browser')->orderByDesc('clicks')->get();

        $mapped = $rows->map(fn ($item) => [
            'name'  => Str::title($item->browser),
            'value' => (int) $item->clicks,
        ]);

        $total = $mapped->sum('value');

        return $mapped->map(fn ($b) => [
            'name'       => $b['name'],
            'value'      => $b['value'],
            'percentage' => $total ? round(($b['value'] / $total) * 100, 1) : 0,
            'color'      => $this->browserColors()[$b['name']] ?? '#6B7280',
        ])->toArray();
    }

    private function getRecentClicks(Link $link, int $limit): array
    {
        return $link->clicks()->latest()->take($limit)->get()->map(function ($click) {
            return [
                'id'         => $click->id,
                'ip_address' => $click->ip_address,
                'user_agent' => $click->user_agent,
                'browser'    => $this->detectBrowser($click->user_agent),
                'platform'   => $click->platform ?: 'Unknown',
                'country'    => $click->country ?: 'Unknown',
                'referrer'   => $click->referrer ?: 'Direct',
                'created_at' => $click->created_at->toIso8601String(),
                'date'       => $click->created_at->toDateString(),
                'time'       => $click->created_at->format('H:i:s'),
            ];
        })->toArray();
    }

    private function browserColors(): array
    {
        return [
            'Chrome'           => '#4285F4',
            'Firefox'          => '#FF7139',
            'Safari'           => '#FF9500',
            'Edge'             => '#0078D4',
            'Opera'            => '#FF1B2D',
            'Brave'            => '#FB542B',
            'Vivaldi'          => '#EF3939',
            'Samsung Internet' => '#00C853',
        ];
    }

    private function detectBrowser(?string $ua): string
    {
        $patterns = [
            'Firefox'          => 'firefox',
            'Chrome'           => 'chrome',
            'Safari'           => 'safari',
            'Edge'             => 'edge',
            'Opera'            => 'opera',
            'Brave'            => 'brave',
            'Vivaldi'          => 'vivaldi',
            'Samsung Internet' => 'samsung',
        ];

        foreach ($patterns as $name => $needle) {
            if ($ua && stripos($ua, $needle) !== false) return $name;
        }

        return 'Unknown';
    }
}
