<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_id',
        'date',
        'total_clicks',
        'unique_clicks',
        'countries',
        'devices',
        'browsers',
        'platforms',
        'referrers',
        'hourly_distribution',
    ];

    protected $casts = [
        'date' => 'date',
        'countries' => 'array',
        'devices' => 'array',
        'browsers' => 'array',
        'platforms' => 'array',
        'referrers' => 'array',
        'hourly_distribution' => 'array',
    ];

    public function link()
    {
        return $this->belongsTo(Link::class);
    }

    // Scopes
    public function scopeForLink($query, $linkId)
    {
        return $query->where('link_id', $linkId);
    }

    public function scopeInPeriod($query, $start, $end)
    {
        return $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
    }

    // Analytics incrementers
    public function addCountryClick(string $country): void
    {
        $countries = $this->countries ?? [];
        $countries[$country] = ($countries[$country] ?? 0) + 1;
        $this->countries = $countries;
    }

    public function addDeviceClick(string $device): void
    {
        $devices = $this->devices ?? [];
        $devices[$device] = ($devices[$device] ?? 0) + 1;
        $this->devices = $devices;
    }

    public function addBrowserClick(string $browser): void
    {
        $browsers = $this->browsers ?? [];
        $browsers[$browser] = ($browsers[$browser] ?? 0) + 1;
        $this->browsers = $browsers;
    }

    public function addReferrerClick(string $referrer): void
    {
        $referrers = $this->referrers ?? [];
        $referrers[$referrer] = ($referrers[$referrer] ?? 0) + 1;
        $this->referrers = $referrers;
    }

    public function addHourlyClick(int $hour): void
    {
        $hourlyDistribution = $this->hourly_distribution ?? array_fill(0, 24, 0);
        $hourlyDistribution[$hour] = ($hourlyDistribution[$hour] ?? 0) + 1;
        $this->hourly_distribution = $hourlyDistribution;
    }
}
