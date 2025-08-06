<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_code',
        'original_url',
        'user_id',
        'session_id',
        'claimed_at',
        'title',
        'description',
        'custom_alias',
        'password',
        'is_active',
        'expires_at',
        'utm_parameters',
        'clicks_count',
        'unique_click_count',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'utm_parameters' => 'array',
    ];

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public function analytics()
    {
        return $this->hasMany(ClickAnalytics::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
