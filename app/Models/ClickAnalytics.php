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
}
