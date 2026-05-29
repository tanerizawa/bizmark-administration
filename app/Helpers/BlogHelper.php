<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class BlogHelper
{
    /**
     * Get the canonical category labels map.
     * Single source of truth — used by both blog/index and blog/show views.
     */
    public static function categoryLabels(bool $isEn = false): array
    {
        return [
            'perizinan-lb3' => $isEn ? 'B3 Waste Permits' : 'Perizinan LB3',
            'amdal' => 'AMDAL',
            'ukl-upl' => 'UKL-UPL',
            'oss-nib' => 'OSS / NIB',
            'izin-operasional' => $isEn ? 'Operational Permits' : 'Izin Operasional',
            'pbg-slf' => 'PBG / SLF',
            'izin-k3' => 'K3',
            'konsultan-lingkungan' => $isEn ? 'Environmental' : 'Konsultan Lingkungan',
            'monitoring-digital' => $isEn ? 'Digital Monitoring' : 'Monitoring Digital',
            'regulation' => $isEn ? 'Regulation' : 'Regulasi',
            'case-study' => $isEn ? 'Case Study' : 'Studi Kasus',
            'news' => $isEn ? 'News' : 'Berita',
            'tips' => 'Tips',
            'general' => $isEn ? 'General' : 'Umum',
        ];
    }

    /**
     * Resolve a single category slug to its display label.
     */
    public static function categoryLabel(string $category, bool $isEn = false): string
    {
        $labels = static::categoryLabels($isEn);

        return $labels[$category] ?? Str::title(str_replace('-', ' ', $category));
    }
}
