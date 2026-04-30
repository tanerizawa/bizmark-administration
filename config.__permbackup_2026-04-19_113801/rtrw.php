<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GISTARU RTRW Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for integrating with GISTARU (ATR/BPN) RTR Online
    | ArcGIS MapServer services for spatial zoning (RTRW) data.
    |
    */

    'enabled' => env('RTRW_ENABLED', true),

    // GISTARU ArcGIS base URLs
    'arcgis_base' => 'https://gistaru.atrbpn.go.id/arcgis/rest/services/',
    'proxy_base' => 'https://gistaru.atrbpn.go.id/proxy_rtronline/run.ashx?',

    // Cache settings
    'cache' => [
        'enabled' => env('RTRW_CACHE_ENABLED', true),
        'ttl_zone_query' => env('RTRW_CACHE_TTL_ZONE', 86400),      // 24 hours
        'ttl_layers' => env('RTRW_CACHE_TTL_LAYERS', 604800),       // 7 days
    ],

    // HTTP client settings for GISTARU proxy
    'http' => [
        'timeout' => 15,
        'connect_timeout' => 5,
        'user_agent' => 'BizmarkID-RTRW/1.0',
    ],

    // Spatial reference (WGS84)
    'spatial_reference' => 4326,

    /*
    |--------------------------------------------------------------------------
    | Province → GISTARU Service Path Mapping
    |--------------------------------------------------------------------------
    |
    | Maps BPS province codes to their GISTARU ArcGIS MapServer paths.
    | Pattern: {num}_RTR_KABUPATEN_KOTA_PROVINSI_{NAME}/_{bps}_{NAME}_PR_PERDA
    |
    | Some provinces are unavailable on GISTARU (permission denied or
    | service stopped): 19 (Babel), 31 (DKI), 34 (DIY), 81 (Maluku).
    |
    */

    'provinces' => [
        '11' => [
            'name' => 'Aceh',
            'path' => '010_RTR_KABUPATEN_KOTA_PROVINSI_ACEH/_1100_ACEH_PR_PERDA',
        ],
        '12' => [
            'name' => 'Sumatera Utara',
            'path' => '011_RTR_KABUPATEN_KOTA_PROVINSI_SUMATERA_UTARA/_1200_SUMATERA_UTARA_PR_PERDA',
        ],
        '13' => [
            'name' => 'Sumatera Barat',
            'path' => '012_RTR_KABUPATEN_KOTA_PROVINSI_SUMATERA_BARAT/_1300_SUMATERA_BARAT_PR_PERDA',
        ],
        '14' => [
            'name' => 'Riau',
            'path' => '013_RTR_KABUPATEN_KOTA_PROVINSI_RIAU/_1400_RIAU_PR_PERDA',
        ],
        '15' => [
            'name' => 'Jambi',
            'path' => '014_RTR_KABUPATEN_KOTA_PROVINSI_JAMBI/_1500_JAMBI_PR_PERDA',
        ],
        '16' => [
            'name' => 'Sumatera Selatan',
            'path' => '015_RTR_KABUPATEN_KOTA_PROVINSI_SUMATERA_SELATAN/_1600_SUMATERA_SELATAN_PR_PERDA',
        ],
        '17' => [
            'name' => 'Bengkulu',
            'path' => '016_RTR_KABUPATEN_KOTA_PROVINSI_BENGKULU/_1700_BENGKULU_PR_PERDA',
        ],
        '18' => [
            'name' => 'Lampung',
            'path' => '017_RTR_KABUPATEN_KOTA_PROVINSI_LAMPUNG/_1800_LAMPUNG_PR_PERDA',
        ],
        // '19' => Kep. Bangka Belitung — unavailable on GISTARU
        '21' => [
            'name' => 'Kepulauan Riau',
            'path' => '019_RTR_KABUPATEN_KOTA_PROVINSI_KEPULAUAN_RIAU/_2100_KEPULAUAN_RIAU_PR_PERDA',
        ],
        // '31' => DKI Jakarta — unavailable on GISTARU
        '32' => [
            'name' => 'Jawa Barat',
            'path' => '021_RTR_KABUPATEN_KOTA_PROVINSI_JAWA_BARAT/_3200_JAWA_BARAT_PR_PERDA',
        ],
        '33' => [
            'name' => 'Jawa Tengah',
            'path' => '022_RTR_KABUPATEN_KOTA_PROVINSI_JAWA_TENGAH/_3300_JAWA_TENGAH_PR_PERDA',
        ],
        // '34' => DI Yogyakarta — unavailable on GISTARU
        '35' => [
            'name' => 'Jawa Timur',
            'path' => '024_RTR_KABUPATEN_KOTA_PROVINSI_JAWA_TIMUR/_3500_JAWA_TIMUR_PR_PERDA',
        ],
        '36' => [
            'name' => 'Banten',
            'path' => '025_RTR_KABUPATEN_KOTA_PROVINSI_BANTEN/_3600_BANTEN_PR_PERDA',
        ],
        '51' => [
            'name' => 'Bali',
            'path' => '026_RTR_KABUPATEN_KOTA_PROVINSI_BALI/_5100_BALI_PR_PERDA',
        ],
        '52' => [
            'name' => 'Nusa Tenggara Barat',
            'path' => '027_RTR_KABUPATEN_KOTA_PROVINSI_NUSA_TENGGARA_BARAT/_5200_NUSA_TENGGARA_BARAT_PR_PERDA',
        ],
        '53' => [
            'name' => 'Nusa Tenggara Timur',
            'path' => '028_RTR_KABUPATEN_KOTA_PROVINSI_NUSA_TENGGARA_TIMUR/_5300_NUSA_TENGGARA_TIMUR_PR_PERDA',
        ],
        '61' => [
            'name' => 'Kalimantan Barat',
            'path' => '029_RTR_KABUPATEN_KOTA_PROVINSI_KALIMANTAN_BARAT/_6100_KALIMANTAN_BARAT_PR_PERDA',
        ],
        '62' => [
            'name' => 'Kalimantan Tengah',
            'path' => '030_RTR_KABUPATEN_KOTA_PROVINSI_KALIMANTAN_TENGAH/_6200_KALIMANTAN_TENGAH_PR_PERDA',
        ],
        '63' => [
            'name' => 'Kalimantan Selatan',
            'path' => '031_RTR_KABUPATEN_KOTA_PROVINSI_KALIMANTAN_SELATAN/_6300_KALIMANTAN_SELATAN_PR_PERDA',
        ],
        '64' => [
            'name' => 'Kalimantan Timur',
            'path' => '032_RTR_KABUPATEN_KOTA_PROVINSI_KALIMANTAN_TIMUR/_6400_KALIMANTAN_TIMUR_PR_PERDA',
        ],
        '65' => [
            'name' => 'Kalimantan Utara',
            'path' => '033_RTR_KABUPATEN_KOTA_PROVINSI_KALIMANTAN_UTARA/_6500_KALIMANTAN_UTARA_PR_PERDA',
        ],
        '71' => [
            'name' => 'Sulawesi Utara',
            'path' => '034_RTR_KABUPATEN_KOTA_PROVINSI_SULAWESI_UTARA/_7100_SULAWESI_UTARA_PR_PERDA',
        ],
        '72' => [
            'name' => 'Sulawesi Tengah',
            'path' => '035_RTR_KABUPATEN_KOTA_PROVINSI_SULAWESI_TENGAH/_7200_SULAWESI_TENGAH_PR_PERDA',
        ],
        '73' => [
            'name' => 'Sulawesi Selatan',
            'path' => '036_RTR_KABUPATEN_KOTA_PROVINSI_SULAWESI_SELATAN/_7300_SULAWESI_SELATAN_PR_PERDA',
        ],
        '74' => [
            'name' => 'Sulawesi Tenggara',
            'path' => '037_RTR_KABUPATEN_KOTA_PROVINSI_SULAWESI_TENGGARA/_7400_SULAWESI_TENGGARA_PR_PERDA',
        ],
        '75' => [
            'name' => 'Gorontalo',
            'path' => '038_RTR_KABUPATEN_KOTA_PROVINSI_GORONTALO/_7500_GORONTALO_PR_PERDA',
        ],
        '76' => [
            'name' => 'Sulawesi Barat',
            'path' => '039_RTR_KABUPATEN_KOTA_PROVINSI_SULAWESI_BARAT/_7600_SULAWESI_BARAT_PR_PERDA',
        ],
        // '81' => Maluku — service not started on GISTARU
        '82' => [
            'name' => 'Maluku Utara',
            'path' => '041_RTR_KABUPATEN_KOTA_PROVINSI_MALUKU_UTARA/_8200_MALUKU_UTARA_PR_PERDA',
        ],
        '91' => [
            'name' => 'Papua',
            'path' => '042_RTR_KABUPATEN_KOTA_PROVINSI_PAPUA/_9100_PAPUA_PR_PERDA',
        ],
        '92' => [
            'name' => 'Papua Barat',
            'path' => '043_RTR_KABUPATEN_KOTA_PROVINSI_PAPUA_BARAT/_9200_PAPUA_BARAT_PR_PERDA',
        ],
    ],
];
