<?php

/*
 * Service definitions have been moved to resources/data/services_data.json
 * for performance (not bootstrapped on every request).
 *
 * Access via:
 *   - App\Services\StaticDataService::servicesData()
 *   - App\Services\StaticDataService::service('slug')
 *
 * This file is kept for backward-compatibility with config('services_data.*').
 */

$path = resource_path('data/services_data.json');

return file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
