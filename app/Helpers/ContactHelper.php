<?php

namespace App\Helpers;

/**
 * ContactHelper — Single Source of Truth for Contact Information
 *
 * Centralizes contact data retrieval from config('landing_metrics.contact')
 * so that contact info (phone, WhatsApp, email) is defined in ONE place
 * and reused everywhere, rather than repeated ~50+ times with fallbacks.
 *
 * Usage in Blade:
 *   {{ \App\Helpers\ContactHelper::phone() }}
 *   {{ \App\Helpers\ContactHelper::whatsappLink() }}
 *   {{ \App\Helpers\ContactHelper::email() }}
 *
 * To add a View Composer that injects $contact globally, register in
 * AppServiceProvider::boot():
 *
 *   \Illuminate\Support\Facades\View::composer('*', function ($view) {
 *       $view->with('contactInfo', \App\Helpers\ContactHelper::all());
 *   });
 */
class ContactHelper
{
    /**
     * Default contact values — single source of truth.
     * Override these via config('landing_metrics.contact').
     */
    private static array $defaults = [
        'phone' => '+62 838 7960 2855',
        'whatsapp' => '6283879602855',
        'whatsapp_link' => 'https://wa.me/6283879602855',
        'email' => 'info@bizmark.id',
        'address' => 'Karawang, Jawa Barat, Indonesia',
    ];

    /**
     * Get all contact info as an array.
     */
    public static function all(): array
    {
        return array_merge(
            self::$defaults,
            config('landing_metrics.contact', [])
        );
    }

    /**
     * Get a single contact field by key, with fallback to default.
     *
     * @param  string  $key  e.g. 'phone', 'whatsapp', 'whatsapp_link', 'email'
     */
    public static function get(string $key): string
    {
        return (string) data_get(
            config('landing_metrics'),
            "contact.{$key}",
            self::$defaults[$key] ?? ''
        );
    }

    /**
     * Get phone number (formatted for display).
     */
    public static function phone(): string
    {
        return static::get('phone');
    }

    /**
     * Get phone number stripped of whitespace (for tel: href).
     */
    public static function phoneHref(): string
    {
        return preg_replace('/\s+/', '', static::get('phone'));
    }

    /**
     * Get WhatsApp number (digits only).
     */
    public static function whatsapp(): string
    {
        return static::get('whatsapp');
    }

    /**
     * Get full WhatsApp link (https://wa.me/...).
     */
    public static function whatsappLink(): string
    {
        return static::get('whatsapp_link') ?: 'https://wa.me/'.static::whatsapp();
    }

    /**
     * Get email address.
     */
    public static function email(): string
    {
        return static::get('email');
    }

    /**
     * Get address.
     */
    public static function address(): string
    {
        return static::get('address');
    }
}
