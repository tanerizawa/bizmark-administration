<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ServiceInquiryRateLimiter
{
    private const EMAIL_LIMIT = 5; // per day
    private const IP_LIMIT = 10; // per day
    private const EMAIL_COOLDOWN = 3600; // 1 hour in seconds
    
    /**
     * Check if request is allowed
     */
    public function check(string $email, string $ip): array
    {
        // Check email limit
        $emailKey = $this->getEmailKey($email);
        $emailCount = Cache::get($emailKey, 0);
        
        if ($emailCount >= self::EMAIL_LIMIT) {
            $retryAfter = Cache::ttl($emailKey);
            return [
                'allowed' => false,
                'reason' => 'email_limit',
                'message' => 'Anda sudah menggunakan ' . self::EMAIL_LIMIT . 'x analisis gratis hari ini. Daftar untuk unlimited analysis.',
                'retry_after' => $retryAfter,
                'retry_after_formatted' => $this->formatRetryAfter($retryAfter)
            ];
        }
        
        // Check IP limit
        $ipKey = $this->getIpKey($ip);
        $ipCount = Cache::get($ipKey, 0);
        
        if ($ipCount >= self::IP_LIMIT) {
            $retryAfter = Cache::ttl($ipKey);
            return [
                'allowed' => false,
                'reason' => 'ip_limit',
                'message' => 'Terlalu banyak permintaan dari jaringan Anda. Silakan coba lagi nanti atau daftar untuk akses unlimited.',
                'retry_after' => $retryAfter,
                'retry_after_formatted' => $this->formatRetryAfter($retryAfter)
            ];
        }
        
        // Check email cooldown (between requests)
        $cooldownKey = $this->getCooldownKey($email);
        if (Cache::has($cooldownKey)) {
            $retryAfter = Cache::ttl($cooldownKey);
            return [
                'allowed' => false,
                'reason' => 'cooldown',
                'message' => 'Mohon tunggu ' . $this->formatRetryAfter($retryAfter) . ' sebelum analisis berikutnya.',
                'retry_after' => $retryAfter,
                'retry_after_formatted' => $this->formatRetryAfter($retryAfter)
            ];
        }
        
        return [
            'allowed' => true,
            'remaining_today' => self::EMAIL_LIMIT - $emailCount - 1
        ];
    }
    
    /**
     * Increment counters after successful request
     */
    public function increment(string $email, string $ip): void
    {
        $now = Carbon::now();
        $endOfDay = $now->copy()->endOfDay();
        
        // Increment email counter (expires at end of day)
        $emailKey = $this->getEmailKey($email);
        $emailCount = Cache::get($emailKey, 0);
        Cache::put($emailKey, $emailCount + 1, $endOfDay);
        
        // Increment IP counter (expires at end of day)
        $ipKey = $this->getIpKey($ip);
        $ipCount = Cache::get($ipKey, 0);
        Cache::put($ipKey, $ipCount + 1, $endOfDay);
        
        // Set cooldown (1 hour)
        $cooldownKey = $this->getCooldownKey($email);
        Cache::put($cooldownKey, true, self::EMAIL_COOLDOWN);
    }
    
    /**
     * Get current usage stats
     */
    public function getStats(string $email, string $ip): array
    {
        $emailKey = $this->getEmailKey($email);
        $ipKey = $this->getIpKey($ip);
        
        return [
            'email_count' => Cache::get($emailKey, 0),
            'email_limit' => self::EMAIL_LIMIT,
            'email_remaining' => self::EMAIL_LIMIT - Cache::get($emailKey, 0),
            'email_resets_at' => Carbon::now()->endOfDay()->toIso8601String(),
            'ip_count' => Cache::get($ipKey, 0),
            'ip_limit' => self::IP_LIMIT,
            'ip_remaining' => self::IP_LIMIT - Cache::get($ipKey, 0),
            'cooldown_active' => Cache::has($this->getCooldownKey($email)),
            'cooldown_expires_in' => Cache::has($this->getCooldownKey($email)) 
                ? Cache::ttl($this->getCooldownKey($email)) 
                : 0
        ];
    }
    
    /**
     * Reset limits for a user (admin function)
     */
    public function reset(string $email, string $ip = null): void
    {
        Cache::forget($this->getEmailKey($email));
        Cache::forget($this->getCooldownKey($email));
        
        if ($ip) {
            Cache::forget($this->getIpKey($ip));
        }
    }
    
    /**
     * Cache key generators
     */
    private function getEmailKey(string $email): string
    {
        return 'inquiry_email_' . md5(strtolower($email));
    }
    
    private function getIpKey(string $ip): string
    {
        return 'inquiry_ip_' . md5($ip);
    }
    
    private function getCooldownKey(string $email): string
    {
        return 'inquiry_cooldown_' . md5(strtolower($email));
    }
    
    /**
     * Format retry after seconds to human readable
     */
    private function formatRetryAfter(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' detik';
        }
        
        $minutes = floor($seconds / 60);
        if ($minutes < 60) {
            return $minutes . ' menit';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes > 0) {
            return $hours . ' jam ' . $remainingMinutes . ' menit';
        }
        
        return $hours . ' jam';
    }
}
