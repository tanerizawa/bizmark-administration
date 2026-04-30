<?php

return [
    // WGS84 geographic (EPSG:4326) — coordinates in degrees, as required by OSS Indonesia
    'prj' => 'GEOGCS["GCS_WGS_1984",DATUM["D_WGS_1984",SPHEROID["WGS_1984",6378137.0,298.257223563]],PRIMEM["Greenwich",0.0],UNIT["Degree",0.0174532925199433]]',

    // Storage disk and path
    'disk' => 'local',
    'path' => 'shapefiles',

    // DBF field definitions for the shapefile
    'fields' => [
        'NAMA' => ['type' => 'char', 'size' => 100],
        'LUAS_M2' => ['type' => 'numeric', 'size' => 15, 'decimals' => 2],
        'LUAS_HA' => ['type' => 'numeric', 'size' => 12, 'decimals' => 6],
        'KELURAHAN' => ['type' => 'char', 'size' => 50],
        'KECAMATAN' => ['type' => 'char', 'size' => 50],
        'KABKOTA' => ['type' => 'char', 'size' => 50],
        'PROVINSI' => ['type' => 'char', 'size' => 50],
        'KETERANGAN' => ['type' => 'char', 'size' => 200],
    ],

    // Validation limits
    'max_points' => 500,
    'min_points' => 3,
];
