<?php

/*
 * PMA service definitions have been moved to resources/data/services_pma.json
 * for performance (not bootstrapped on every request).
 *
 * Access via:
 *   - App\Services\StaticDataService::servicesPma()
 *   - App\Services\StaticDataService::pmaService('slug')
 *
 * This file is kept for backward-compatibility with config('services_pma.*').
 */

$path = resource_path('data/services_pma.json');

return file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
