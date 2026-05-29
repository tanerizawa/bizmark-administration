<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'client_id',
        'key',
        'name',
        'plan',
        'allowed_endpoints',
        'monthly_limit',
        'usage_this_month',
        'usage_reset_at',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'allowed_endpoints' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'usage_reset_at' => 'datetime',
    ];

    protected $hidden = ['key'];

    public static array $planLimits = [
        'free' => 100,
        'starter' => 5000,
        'pro' => 50000,
        'enterprise' => PHP_INT_MAX,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(ApiUsageLog::class);
    }

    public function hasQuotaRemaining(): bool
    {
        return $this->usage_this_month < $this->monthly_limit;
    }

    public function incrementUsage(): void
    {
        // Reset monthly usage if month has passed
        if ($this->usage_reset_at && now()->gt($this->usage_reset_at)) {
            $this->update([
                'usage_this_month' => 0,
                'usage_reset_at' => now()->addMonth(),
            ]);
        }

        $this->increment('usage_this_month');
        $this->update(['last_used_at' => now()]);
    }

    public static function generate(int $clientId, string $name, string $plan = 'free'): self
    {
        return self::create([
            'client_id' => $clientId,
            'key' => hash('sha256', Str::random(40).$clientId.time()),
            'name' => $name,
            'plan' => $plan,
            'monthly_limit' => self::$planLimits[$plan] ?? 100,
            'usage_reset_at' => now()->addMonth(),
        ]);
    }
}
