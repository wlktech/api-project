<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class RefreshToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'ip_address',
        'user_agent',
        'is_revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_revoked' => 'boolean',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new refresh token
     */
    public static function generate($userId, $request = null)
    {
        return self::create([
            'user_id' => $userId,
            'token' => hash('sha256', Str::random(60)),
            'expires_at' => now()->addDays(30), // 30 days expiry
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    /**
     * Check if token is valid
     */
    public function isValid()
    {
        return !$this->is_revoked && $this->expires_at->isFuture();
    }

    /**
     * Revoke token
     */
    public function revoke()
    {
        $this->update(['is_revoked' => true]);
    }

    /**
     * Clean expired tokens (can be scheduled)
     */
    public static function cleanExpired()
    {
        return self::where('expires_at', '<', now())
            ->orWhere('is_revoked', true)
            ->delete();
    }
}
