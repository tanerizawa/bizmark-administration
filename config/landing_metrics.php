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
     * NOTE: Hindari angka spesifik yang sulit diverifikasi
     */
    'clients' => [
        'total' => null,                   // Don't show specific number
        'active_this_month' => null,       // Don't advertise
        'satisfaction_rate' => null,       // Unverifiable claim
        'industries' => 6,                 // Jumlah sektor industri yang dilayani
    ],

    /**
     * Project Statistics
     * NOTE: Fokus pada kapabilitas, bukan klaim angka
     */
    'projects' => [
        'completed' => null,               // Don't show specific number (inconsistent)
        'sla_success_rate' => null,        // Unverifiable claim
        'active_this_month' => null,       // Don't advertise
    ],

    /**
     * Coverage Area
     * NOTE: "Berbagai kota" lebih humble dan tidak defensive
     */
    'coverage' => [
        'provinces' => null,               // Don't specify (sounds defensive)
        'cities' => 'berbagai',            // "Berbagai kota" - humble approach
    ],

    /**
     * Permits & Licenses
     * NOTE: Fokus pada jenis layanan, bukan volume
     */
    'permits' => [
        'total_processed' => null,         // Don't show inflated numbers
        'types_available' => 15,           // Jenis perizinan yang ditangani (factual)
    ],

    /**
     * Company Experience
     * NOTE: Waktu operasional = factual, bisa dibuktikan
     * Founded 2014 (as documented in about page story, first client 2014)
     */
    'experience' => [
        'years' => (int) date('Y') - 2014, // Dynamic calculation from since_year
        'since_year' => 2014,              // Berdiri sejak tahun (bukan 2015 — PT terdaftar 2014)
    ],

    /**
     * Performance Metrics
     * NOTE: Timeline proses adalah faktual, bukan klaim kepuasan
     */
    'performance' => [
        'average_days_min' => 1,           // Waktu proses minimum (hari)
        'average_days_max' => 3,           // Waktu proses maksimum (hari)
        'sla_percentage' => null,          // Unverifiable
        'success_rate' => null,            // Unverifiable
    ],

    /**
     * Certifications & Awards
     * NOTE: REMOVE - sounds defensive and unverifiable
     */
    'certifications' => [
        'iso' => null,                     // Don't claim unless real
        'award' => null,                   // Don't claim
        'government_partner' => false,     // Don't mention unless critical
    ],

    /**
     * Contact Information
     * NOTE: Hide WA number from direct display to prevent spam
     */
    'contact' => [
        'phone' => '+62 838 7960 2855',
        'phone_display' => 'Hubungi Kami',      // Don't show direct number
        'whatsapp' => '6283879602855',
        'whatsapp_link' => 'https://wa.me/6283879602855',  // Use link instead
        'email' => 'info@bizmark.id',
        'hr_email' => 'hr@bizmark.id',
        'hours' => 'Portal Aktif 24/7',
    ],

    /**
     * Trust Badges (untuk display)
     * NOTE: REMOVE defensive badges, focus on platform capabilities
     */
    'trust_badges' => [
        [
            'icon' => 'fa-desktop',
            'label' => 'Portal Digital',
            'color' => 'blue',
        ],
        [
            'icon' => 'fa-clock',
            'label' => 'Monitoring Real-Time',
            'color' => 'blue',
        ],
    ],

    /**
     * Statistics untuk Display (formatted)
     * NOTE: Remove all unverifiable claims, focus on capabilities
     */
    'display' => [
        'clients_total' => null,           // Don't show
        'projects_completed' => null,      // Don't show
        'sla_rate' => null,               // Unverifiable
        'provinces' => null,              // Changed to "berbagai kota"
        'cities' => 'Berbagai Kota',      // Humble, not defensive
        'permits_processed' => null,      // Inflated number removed
        'experience_years' => '10+',
        'process_time' => '1-3 Hari',
        'satisfaction' => null,           // Unverifiable
        'legal_compliance' => null,       // Too defensive ("100%")
        'service_types' => '15+ Jenis Layanan',
        'platform_uptime' => '24/7',
    ],

    /**
     * Hero Trust Indicators + Shared Stats — Single Source of Truth
     *
     * AUDIT MAY 2026 — Per request user: hapus semua data fake/halusinasi.
     * Hanya "berdiri sejak 2014" yang dapat diverifikasi. Semua angka spesifik
     * lainnya (klien, izin, SLA, success rate) di-null-kan sampai ada data riil
     * yang bisa dipertanggungjawabkan.
     *
     * Untuk mengaktifkan kembali: ubah null → string angka, view sudah handle nullable.
     */
    'stats' => [
        'experience_label' => ((int) date('Y') - 2014).'+ Tahun', // Dynamic, factual
        'clients_label' => null, // Tidak diverifikasi
        'clients_since_label' => 'Sejak 2014',                          // Factual: tanggal pendirian
        'clients_active_label' => null, // Tidak diverifikasi
        'permits_label' => null, // Tidak diverifikasi
        'permits_issued_label' => null, // Tidak diverifikasi
        'success_label' => null, // Unverifiable
        'sla_ontime_label' => null, // Unverifiable
    ],

    /**
     * AggregateRating — schema.org
     * Disabled per audit May 2026: rating dan review count tidak dapat
     * diverifikasi secara internal saat ini.
     */
    'aggregate_rating' => [
        'enabled' => false,
        'rating_value' => null,
        'best_rating' => '5',
        'worst_rating' => '1',
        'review_count' => null,
    ],

    /**
     * Newsletter — subscriber count
     * Disabled per audit May 2026: angka subscriber tidak dapat diverifikasi.
     */
    'newsletter' => [
        'subscriber_count' => null,
        'subscriber_count_en' => null,
    ],

];
