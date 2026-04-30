<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ServiceComparisonController extends Controller
{
    /**
     * All available comparisons (manually curated pairs)
     */
    protected array $comparisons = [
        'amdal-vs-ukl-upl' => ['amdal', 'ukl-upl'],
        'perizinan-lb3-vs-amdal' => ['perizinan-lb3', 'amdal'],
        'pbg-slf-vs-izin-operasional' => ['pbg-slf', 'izin-operasional'],
        'oss-nib-vs-izin-operasional' => ['oss-nib', 'izin-operasional'],
        'ukl-upl-vs-konsultan-lingkungan' => ['ukl-upl', 'konsultan-lingkungan'],
        'amdal-vs-perizinan-lb3' => ['amdal', 'perizinan-lb3'],
        'izin-k3-vs-izin-operasional' => ['izin-k3', 'izin-operasional'],
        'perizinan-lb3-vs-monitoring-digital' => ['perizinan-lb3', 'monitoring-digital'],
    ];

    /**
     * Comparison index page listing all available comparisons
     */
    public function index()
    {
        $allServices = config('services_data');
        $pairs = [];

        foreach ($this->comparisons as $slug => [$slugA, $slugB]) {
            if (isset($allServices[$slugA], $allServices[$slugB])) {
                $pairs[$slug] = [
                    'a' => $allServices[$slugA],
                    'b' => $allServices[$slugB],
                    'slug' => $slug,
                ];
            }
        }

        return view('programmatic.comparison-index', compact('pairs'));
    }

    /**
     * Individual comparison page
     */
    public function show(string $comparisonSlug)
    {
        if (! isset($this->comparisons[$comparisonSlug])) {
            abort(404);
        }

        [$slugA, $slugB] = $this->comparisons[$comparisonSlug];
        $allServices = config('services_data');

        if (! isset($allServices[$slugA], $allServices[$slugB])) {
            abort(404);
        }

        $serviceA = $allServices[$slugA];
        $serviceB = $allServices[$slugB];
        $year = (int) date('Y');

        $pageData = $this->buildComparisonData($serviceA, $serviceB, $slugA, $slugB, $year);

        // Related articles
        $relatedArticles = Cache::remember(
            "comparison.articles.{$comparisonSlug}",
            3600,
            function () use ($serviceA, $serviceB) {
                return Article::published()
                    ->byLanguage('id')
                    ->where(function ($q) use ($serviceA, $serviceB) {
                        $q->where('title', 'LIKE', '%'.$serviceA['title'].'%')
                            ->orWhere('title', 'LIKE', '%'.$serviceB['title'].'%');
                    })
                    ->orderBy('views_count', 'desc')
                    ->take(4)
                    ->get();
            }
        );

        // Other comparisons for cross-linking
        $otherComparisons = collect($this->comparisons)
            ->except($comparisonSlug)
            ->map(function ($pair, $slug) use ($allServices) {
                [$a, $b] = $pair;
                if (! isset($allServices[$a], $allServices[$b])) {
                    return null;
                }

                return [
                    'slug' => $slug,
                    'a' => $allServices[$a],
                    'b' => $allServices[$b],
                ];
            })
            ->filter()
            ->take(4);

        return view('programmatic.comparison-show', compact(
            'serviceA', 'serviceB', 'slugA', 'slugB',
            'pageData', 'year', 'relatedArticles', 'otherComparisons',
            'comparisonSlug'
        ));
    }

    protected function buildComparisonData(array $a, array $b, string $slugA, string $slugB, int $year): array
    {
        $titleA = $a['title'];
        $titleB = $b['title'];

        return [
            'meta_title' => "Perbedaan {$titleA} dan {$titleB} {$year} | Bizmark.ID",
            'meta_description' => "Apa perbedaan {$titleA} dan {$titleB}? Panduan lengkap perbandingan, persyaratan, dan kapan Anda membutuhkan masing-masing layanan perizinan.",
            'meta_keywords' => "perbedaan {$titleA} dan {$titleB}, {$titleA} vs {$titleB}, perbandingan perizinan, {$titleA}, {$titleB}",
            'h1' => "Perbedaan {$titleA} dan {$titleB} ({$year})",
            'intro' => "Bingung memilih antara {$titleA} dan {$titleB}? Kedua perizinan ini memiliki tujuan dan persyaratan yang berbeda. Panduan ini akan membantu Anda memahami perbedaan utama, kapan masing-masing dibutuhkan, dan bagaimana Bizmark.ID dapat membantu proses pengurusannya.",

            'comparison_table' => [
                ['aspect' => 'Deskripsi', 'a' => $a['short_description'] ?? '-', 'b' => $b['short_description'] ?? '-'],
                ['aspect' => 'Kategori', 'a' => $a['category'] ?? 'Perizinan', 'b' => $b['category'] ?? 'Perizinan'],
                ['aspect' => 'Waktu Proses', 'a' => $a['process_time'] ?? 'Bervariasi', 'b' => $b['process_time'] ?? 'Bervariasi'],
                ['aspect' => 'Cocok Untuk', 'a' => $this->getSuitableFor($a), 'b' => $this->getSuitableFor($b)],
            ],

            'faqs' => [
                [
                    'question' => "Apa perbedaan utama {$titleA} dan {$titleB}?",
                    'answer' => "{$titleA} berfokus pada ".($a['short_description'] ?? 'perizinan spesifik').", sedangkan {$titleB} berfokus pada ".($b['short_description'] ?? 'perizinan spesifik').'. Keduanya memiliki persyaratan dan proses yang berbeda.',
                ],
                [
                    'question' => "Apakah saya perlu {$titleA} dan {$titleB} sekaligus?",
                    'answer' => 'Tergantung jenis dan skala usaha Anda. Beberapa perusahaan membutuhkan keduanya, terutama industri yang memiliki dampak lingkungan signifikan. Hubungi Bizmark.ID untuk konsultasi gratis.',
                ],
                [
                    'question' => "Mana yang lebih mudah diurus, {$titleA} atau {$titleB}?",
                    'answer' => "Tingkat kompleksitas tergantung pada skala usaha dan jenis kegiatan. {$titleA} dan {$titleB} masing-masing memiliki persyaratan unik. Dengan bantuan konsultan berpengalaman seperti Bizmark.ID, keduanya dapat diselesaikan secara efisien.",
                ],
                [
                    'question' => "Berapa biaya pengurusan {$titleA} versus {$titleB}?",
                    'answer' => 'Biaya bervariasi berdasarkan kompleksitas dan lokasi. Secara umum, izin yang memerlukan kajian lebih mendalam akan memiliki biaya lebih tinggi. Hubungi kami untuk penawaran yang disesuaikan.',
                ],
            ],

            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => [],
            ],
        ];
    }

    protected function getSuitableFor(array $service): string
    {
        $cat = $service['category'] ?? '';

        return match (strtoupper($cat)) {
            'LINGKUNGAN' => 'Perusahaan dengan dampak lingkungan',
            'BANGUNAN' => 'Proyek pembangunan gedung/fasilitas',
            'INDUSTRI' => 'Industri manufaktur & operasional',
            'K3' => 'Perusahaan dengan risiko keselamatan kerja',
            default => 'Berbagai jenis usaha',
        };
    }
}
