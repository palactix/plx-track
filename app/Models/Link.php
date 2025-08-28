<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
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

    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
        $this->update(['last_clicked_at' => now()]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPasswordProtected(): bool
    {
        return !empty($this->password_hash);
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
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}
