<?php

/*
 * Data statis telah dipindahkan ke resources/data/neuroscience.json
 * untuk performa (tidak di-bootstrap setiap request).
 *
 * Akses via:
 *   - App\Services\StaticDataService::get('neuroscience')
 *   - App\Services\StaticDataService::find('neuroscience', 'key.nested')
 *
 * File ini dipertahankan untuk backward-compatibility dengan config('neuroscience.*').
 */
$path = resource_path('data/neuroscience.json');

return file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
