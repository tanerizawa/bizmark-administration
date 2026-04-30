<?php

/*
 * Data statis telah dipindahkan ke resources/data/terms.json
 * untuk performa (tidak di-bootstrap setiap request).
 *
 * Akses via:
 *   - App\Services\StaticDataService::get('terms')
 *   - App\Services\StaticDataService::find('terms', 'key.nested')
 *
 * File ini dipertahankan untuk backward-compatibility dengan config('terms.*').
 */
$path = resource_path('data/terms.json');

return file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
