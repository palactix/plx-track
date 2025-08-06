<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_id',
        'session_id',
        'ip_address',
        'user_agent',
        'referrer',
        'country',
        'city',
        'region',
        'device_type',
        'device_name',
        'browser',
        'browser_version',
        'platform',
        'platform_version',
        'is_mobile',
        'is_tablet',
        'is_desktop',
        'is_bot',
        'utm_parameters',
        'clicked_at',
    ];

    protected $casts = [
        'utm_parameters' => 'array',
        'clicked_at' => 'datetime',
    ];

    public function link()
    {
        return $this->belongsTo(Link::class);
    }
}
