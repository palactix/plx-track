<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimableSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'links_count',
        'first_link_created',
        'last_activity',
        'claimed_at',
        'claim_prompted',
        'metadata',
    ];

    protected $casts = [
        'first_link_created' => 'datetime',
        'last_activity' => 'datetime',
        'claimed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
