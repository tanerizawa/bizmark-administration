<?php

/*
 * Data statis telah dipindahkan ke resources/data/programmatic_seo.json
 * untuk performa (tidak di-bootstrap setiap request).
 *
 * Akses via:
 *   - App\Services\StaticDataService::get('programmatic_seo')
 *   - App\Services\StaticDataService::find('programmatic_seo', 'key.nested')
 *
 * File ini dipertahankan untuk backward-compatibility dengan config('programmatic_seo.*').
 */
$path = resource_path('data/programmatic_seo.json');

return file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
