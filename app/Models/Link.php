<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Link extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'uuid', 'short_code', 'original_url', 'user_id', 'title', 'description', 'custom_alias', 'meta_fetched', 'tags', 
        'meta_title', 'meta_description', 'og_image_url', 'password_hash', 
        'ip_whitelist', 'country_whitelist', 'expires_at', 'redirect_type', 
        'is_active', 'clicks_count', 'unique_clicks_count', 'last_clicked_at', 'qr_code_url',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', "password"
    ];

    public $casts = [
        "expires_at" => "datetime"
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($link) {
            
            if (empty($link->short_code)) {
                $link->short_code = $link->generateShortCode();
            }

            if (!empty($link->password)) {
                $link->password_hash = Hash::make($link->password);
                unset($link->password);
            }
        });
    }

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

    // Helper methods
    public function generateShortCode(): string
    {
        do {
            $shortCode = Str::random(6);
        } while (self::where('short_code', $shortCode)->exists());

        return $shortCode;
    }

    public function incrementClicks(bool $isUnique = false): void
    {
        $this->increment('clicks_count');
        if ($isUnique) {
            $this->increment('unique_clicks_count');
        }
        $this->update(['last_clicked_at' => now()]);
    }

    public function incrementUniqueClicks()
    {
        $this->increment('unique_clicks_count');
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPasswordProtected(): bool
    {
        return !empty($this->password_hash);
    }

    public function canBeAccessed(): bool
    {
        return $this->is_active && !$this->getIsExpiredAttribute();
    }
    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password_hash);
    }

    public function getShortUrlAttribute(): string
    {
        return url($this->short_code);
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

    /**
     * Scope a query to links owned by a given user id.
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for the currently authenticated user's links.
     */
    public function scopeMyLink($query)
    {
        return $query->ownedBy(Auth::id());
    }

    public function scopePublic($query)
    {
        return $query->whereNull('user_id');
    }


    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}
