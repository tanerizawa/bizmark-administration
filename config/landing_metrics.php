<?php

/**
 * Landing Page Metrics - Single Source of Truth
 * 
 * Semua angka statistik untuk landing page (desktop & mobile) harus menggunakan
 * data dari file ini untuk memastikan konsistensi.
 * 
 * Last Updated: November 20, 2025
 */

return [
    
    /**
     * Client Statistics
     */
    'clients' => [
        'total' => 500,                    // Total klien yang pernah dilayani
        'active_this_month' => 23,         // Klien aktif bulan ini
        'satisfaction_rate' => 98,         // Tingkat kepuasan (%)
        'industries' => 6,                 // Jumlah sektor industri
    ],
    
    /**
     * Project Statistics
     */
    'projects' => [
        'completed' => 138,                // Project yang sudah selesai
        'sla_success_rate' => 96,          // Persentase tepat waktu (%)
        'active_this_month' => 23,         // Project aktif bulan ini
    ],
    
    /**
     * Coverage Area
     */
    'coverage' => [
        'provinces' => 18,                 // Jumlah provinsi terlayani
        'cities' => 50,                    // Jumlah kota terlayani
    ],
    
    /**
     * Permits & Licenses
     */
    'permits' => [
        'total_processed' => 1000,         // Total izin yang diproses (1.000+)
        'types_available' => 15,           // Jenis perizinan yang ditangani
    ],
    
    /**
     * Company Experience
     */
    'experience' => [
        'years' => 10,                     // Tahun pengalaman
        'since_year' => 2015,              // Berdiri sejak tahun
    ],
    
    /**
     * Performance Metrics
     */
    'performance' => [
        'average_days_min' => 1,           // Waktu proses minimum (hari)
        'average_days_max' => 3,           // Waktu proses maksimum (hari)
        'sla_percentage' => 96,            // SLA on-time delivery (%)
        'success_rate' => 98,              // Success rate overall (%)
    ],
    
    /**
     * Certifications & Awards
     */
    'certifications' => [
        'iso' => 'ISO 9001:2015',          // ISO Certification
        'award' => 'Top Rated 2024',       // Latest award
        'government_partner' => true,      // Government partnership status
    ],
    
    /**
     * Contact Information
     */
    'contact' => [
        'phone' => '+62 838 7960 2855',
        'phone_display' => '+62 838 7960 2855',
        'whatsapp' => '6283879602855',
        'email' => 'info@bizmark.id',
        'hours' => '24/7 Portal Aktif',
    ],
    
    /**
     * Trust Badges (untuk display)
     */
    'trust_badges' => [
        [
            'icon' => 'fa-check-circle',
            'label' => 'Terdaftar Resmi',
            'color' => 'green',
        ],
        [
            'icon' => 'fa-bolt',
            'label' => 'Proses 1-3 Hari',
            'color' => 'yellow',
        ],
        [
            'icon' => 'fa-shield-alt',
            'label' => 'Garansi Uang Kembali',
            'color' => 'blue',
        ],
        [
            'icon' => 'fa-star',
            'label' => '98% Rating',
            'color' => 'yellow',
        ],
    ],
    
    /**
     * Statistics untuk Display (formatted)
     */
    'display' => [
        'clients_total' => '500+',
        'projects_completed' => '138',
        'sla_rate' => '96%',
        'provinces' => '18',
        'permits_processed' => '1.000+',
        'experience_years' => '10+',
        'process_time' => '1-3 Hari',
        'satisfaction' => '98%',
        'legal_compliance' => '100%',
    ],
    
];
