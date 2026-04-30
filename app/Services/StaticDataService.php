<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class StaticDataService
{
    private static array $loaded = [];

    /**
     * Get all data from a static JSON file in resources/data/.
     */
    public static function get(string $file): array
    {
        if (isset(self::$loaded[$file])) {
            return self::$loaded[$file];
        }

        $path = resource_path("data/{$file}.json");

        if (! file_exists($path)) {
            return [];
        }

        $data = json_decode(file_get_contents($path), true) ?? [];
        self::$loaded[$file] = $data;

        return $data;
    }

    /**
     * Get a specific key from a static JSON file (dot-notation).
     */
    public static function find(string $file, string $key): mixed
    {
        $data = self::get($file);

        return data_get($data, $key);
    }

    /**
     * Get all services data.
     */
    public static function servicesData(): array
    {
        return self::get('services_data');
    }

    /**
     * Get all PMA services data.
     */
    public static function servicesPma(): array
    {
        return self::get('services_pma');
    }

    /**
     * Get a single service by slug from services_data.
     */
    public static function service(string $slug): ?array
    {
        return self::get('services_data')[$slug] ?? null;
    }

    /**
     * Get a single PMA service by slug.
     */
    public static function pmaService(string $slug): ?array
    {
        return self::get('services_pma')[$slug] ?? null;
    }

    /**
     * Get programmatic SEO data.
     */
    public static function programmaticSeo(): array
    {
        return self::get('programmatic_seo');
    }

    /**
     * Get terms & conditions data.
     */
    public static function terms(): array
    {
        return self::get('terms');
    }

    /**
     * Get neuroscience UI config data.
     */
    public static function neuroscience(): array
    {
        return self::get('neuroscience');
    }

    /**
     * Get landing page static content data.
     */
    public static function landing(): array
    {
        return self::get('landing');
    }

    /**
     * Clear in-memory cache (useful in tests).
     */
    public static function flush(): void
    {
        self::$loaded = [];
    }
}
