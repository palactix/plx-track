<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use HasFactory, SoftDeletes;

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
        'last_clicked_at', // Added for the new migration
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_clicked_at' => 'datetime',
        'utm_parameters' => 'array',
    ];

    // Relationships
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopePublic($query)
    {
        return $query->whereNull('user_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    // Accessors & Mutators
    public function getShortUrlAttribute()
    {
        return config('app.url') . '/' . $this->short_code;
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsPasswordProtectedAttribute()
    {
        return !empty($this->password);
    }

    // Helper Methods
    public function incrementClicks()
    {
        $this->increment('clicks_count');
        $this->update(['last_clicked_at' => now()]);
    }

    public function incrementUniqueClicks()
    {
        $this->increment('unique_click_count');
    }
}
