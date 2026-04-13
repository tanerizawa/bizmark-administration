@extends('landing.layout')

@section('title', 'Buat File SHP Online Gratis - Polygon SHP Maker untuk OSS RBA | Bizmark.ID')
@section('description', 'Buat file Shapefile (.shp) online gratis tanpa install software. Gambar poligon di peta interaktif, isi metadata lokasi lengkap, dan unduh file SHP siap upload ke OSS RBA dengan proyeksi WGS84. Tool pembuat SHP terlengkap di Indonesia.')
@section('meta_title', 'Buat File SHP Online Gratis - Polygon SHP Maker untuk OSS RBA | Bizmark.ID')
@section('meta_description', 'Cara buat file SHP online gratis tanpa install software. Polygon SHP Maker dengan peta interaktif, proyeksi WGS84, dan format ESRI Shapefile standar OSS RBA. Unduh langsung dalam ZIP.')
@section('meta_keywords', 'buat file shp online, buat file shp gratis, polygon shp maker, cara buat shp untuk oss, shapefile generator oss rba, cara membuat file shp, buat shp online gratis, konversi polygon ke shp, tools shp gratis indonesia, shapefile wgs84 oss, download shapefile gratis, cara buat file shp untuk perizinan, pembuat shp online, file shp untuk oss rba, polygon to shapefile online free')
@section('og_title', 'Buat File SHP Online Gratis - Polygon SHP Maker untuk OSS | Bizmark.ID')
@section('og_description', 'Tool gratis membuat file Shapefile (.shp) untuk OSS tanpa install software. Gambar poligon di peta interaktif, isi metadata lokasi, unduh langsung dalam format ZIP. Proyeksi WGS84 standar.')
@section('og_type', 'website')
@section('twitter_title', 'Buat File SHP Online Gratis - Polygon SHP Maker untuk OSS')
@section('twitter_description', 'Cara buat file SHP online gratis untuk OSS RBA. Gambar poligon di peta interaktif, unduh langsung dalam format ZIP. Tanpa install software, gratis tanpa batas.')

@section('structured_data')
{{-- WebApplication Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "Polygon SHP Maker - Buat File SHP Online Gratis",
    "alternateName": ["Pembuat SHP Online", "SHP Maker Gratis", "Shapefile Generator Indonesia"],
    "url": "{{ url('/polygon-shp-maker') }}",
    "description": "Tool gratis untuk membuat file Shapefile (.shp) online tanpa install software. Gambar poligon di peta interaktif, isi metadata lokasi lengkap, dan unduh file SHP dengan proyeksi WGS84 standar OSS RBA. Gratis tanpa batas penggunaan.",
    "applicationCategory": "UtilityApplication",
    "applicationSubCategory": "GIS Tool",
    "operatingSystem": "Web Browser",
    "browserRequirements": "Requires JavaScript. Works on Chrome, Firefox, Safari, Edge.",
    "softwareVersion": "2.0",
    "inLanguage": "id",
    "isAccessibleForFree": true,
    "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "IDR",
        "availability": "https://schema.org/InStock"
    },
    "featureList": [
        "Gambar poligon interaktif di peta",
        "Proyeksi WGS84 standar OSS RBA",
        "Format ESRI Shapefile (.shp/.shx/.dbf/.prj)",
        "Unduh dalam format ZIP siap upload",
        "Gratis tanpa batas penggunaan",
        "Auto-save data di browser",
        "Import koordinat dari CSV",
        "Hitung luas otomatis (m² dan hektar)",
        "Resize poligon ke luas target",
        "Metadata lokasi lengkap (provinsi hingga kelurahan)"
    ],
    "screenshot": "{{ asset('images/og-image-id.jpg') }}",
    "author": {
        "@@type": "Organization",
        "name": "PT Cangah Pajaratan Mandiri",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo.png') }}"
    },
    "aggregateRating": {
        "@@type": "AggregateRating",
        "ratingValue": "4.8",
        "ratingCount": "150",
        "bestRating": "5"
    }
}
</script>
{{-- HowTo Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "HowTo",
    "name": "Cara Membuat File SHP untuk OSS dengan Polygon SHP Maker",
    "description": "Panduan langkah demi langkah membuat file Shapefile (.shp) gratis untuk diunggah ke sistem OSS RBA menggunakan Polygon SHP Maker Bizmark.ID.",
    "image": "{{ asset('images/og-image-id.jpg') }}",
    "totalTime": "PT5M",
    "estimatedCost": {
        "@@type": "MonetaryAmount",
        "currency": "IDR",
        "value": "0"
    },
    "supply": [
        {
            "@@type": "HowToSupply",
            "name": "Browser (Chrome, Firefox, Safari, atau Edge)"
        },
        {
            "@@type": "HowToSupply",
            "name": "Koneksi internet"
        }
    ],
    "tool": {
        "@@type": "HowToTool",
        "name": "Polygon SHP Maker by Bizmark.ID",
        "url": "{{ url('/polygon-shp-maker') }}"
    },
    "step": [
        {
            "@@type": "HowToStep",
            "position": 1,
            "name": "Pilih Lokasi di Peta",
            "text": "Pilih Provinsi, Kabupaten, Kecamatan, dan Kelurahan di panel Data Lokasi. Peta akan otomatis berpindah ke wilayah yang dipilih.",
            "url": "{{ url('/polygon-shp-maker') }}#step-1"
        },
        {
            "@@type": "HowToStep",
            "position": 2,
            "name": "Gambar Poligon Batas Lahan",
            "text": "Klik ikon poligon di toolbar peta, lalu klik titik-titik di peta untuk menandai batas lahan. Klik titik pertama atau tombol Finish untuk menutup poligon.",
            "url": "{{ url('/polygon-shp-maker') }}#step-2"
        },
        {
            "@@type": "HowToStep",
            "position": 3,
            "name": "Edit dan Sesuaikan Bentuk Poligon",
            "text": "Klik ikon Edit untuk mengedit bentuk poligon. Geser titik sudut untuk memindahkan, atau tarik titik tengah sisi untuk menambah titik baru. Gunakan fitur Resize untuk menyesuaikan luas.",
            "url": "{{ url('/polygon-shp-maker') }}#step-3"
        },
        {
            "@@type": "HowToStep",
            "position": 4,
            "name": "Isi Metadata Lokasi dan Data Pemohon",
            "text": "Isi kolom Nama Lahan/Proyek (wajib), email, dan nomor telepon. Data wilayah dan keterangan bersifat opsional namun akan tersimpan di atribut file SHP.",
            "url": "{{ url('/polygon-shp-maker') }}#step-4"
        },
        {
            "@@type": "HowToStep",
            "position": 5,
            "name": "Unduh File SHP dalam Format ZIP",
            "text": "Klik tombol Buat & Unduh SHP untuk mengunduh file ZIP berisi .shp, .shx, .dbf, dan .prj yang siap diunggah ke sistem OSS RBA.",
            "url": "{{ url('/polygon-shp-maker') }}#step-5"
        }
    ]
}
</script>
{{-- BreadcrumbList Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Beranda",
            "item": "{{ url('/') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "Layanan",
            "item": "{{ url('/layanan') }}"
        },
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "Polygon SHP Maker",
            "item": "{{ url('/polygon-shp-maker') }}"
        }
    ]
}
</script>
{{-- FAQPage Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {
            "@@type": "Question",
            "name": "Apa itu file SHP (Shapefile)?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "File SHP (Shapefile) adalah format file geospasial standar yang dikembangkan oleh ESRI. File ini digunakan untuk menyimpan data lokasi berupa titik, garis, atau poligon beserta atribut datanya. Dalam konteks perizinan di Indonesia, file SHP digunakan untuk mengunggah data batas lahan ke sistem OSS (Online Single Submission) dan RBA (Risk-Based Approach)."
            }
        },
        {
            "@@type": "Question",
            "name": "Bagaimana cara membuat file SHP untuk OSS?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Anda bisa membuat file SHP untuk OSS secara gratis menggunakan Polygon SHP Maker di Bizmark.ID. Caranya: (1) Pilih lokasi di peta, (2) Gambar poligon batas lahan dengan klik pada peta, (3) Isi metadata lokasi seperti nama lahan dan wilayah, (4) Klik Buat & Unduh SHP. File ZIP berisi .shp, .shx, .dbf, dan .prj akan langsung terunduh - siap upload ke OSS."
            }
        },
        {
            "@@type": "Question",
            "name": "Apakah Polygon SHP Maker ini gratis?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Ya, Polygon SHP Maker di Bizmark.ID sepenuhnya gratis tanpa batas penggunaan. Tidak perlu membuat akun, tidak ada batasan jumlah file yang bisa dibuat, dan tidak ada watermark pada file SHP yang dihasilkan."
            }
        },
        {
            "@@type": "Question",
            "name": "Apa saja komponen file SHP yang dihasilkan?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Polygon SHP Maker menghasilkan file ZIP yang berisi 4 komponen standar ESRI Shapefile: (1) .shp - data geometri poligon, (2) .shx - index spasial, (3) .dbf - atribut/metadata lokasi, dan (4) .prj - informasi proyeksi WGS84. Keempat file ini wajib ada saat upload ke sistem OSS."
            }
        },
        {
            "@@type": "Question",
            "name": "Proyeksi apa yang digunakan pada file SHP?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "File SHP yang dihasilkan menggunakan proyeksi WGS84 (EPSG:4326), yaitu sistem koordinat standar yang digunakan oleh GPS dan diterima oleh sistem OSS/RBA pemerintah Indonesia. Proyeksi ini memastikan file SHP Anda kompatibel dan dapat langsung diunggah tanpa konversi tambahan."
            }
        },
        {
            "@@type": "Question",
            "name": "Apakah perlu install software untuk membuat file SHP?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Tidak perlu. Polygon SHP Maker berjalan sepenuhnya di browser (Chrome, Firefox, Safari, Edge) tanpa perlu menginstal software apapun seperti QGIS atau ArcGIS. Cukup buka halaman, gambar poligon, dan unduh file SHP - semua bisa dilakukan langsung dari browser."
            }
        },
        {
            "@@type": "Question",
            "name": "Berapa banyak titik koordinat yang bisa dimasukkan?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Polygon SHP Maker mendukung hingga {{ $maxPoints ?? 500 }} titik koordinat per poligon. Minimal 3 titik diperlukan untuk membentuk sebuah poligon. Anda juga bisa mengimpor koordinat dari file CSV atau teks."
            }
        },
        {
            "@@type": "Question",
            "name": "Apakah data saya aman di Polygon SHP Maker?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Ya. Data formulir dan poligon otomatis tersimpan sementara di browser (localStorage) selama 24 jam untuk kenyamanan Anda. File SHP yang dihasilkan dihapus dari server secara otomatis setelah proses unduh selesai. Kami tidak menyimpan data geospasial Anda secara permanen di server."
            }
        }
    ]
}
</script>
@endsection

@push('styles')
<link rel="preconnect" href="https://unpkg.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha384-sHL9NAb7lN7rfvG5lfHpm643Xkcjzp4jFvuavGOndn6pjVqS6ny56CAt3nsEVT4H" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" integrity="sha384-NZLkVuBRMEeB4VeZz27WwTRvlhec30biQ8Xx7zG7JJnkvEKRg5qi6BNbEXo9ydwv" crossorigin="anonymous" />
<style>
    #map { height: 500px; width: 100%; border-radius: 0.75rem; z-index: 1; }
    @media (min-width: 1024px) { #map { height: 600px; } }
    .coord-table { max-height: 250px; overflow-y: auto; }
    .coord-table::-webkit-scrollbar { width: 4px; }
    .coord-table::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }
    .stat-card { transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    /* Fix disabled select styling */
    select:disabled {
        background-color: #f3f4f6 !important;
        color: #9ca3af !important;
        cursor: not-allowed;
        opacity: 0.7;
    }
    /* Generate button */
    .btn-generate {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-generate.is-ready {
        animation: pulseReady 2s ease-in-out infinite;
    }
    .btn-generate.is-ready:hover {
        animation: none;
        transform: translateY(-1px);
    }
    @keyframes pulseReady {
        0%, 100% { box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3); }
        50% { box-shadow: 0 4px 24px rgba(5, 150, 105, 0.5); }
    }
    /* Autosave indicator */
    .autosave-indicator {
        transition: opacity 0.3s ease;
    }
    @keyframes fadeInOut {
        0% { opacity: 0; }
        20% { opacity: 1; }
        80% { opacity: 1; }
        100% { opacity: 0; }
    }
    /* Better input focus transitions */
    input, select, textarea {
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    /* Step indicator transition */
    .step-badge {
        transition: all 0.3s ease;
    }
    /* Hide elements until Alpine initializes */
    [x-cloak] { display: none !important; }
    /* Import modal */
    .import-modal-backdrop {
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(2px);
    }
    /* Stats loading shimmer */
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .stat-loading {
        background: linear-gradient(90deg, #e5e7eb 25%, #f3f4f6 50%, #e5e7eb 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    /* Toast notification */
    .toast-enter {
        animation: toastIn 0.3s ease;
    }
    @keyframes toastIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-blue-50 py-20" x-data="polygonShpApp()">
    <div class="container mx-auto px-4 max-w-7xl">

        <!-- Breadcrumb Navigation -->
        <nav class="mb-6 text-sm" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2 text-gray-500">
                <li><a href="{{ url('/') }}" class="hover:text-emerald-600 transition">Beranda</a></li>
                <li><i class="fas fa-chevron-right text-xs text-gray-300" aria-hidden="true"></i></li>
                <li><a href="{{ route('services.index.id') }}" class="hover:text-emerald-600 transition">Layanan</a></li>
                <li><i class="fas fa-chevron-right text-xs text-gray-300" aria-hidden="true"></i></li>
                <li class="text-emerald-700 font-medium" aria-current="page">Polygon SHP Maker</li>
            </ol>
        </nav>

        <!-- Header -->
        <header class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Buat File <span class="text-emerald-600">SHP Online Gratis</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Polygon SHP Maker &mdash; gambar poligon di peta interaktif, isi data lokasi, lalu unduh file Shapefile (.shp) siap upload ke <strong>OSS RBA</strong>. Tanpa install software, 100% gratis.
            </p>
        </header>

        <!-- Step Indicators -->
        <div class="flex justify-center mb-10">
            <div class="flex items-center gap-2 md:gap-4 text-sm md:text-base">
                <div class="flex items-center gap-2 px-4 py-2 rounded-full transition-all"
                     :class="step >= 1 ? 'bg-emerald-100 text-emerald-700 font-semibold' : 'bg-gray-100 text-gray-500'">
                    <span class="step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                          :style="step >= 1 ? 'background-color: #059669; color: #fff;' : 'background-color: #d1d5db; color: #4b5563;'">1</span>
                    Gambar Poligon
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
                <div class="flex items-center gap-2 px-4 py-2 rounded-full transition-all"
                     :class="step >= 2 ? 'bg-emerald-100 text-emerald-700 font-semibold' : 'bg-gray-100 text-gray-500'">
                    <span class="step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                          :style="step >= 2 ? 'background-color: #059669; color: #fff;' : 'background-color: #d1d5db; color: #4b5563;'">2</span>
                    Isi Metadata
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
                <div class="flex items-center gap-2 px-4 py-2 rounded-full transition-all"
                     :class="step >= 3 ? 'bg-emerald-100 text-emerald-700 font-semibold' : 'bg-gray-100 text-gray-500'">
                    <span class="step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                          :style="step >= 3 ? 'background-color: #059669; color: #fff;' : 'background-color: #d1d5db; color: #4b5563;'">3</span>
                    Unduh SHP
                </div>
            </div>
        </div>

        <!-- Data Restored Notification -->
        <div class="max-w-2xl mx-auto mb-6" x-show="restoredMsg" x-transition.duration.300ms role="status" aria-live="polite">
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
                <span><i class="fas fa-history mr-2" aria-hidden="true"></i><span x-text="restoredMsg"></span></span>
                <button @click="restoredMsg = ''" class="text-blue-400 hover:text-blue-600 ml-3" aria-label="Tutup notifikasi">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <!-- Toast Notification -->
        <div class="fixed top-6 right-6 z-50" x-show="toastMsg" x-cloak x-transition.duration.300ms role="alert" aria-live="assertive">
            <div class="toast-enter px-4 py-3 rounded-xl shadow-lg text-sm flex items-center gap-2" :class="toastType === 'success' ? 'bg-emerald-600 text-white' : toastType === 'error' ? 'bg-red-600 text-white' : 'bg-blue-600 text-white'">
                <i class="fas" :class="toastType === 'success' ? 'fa-check-circle' : toastType === 'error' ? 'fa-times-circle' : 'fa-info-circle'" aria-hidden="true"></i>
                <span x-text="toastMsg"></span>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid lg:grid-cols-3 gap-6">

            <!-- Map (2 cols) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-map-marked-alt text-emerald-600 mr-2" aria-hidden="true"></i>Peta Interaktif
                        </h2>
                        <div class="flex items-center gap-3">
                            <button @click="importCoordinates()" class="text-xs text-blue-600 hover:text-blue-800 font-medium" title="Import koordinat dari CSV/teks" aria-label="Import koordinat">
                                <i class="fas fa-file-import mr-1" aria-hidden="true"></i>Import
                            </button>
                            <span class="text-gray-300">|</span>
                            <span class="text-xs text-gray-500" x-text="coordinates.length + ' titik'" aria-live="polite"></span>
                            <button @click="confirmResetPolygon()" class="text-xs text-red-500 hover:text-red-700 font-medium" x-show="coordinates.length > 0" aria-label="Reset semua poligon">
                                <i class="fas fa-trash-alt mr-1" aria-hidden="true"></i>Reset
                            </button>
                        </div>
                    </div>
                    <div class="relative">
                    <div id="map" role="application" aria-label="Peta interaktif untuk menggambar poligon. Gunakan toolbar di kiri atas untuk menggambar, mengedit, atau menghapus poligon." tabindex="0"></div>

                    </div><!-- end relative map container -->
                    <div class="px-4 py-2 bg-gray-50 text-xs text-gray-400 flex items-center gap-2" x-show="isDrawing">
                        <i class="fas fa-lightbulb text-amber-400" aria-hidden="true"></i>
                        <span>Tips: Tekan <kbd class="px-1 py-0.5 bg-gray-200 rounded text-gray-600">Backspace</kbd> untuk membatalkan titik terakhir, <kbd class="px-1 py-0.5 bg-gray-200 rounded text-gray-600">Escape</kbd> untuk membatalkan gambar.</span>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4" x-show="coordinates.length >= 3" x-transition role="region" aria-label="Statistik poligon" aria-live="polite">
                    <div class="stat-card bg-white rounded-xl p-4 shadow-md text-center">
                        <div class="text-xs text-gray-500 mb-1">Luas (m²)</div>
                        <div class="text-lg font-bold text-gray-900" x-show="!calculatingStats" x-text="formatNumber(areaM2)"></div>
                        <div class="h-6 rounded stat-loading" x-show="calculatingStats"></div>
                    </div>
                    <div class="stat-card bg-white rounded-xl p-4 shadow-md text-center">
                        <div class="text-xs text-gray-500 mb-1">Luas (Ha)</div>
                        <div class="text-lg font-bold text-emerald-600" x-show="!calculatingStats" x-text="formatNumber(areaHa, 4)"></div>
                        <div class="h-6 rounded stat-loading" x-show="calculatingStats"></div>
                    </div>
                    <div class="stat-card bg-white rounded-xl p-4 shadow-md text-center">
                        <div class="text-xs text-gray-500 mb-1">Keliling (m)</div>
                        <div class="text-lg font-bold text-gray-900" x-show="!calculatingStats" x-text="formatNumber(perimeterM)"></div>
                        <div class="h-6 rounded stat-loading" x-show="calculatingStats"></div>
                    </div>
                    <div class="stat-card bg-white rounded-xl p-4 shadow-md text-center">
                        <div class="text-xs text-gray-500 mb-1">Jumlah Titik</div>
                        <div class="text-lg font-bold text-gray-900" x-text="coordinates.length"></div>
                    </div>
                </div>

                <!-- Informasi Tata Ruang (RTRW) -->
                <div class="bg-white rounded-2xl shadow-xl mt-4 overflow-hidden" x-show="coordinates.length >= 3" x-transition>
                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-gray-800">
                                <i class="fas fa-city text-blue-600 mr-1.5" aria-hidden="true"></i>Informasi Tata Ruang
                            </h3>
                            <span x-show="rtrwZones.length > 0" class="text-[10px] px-1.5 py-0.5 rounded bg-blue-600 text-white font-bold leading-none">
                                <span x-text="rtrwZones.length"></span>
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">

                            <button @click="queryRtrwZona()" :disabled="rtrwLoading || !selectedProvinsi"
                                    class="w-6 h-6 rounded flex items-center justify-center text-[10px] transition disabled:opacity-20 text-blue-600 hover:bg-blue-50"
                                    x-show="!rtrwLoading" title="Refresh">
                                <i class="fas fa-sync-alt" aria-hidden="true"></i>
                            </button>
                            <span x-show="rtrwLoading" class="text-[10px] text-blue-500"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i></span>
                        </div>
                    </div>

                    <div class="p-4">
                        <!-- Prompt to select province -->
                        <div x-show="!selectedProvinsi && rtrwZones.length === 0" class="text-center py-4">
                            <i class="fas fa-map-marked-alt text-gray-300 text-2xl mb-2" aria-hidden="true"></i>
                            <p class="text-xs text-gray-500">Pilih provinsi untuk melihat informasi tata ruang.</p>
                        </div>

                        <!-- Loading state -->
                        <div x-show="rtrwLoading" class="py-4">
                            <div class="flex items-center justify-center gap-2 text-xs text-gray-500 mb-3">
                                <svg class="animate-spin h-3.5 w-3.5 text-blue-500" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                Mengambil data tata ruang...
                            </div>
                            <div class="space-y-2">
                                <div class="h-14 bg-gray-100 rounded-lg animate-pulse"></div>
                                <div class="h-10 bg-gray-100 rounded-lg animate-pulse"></div>
                            </div>
                        </div>

                        <!-- Error state -->
                        <div x-show="rtrwError && !rtrwLoading" x-transition class="py-2">
                            <div class="flex items-center gap-2 text-xs text-red-700 bg-red-50 rounded-lg px-3 py-2.5" role="alert">
                                <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0" aria-hidden="true"></i>
                                <span x-text="rtrwError"></span>
                            </div>
                            <button @click="queryRtrwZona()" class="mt-2 w-full text-[11px] text-blue-700 font-semibold py-1.5 rounded-lg hover:bg-blue-50 transition"
                                    x-show="selectedProvinsi">
                                <i class="fas fa-redo mr-1" aria-hidden="true"></i>Coba lagi
                            </button>
                        </div>

                        <!-- Query button when province selected but no data yet -->
                        <div x-show="selectedProvinsi && rtrwZones.length === 0 && !rtrwLoading && !rtrwError" class="text-center py-4">
                            <p class="text-xs text-gray-500 mb-2">Belum ada data zona di titik tengah poligon.</p>
                            <button @click="queryRtrwZona()" class="px-4 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                                <i class="fas fa-search-location mr-1" aria-hidden="true"></i>Cek Zona RTRW
                            </button>
                        </div>

                        <!-- Zone Results -->
                        <div x-show="rtrwZones.length > 0 && !rtrwLoading" x-transition>
                            <!-- Source row -->
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[10px] text-gray-400 mb-2.5">
                                <span><i class="fas fa-database mr-0.5" aria-hidden="true"></i> <span x-text="rtrwSource || 'GISTARU ATR/BPN'"></span></span>
                                <span><i class="fas fa-map-marker-alt mr-0.5" aria-hidden="true"></i> <span x-text="rtrwProvince"></span></span>
                            </div>

                            <!-- Zone Detail Cards -->
                            <div class="space-y-2">
                                <template x-for="(zone, idx) in rtrwZones" :key="'zone-'+idx">
                                    <div class="rounded-lg border overflow-hidden" :style="'border-color:' + _hexToRgba(_getZonaColor(zone.zona), 0.3)">
                                        <!-- Zone header: colored bar -->
                                        <div class="flex items-center gap-2 px-3 py-2" :style="'background:' + _getZonaColor(zone.zona)">
                                            <span class="w-2 h-2 rounded-full bg-white/50 flex-shrink-0"></span>
                                            <span class="font-bold text-xs leading-tight flex-1 min-w-0 truncate" :style="'color:' + _zonaTextColor(_getZonaColor(zone.zona))" x-text="zone.zona || 'Zona tidak diketahui'"></span>
                                            <span class="text-[9px] px-1.5 py-px rounded font-bold flex-shrink-0 bg-black/15" :style="'color:' + _zonaTextColor(_getZonaColor(zone.zona))" x-text="'#' + (idx + 1)"></span>
                                        </div>
                                        <!-- Zone body -->
                                        <div class="px-3 py-2 text-[11px] bg-white space-y-1.5">
                                            <!-- Tags row -->
                                            <div class="flex flex-wrap gap-1" x-show="zone.jenis_zona || zone.layer_name">
                                                <template x-if="zone.jenis_zona">
                                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold"
                                                          :style="'background:' + _hexToRgba(_getZonaColor(zone.zona), 0.12) + ';color:' + _getZonaColor(zone.zona)">
                                                        <i class="fas fa-tag text-[7px]" aria-hidden="true"></i>
                                                        <span x-text="zone.jenis_zona"></span>
                                                    </span>
                                                </template>
                                                <template x-if="zone.layer_name">
                                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">
                                                        <i class="fas fa-layer-group text-[7px]" aria-hidden="true"></i>
                                                        <span x-text="zone.layer_name.replace(/^_\d+_/, '')"></span>
                                                    </span>
                                                </template>
                                            </div>
                                            <!-- Admin area: compact inline grid -->
                                            <div class="grid grid-cols-[auto_1fr] gap-x-2 gap-y-0.5 text-[11px]" x-show="zone.kabupaten_kota || zone.kecamatan || zone.provinsi">
                                                <template x-if="zone.provinsi">
                                                    <div class="contents">
                                                        <span class="text-gray-400 whitespace-nowrap"><i class="fas fa-globe-asia w-3 text-center text-[9px]" aria-hidden="true"></i> Provinsi</span>
                                                        <span class="text-gray-800 font-medium" x-text="zone.provinsi"></span>
                                                    </div>
                                                </template>
                                                <template x-if="zone.kabupaten_kota">
                                                    <div class="contents">
                                                        <span class="text-gray-400 whitespace-nowrap"><i class="fas fa-building w-3 text-center text-[9px]" aria-hidden="true"></i> Kab/Kota</span>
                                                        <span class="text-gray-800 font-medium" x-text="zone.kabupaten_kota"></span>
                                                    </div>
                                                </template>
                                                <template x-if="zone.kecamatan">
                                                    <div class="contents">
                                                        <span class="text-gray-400 whitespace-nowrap"><i class="fas fa-map-pin w-3 text-center text-[9px]" aria-hidden="true"></i> Kecamatan</span>
                                                        <span class="text-gray-800 font-medium" x-text="zone.kecamatan"></span>
                                                    </div>
                                                </template>
                                            </div>
                                            <!-- Dasar Hukum -->
                                            <template x-if="zone.no_perda">
                                                <div class="bg-amber-50 border border-amber-200 rounded px-2.5 py-1.5">
                                                    <span class="text-amber-800 font-semibold"><i class="fas fa-gavel text-[9px] mr-0.5" aria-hidden="true"></i> Dasar Hukum:</span>
                                                    <span class="text-amber-900 break-words" x-text="zone.no_perda"></span>
                                                </div>
                                            </template>
                                            <!-- Catatan -->
                                            <template x-if="zone.remark && zone.remark !== 'Tidak Ada'">
                                                <div class="bg-gray-50 border border-gray-200 rounded px-2.5 py-1.5">
                                                    <span class="text-gray-600 font-semibold"><i class="fas fa-sticky-note text-[9px] mr-0.5" aria-hidden="true"></i> Catatan:</span>
                                                    <span class="text-gray-700" x-text="zone.remark"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Disclaimer -->
                            <div x-show="rtrwDisclaimer" class="mt-2.5 flex items-start gap-1.5 text-[10px] text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-2.5 py-2">
                                <i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0 text-amber-500" aria-hidden="true"></i>
                                <span x-text="rtrwDisclaimer"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sesuaikan Luas (Resize Area) -->
                <div class="bg-white rounded-2xl shadow-xl mt-4 overflow-hidden" x-show="coordinates.length >= 3" x-transition>
                    <div class="p-4">
                        <h3 class="text-sm font-bold text-gray-700 mb-3">
                            <i class="fas fa-expand-arrows-alt text-emerald-600 mr-2" aria-hidden="true"></i>Sesuaikan Luas
                        </h3>
                        <p class="text-xs text-gray-500 mb-3">Masukkan luas dari sertifikat tanah. Poligon akan diskalakan otomatis agar luasnya sesuai — posisi tetap di lokasi yang sama.</p>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input type="number" x-model.number="targetArea" min="1" step="any" 
                                       class="w-full px-3 py-2 pr-12 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                       placeholder="Contoh: 10000" 
                                       @keydown.enter="resizeToTargetArea()">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">m²</span>
                            </div>
                            <button @click="resizeToTargetArea()" 
                                    :disabled="!targetArea || targetArea <= 0 || resizingArea"
                                    class="px-4 py-2 text-sm font-semibold rounded-lg transition whitespace-nowrap"
                                    :class="targetArea > 0 ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                                <span x-show="!resizingArea"><i class="fas fa-compress-arrows-alt mr-1" aria-hidden="true"></i>Sesuaikan</span>
                                <span x-show="resizingArea" class="flex items-center gap-1"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i>Proses...</span>
                            </button>
                        </div>
                        <div class="mt-2 text-xs" x-show="areaM2 > 0 && targetArea > 0" x-cloak>
                            <span class="text-gray-500">Luas saat ini: <strong x-text="formatNumber(areaM2)"></strong> m²</span>
                            <span class="mx-1 text-gray-300">→</span>
                            <span class="text-emerald-600 font-semibold">Target: <span x-text="formatNumber(targetArea)"></span> m²</span>
                            <span class="text-gray-400 ml-1" x-show="areaM2 > 0">
                                (selisih: <span x-text="formatNumber(Math.abs(areaM2 - targetArea))"></span> m²)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Coordinate Table -->
                <div class="bg-white rounded-2xl shadow-xl mt-4 overflow-hidden" x-show="coordinates.length > 0" x-transition>
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-700">
                            <i class="fas fa-list-ol text-emerald-600 mr-2" aria-hidden="true"></i>Daftar Koordinat
                        </h3>
                        <div class="flex items-center gap-2">
                            <button @click="copyCoordinates()" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium" title="Salin koordinat ke clipboard" aria-label="Salin koordinat">
                                <i class="fas fa-copy mr-1" aria-hidden="true"></i>Salin
                            </button>
                            <span class="text-gray-300">|</span>
                            <button @click="showCoords = !showCoords" class="text-xs text-emerald-600 hover:text-emerald-800" :aria-expanded="showCoords">
                                <span x-text="showCoords ? 'Sembunyikan' : 'Tampilkan'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="coord-table" x-show="showCoords" x-transition>
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-500">No</th>
                                    <th class="px-4 py-2 text-left text-gray-500">Longitude</th>
                                    <th class="px-4 py-2 text-left text-gray-500">Latitude</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(coord, i) in coordinates" :key="i">
                                    <tr class="border-t border-gray-50 hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-600" x-text="i + 1"></td>
                                        <td class="px-4 py-2 font-mono text-gray-800" x-text="coord[0].toFixed(8)"></td>
                                        <td class="px-4 py-2 font-mono text-gray-800" x-text="coord[1].toFixed(8)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Legend & Usage Guide -->
                <div class="bg-white rounded-2xl shadow-xl mt-4 overflow-hidden" x-data="{ showLegend: true, showGuide: false }">
                    <!-- Legend -->
                    <div class="p-4 border-b border-gray-100">
                        <button @click="showLegend = !showLegend" class="w-full flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-700">
                                <i class="fas fa-map-signs text-emerald-600 mr-2"></i>Legenda Peta & Toolbar
                            </h3>
                            <i class="fas text-gray-400 text-xs" :class="showLegend ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>
                    <div x-show="showLegend" x-transition class="p-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-xs text-gray-700">
                            <!-- Toolbar icons -->
                            <div class="font-semibold text-gray-500 uppercase tracking-wider col-span-full mb-1">Toolbar Peta</div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg width="16" height="16" viewBox="0 0 16 16"><polygon points="8,2 14,6 12,14 4,14 2,6" fill="none" stroke="#059669" stroke-width="1.5"/></svg>
                                </span>
                                <div><span class="font-semibold text-gray-800">Draw a polygon</span> — Klik untuk mulai menggambar poligon di peta</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-pen text-gray-600 text-sm"></i>
                                </span>
                                <div><span class="font-semibold text-gray-800">Edit layers</span> — Geser titik poligon atau tambah titik baru dari titik tengah sisi</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-trash-alt text-gray-600 text-sm"></i>
                                </span>
                                <div><span class="font-semibold text-gray-800">Delete layers</span> — Klik poligon lalu simpan untuk menghapusnya</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-layer-group text-gray-600 text-sm"></i>
                                </span>
                                <div><span class="font-semibold text-gray-800">Layer switcher</span> — Beralih antara peta jalan (OSM) dan citra satelit (Esri)</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-plus text-gray-600 text-xs"></i><i class="fas fa-minus text-gray-600 text-xs ml-0.5"></i>
                                </span>
                                <div><span class="font-semibold text-gray-800">Zoom +/−</span> — Perbesar/perkecil tampilan peta</div>
                            </div>

                            <!-- Map symbols -->
                            <div class="font-semibold text-gray-500 uppercase tracking-wider col-span-full mt-3 mb-1">Simbol di Peta</div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-6 rounded border-2 flex-shrink-0" style="border-color: #059669; background-color: rgba(16,185,129,0.2);"></span>
                                <div><span class="font-semibold text-gray-800">Area poligon</span> — Bidang lahan yang digambar</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 flex-shrink-0 text-center">
                                    <span class="inline-block w-3 h-3 rounded-full bg-white border-2" style="border-color: #059669;"></span>
                                </span>
                                <div><span class="font-semibold text-gray-800">Titik sudut</span> — Vertex poligon (dapat digeser saat mode edit)</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 flex-shrink-0 text-center">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full opacity-50" style="background-color: #059669;"></span>
                                </span>
                                <div><span class="font-semibold text-gray-800">Titik tengah sisi</span> — Tarik untuk menambah titik baru saat edit</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 flex-shrink-0 text-center">
                                    <i class="fas fa-map-pin text-red-500"></i>
                                </span>
                                <div><span class="font-semibold text-gray-800">Marker lokasi</span> — Posisi kelurahan/desa yang dipilih</div>
                            </div>

                            <!-- UI elements -->
                            <div class="font-semibold text-gray-500 uppercase tracking-wider col-span-full mt-3 mb-1">Elemen Antarmuka</div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 flex-shrink-0 text-center">
                                    <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                </span>
                                <div><span class="font-semibold text-gray-800">Tersimpan</span> — Data otomatis tersimpan di browser (24 jam)</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-8 flex-shrink-0 text-center">
                                    <i class="fas fa-eraser text-gray-400 text-sm"></i>
                                </span>
                                <div><span class="font-semibold text-gray-800">Hapus tersimpan</span> — Bersihkan data yang disimpan di browser</div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Guide -->
                    <div class="border-t border-gray-100 p-4">
                        <button @click="showGuide = !showGuide" class="w-full flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-700">
                                <i class="fas fa-book-open text-emerald-600 mr-2"></i>Panduan Penggunaan
                            </h3>
                            <i class="fas text-gray-400 text-xs" :class="showGuide ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>
                    <div x-show="showGuide" x-transition class="px-4 pb-5">
                        <ol class="space-y-4 text-sm text-gray-700">
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">1</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Pilih Lokasi (Opsional)</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pilih Provinsi → Kabupaten → Kecamatan → Kelurahan di panel <strong>Data Lokasi</strong>.
                                    Peta akan otomatis berpindah ke wilayah yang dipilih.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">2</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Gambar Poligon</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Klik ikon poligon <svg class="inline w-3.5 h-3.5 -mt-0.5" viewBox="0 0 16 16"><polygon points="8,2 14,6 12,14 4,14 2,6" fill="none" stroke="#059669" stroke-width="2"/></svg> di toolbar kiri peta.
                                    Klik titik-titik di peta untuk menandai batas lahan, lalu klik titik pertama atau <strong>Finish</strong> untuk menutup poligon.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">3</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Edit Bentuk (Jika Perlu)</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Klik ikon <i class="fas fa-pen text-gray-500 text-xs"></i> <strong>Edit</strong> untuk mengedit.
                                    Geser <span class="inline-block w-2 h-2 rounded-full bg-white border-2 align-middle" style="border-color:#059669"></span> titik sudut untuk memindahkan, atau tarik
                                    <span class="inline-block w-2 h-2 rounded-full opacity-60 align-middle" style="background:#059669"></span> titik tengah sisi untuk menambah titik baru.
                                    Klik <strong>Save</strong> untuk menyimpan perubahan.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">4</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Isi Nama Lahan</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Isi kolom <strong>Nama Lahan / Proyek</strong> (wajib). Data wilayah dan keterangan bersifat opsional namun akan tersimpan di atribut file SHP.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">5</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Unduh File SHP</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Tombol <strong>Buat & Unduh SHP</strong> akan aktif (hijau) bila poligon sudah digambar dan nama lahan terisi.
                                    Klik untuk mengunduh file ZIP berisi .shp, .shx, .dbf, dan .prj yang siap diunggah ke OSS.</p>
                                </div>
                            </li>
                        </ol>
                        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                            <i class="fas fa-lightbulb text-amber-500 mr-1.5"></i>
                            <strong>Tips:</strong> Data formulir dan poligon otomatis tersimpan di browser selama 24 jam. Jika halaman ter-refresh, data akan dipulihkan secara otomatis.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar (1 col) -->
            <div class="space-y-6">

                <!-- Metadata Form -->
                <div class="bg-white rounded-2xl shadow-xl p-6" role="form" aria-label="Form data lokasi dan pemohon">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-edit text-emerald-600 mr-2" aria-hidden="true"></i>Data Lokasi / Proyek
                        </h2>
                        <div class="flex items-center gap-2">
                            <span class="autosave-indicator text-xs text-emerald-600 flex items-center gap-1" x-show="autoSaveMsg" x-transition role="status">
                                <i class="fas fa-check-circle" aria-hidden="true"></i><span x-text="autoSaveMsg"></span>
                            </span>
                            <button @click="clearSavedData()" class="text-xs text-gray-400 hover:text-red-500 transition" title="Hapus data tersimpan" x-show="hasSavedData" aria-label="Hapus data tersimpan dari browser">
                                <i class="fas fa-eraser" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lahan / Proyek <span class="text-red-500">*</span></label>
                            <input type="text" x-model="form.name" maxlength="100" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                   placeholder="Contoh: Lahan Gudang Cibitung">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Provinsi</label>
                            <select x-model="selectedProvinsi" @change="onProvinsiChange()"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white text-gray-900 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                                    :disabled="loadingAddr">
                                <option value="">-- Pilih Provinsi --</option>
                                <template x-for="prov in provinsiList" :key="prov.id">
                                    <option :value="prov.id" x-text="prov.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kabupaten / Kota</label>
                            <select x-model="selectedKabkota" @change="onKabkotaChange()"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white text-gray-900 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                                    :disabled="!selectedProvinsi || loadingAddr">
                                <option value="">-- Pilih Kabupaten/Kota --</option>
                                <template x-for="kab in kabkotaList" :key="kab.id">
                                    <option :value="kab.id" x-text="kab.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kecamatan</label>
                            <select x-model="selectedKecamatan" @change="onKecamatanChange()"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white text-gray-900 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                                    :disabled="!selectedKabkota || loadingAddr">
                                <option value="">-- Pilih Kecamatan --</option>
                                <template x-for="kec in kecamatanList" :key="kec.id">
                                    <option :value="kec.id" x-text="kec.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kelurahan / Desa</label>
                            <select x-model="selectedKelurahan" @change="onKelurahanChange()"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white text-gray-900 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                                    :disabled="!selectedKecamatan || loadingAddr">
                                <option value="">-- Pilih Kelurahan/Desa --</option>
                                <template x-for="kel in kelurahanList" :key="kel.id">
                                    <option :value="kel.id" x-text="kel.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div x-show="loadingAddr" class="flex items-center gap-2 text-xs text-emerald-600" role="status">
                            <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            Memuat data wilayah...
                        </div>
                        <div x-show="addrError" x-transition class="text-xs text-red-500 flex items-center gap-1" role="alert">
                            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                            <span x-text="addrError"></span>
                            <button @click="retryLoadAddress()" class="ml-1 underline hover:text-red-700">Coba lagi</button>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan</label>
                            <textarea x-model="form.keterangan" maxlength="200" rows="2"
                                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none"
                                      placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Data Pemohon -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-user-tie text-emerald-600 mr-2" aria-hidden="true"></i>Data Pemohon
                    </h2>
                    <p class="text-xs text-gray-500 mb-4">Informasi kontak diperlukan untuk mengunduh file SHP.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Perusahaan / Badan Usaha</label>
                            <input type="text" x-model="form.company_name" maxlength="150"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                   placeholder="Contoh: PT Maju Jaya Sejahtera">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Penanggung Jawab</label>
                            <input type="text" x-model="form.contact_person" maxlength="100"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                   placeholder="Nama lengkap PIC">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="email" x-model="form.email" maxlength="150" required
                                       @blur="checkEmail()" @input="_debouncedCheckEmail()"
                                       :readonly="authClient !== null"
                                       class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                       :class="emailRegistered && !authClient ? 'border-amber-400 bg-amber-50' : authClient ? 'border-emerald-300 bg-emerald-50' : 'border-gray-300'"
                                       placeholder="email@perusahaan.com">
                                <span x-show="emailChecking" class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i class="fas fa-spinner fa-spin text-gray-400 text-xs"></i>
                                </span>
                                <span x-show="authClient && !emailChecking" class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                                </span>
                            </div>
                            <!-- Login required prompt -->
                            <div x-show="emailRegistered && !authClient" x-transition class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                <p class="text-xs text-amber-800 font-medium mb-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Email ini sudah terdaftar. Silakan login terlebih dahulu.
                                </p>
                                <a href="/login?redirect={{ urlencode(request()->fullUrl()) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                                <span class="text-xs text-gray-500 ml-2">atau</span>
                                <a href="/login?redirect={{ urlencode(request()->fullUrl()) }}" 
                                   class="text-xs text-blue-600 hover:text-blue-800 font-medium underline">gunakan email lain</a>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">No. WhatsApp / Telepon <span class="text-red-500">*</span></label>
                            <input type="tel" x-model="form.phone" maxlength="30" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                   placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                </div>

                <!-- Syarat & Ketentuan -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="agreedTerms" x-model="agreedTerms"
                               class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="agreedTerms" class="text-xs text-gray-600 leading-relaxed">
                            Dengan menggunakan fitur Polygon SHP Maker, saya menyetujui 
                            <a href="{{ route('terms.conditions.id') }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 underline font-semibold">Syarat & Ketentuan</a> 
                            serta <a href="{{ route('privacy.policy.id') }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 underline font-semibold">Kebijakan Privasi</a> 
                            yang berlaku di Bizmark.ID. Data yang dimasukkan dapat digunakan untuk keperluan tindak lanjut layanan.
                        </label>
                    </div>
                    <p class="text-xs text-amber-600 mt-2" x-show="!agreedTerms && coordinates.length >= 3">
                        <i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i>Anda harus menyetujui syarat & ketentuan untuk mengunduh file SHP
                    </p>
                </div>

                <!-- Generate Button -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <button @click="generateShp()" 
                            :disabled="!canGenerate || loading"
                            class="btn-generate w-full py-3 px-6 rounded-xl font-bold flex items-center justify-center gap-2 border"
                            :class="{
                                'is-ready': canGenerate && !loading,
                                'cursor-not-allowed': !canGenerate || loading
                            }"
                            :style="canGenerate && !loading 
                                ? 'background-color: #059669; color: #fff; border-color: transparent;' 
                                : 'background-color: #e5e7eb; color: #9ca3af; border-color: #d1d5db;'">
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            Membuat SHP...
                        </span>
                        <span x-show="!loading">
                            <i class="fas fa-download mr-2"></i>Buat & Unduh SHP
                        </span>
                    </button>

                    <!-- Validation Messages -->
                    <div class="mt-3 space-y-1" role="alert" aria-live="polite">
                        <p class="text-xs text-amber-600" x-show="coordinates.length < 3 && coordinates.length > 0">
                            <i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i>Minimal 3 titik koordinat diperlukan
                        </p>
                        <p class="text-xs text-amber-600" x-show="coordinates.length === 0">
                            <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>Klik ikon poligon di peta untuk mulai menggambar
                        </p>
                        <p class="text-xs text-amber-600" x-show="!form.name && coordinates.length >= 3">
                            <i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i>Nama lahan/proyek wajib diisi
                        </p>
                        <p class="text-xs text-amber-600" x-show="form.name && coordinates.length >= 3 && !form.email">
                            <i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i>Email wajib diisi
                        </p>
                        <p class="text-xs text-amber-600" x-show="form.name && form.email && coordinates.length >= 3 && !form.phone">
                            <i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i>No. WhatsApp/Telepon wajib diisi
                        </p>
                    </div>

                    <!-- Error Message -->
                    <div class="mt-3 p-3 bg-red-50 rounded-lg text-sm text-red-700" x-show="errorMsg" x-transition role="alert">
                        <i class="fas fa-times-circle mr-1" aria-hidden="true"></i><span x-text="errorMsg"></span>
                    </div>

                    <!-- Success Message -->
                    <div class="mt-3 p-3 bg-emerald-50 rounded-lg text-sm text-emerald-700" x-show="successMsg" x-transition role="status">
                        <i class="fas fa-check-circle mr-1" aria-hidden="true"></i><span x-text="successMsg"></span>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl shadow-xl p-6 text-white">
                    <h3 class="font-bold mb-3"><i class="fas fa-info-circle mr-2"></i>Tentang SHP Maker</h3>
                    <ul class="text-sm space-y-2 text-emerald-100">
                        <li><i class="fas fa-check mr-2 text-emerald-300"></i>Proyeksi WGS84 (standar OSS)</li>
                        <li><i class="fas fa-check mr-2 text-emerald-300"></i>Format ESRI Shapefile (.shp/.shx/.dbf/.prj)</li>
                        <li><i class="fas fa-check mr-2 text-emerald-300"></i>File diunduh dalam format ZIP</li>
                        <li><i class="fas fa-check mr-2 text-emerald-300"></i>Gratis tanpa batas penggunaan</li>
                        <li><i class="fas fa-check mr-2 text-emerald-300"></i>Maks. {{ $maxPoints }} titik per poligon</li>
                    </ul>
                </div>

                <!-- CTA -->
                <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
                    <p class="text-sm text-gray-600 mb-3">Butuh bantuan pengurusan OSS / perizinan usaha?</p>
                    <a href="{{ route('consultation.index') }}" 
                       class="inline-block px-6 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-all">
                        <i class="fas fa-headset mr-2"></i>Konsultasi Gratis
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Coordinates Modal -->
    <div x-show="showImportModal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="import-modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="showImportModal = false"
         role="dialog" aria-modal="true" aria-label="Import koordinat">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" @click.outside="showImportModal = false">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">
                    <i class="fas fa-file-import text-emerald-600 mr-2" aria-hidden="true"></i>Import Koordinat
                </h3>
                <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600" aria-label="Tutup modal">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="p-5">
                <p class="text-sm text-gray-600 mb-3">Paste koordinat dalam format CSV (satu baris per titik). Kolom: <code class="bg-gray-100 px-1 rounded">longitude, latitude</code></p>
                <div class="text-xs text-gray-400 mb-2">Contoh:</div>
                <pre class="bg-gray-50 rounded-lg p-2 text-xs text-gray-500 mb-3 font-mono">107.31234567, -6.32456789
107.31345678, -6.32567890
107.31456789, -6.32345678</pre>
                <textarea x-model="importText" rows="8" 
                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-mono"
                          placeholder="Paste koordinat di sini..."></textarea>
                <p class="text-xs text-red-500 mt-1" x-show="importError" x-text="importError"></p>
            </div>
            <div class="p-5 border-t border-gray-100 flex justify-end gap-3">
                <button @click="showImportModal = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 rounded-lg">
                    Batal
                </button>
                <button @click="processImport()" class="px-4 py-2 text-sm bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition">
                    <i class="fas fa-check mr-1" aria-hidden="true"></i>Import
                </button>
            </div>
        </div>
    </div>
</div>

{{-- SEO Content Sections - Rich informational content for search engines --}}
<div class="bg-white py-16">
    <div class="container mx-auto px-4 max-w-5xl">

        {{-- What is SHP --}}
        <section class="mb-14">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Apa Itu File SHP (Shapefile)?</h2>
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed space-y-4">
                <p>
                    <strong>File SHP (Shapefile)</strong> adalah format file geospasial standar yang dikembangkan oleh <strong>ESRI</strong> (Environmental Systems Research Institute). Format ini digunakan secara luas di seluruh dunia untuk menyimpan data lokasi berupa <em>titik, garis, atau poligon</em> beserta atribut datanya.
                </p>
                <p>
                    Dalam konteks <strong>perizinan di Indonesia</strong>, file SHP menjadi salah satu dokumen wajib yang harus diunggah ke sistem <strong>OSS (Online Single Submission)</strong> dan <strong>RBA (Risk-Based Approach)</strong>. File ini berisi data batas lahan atau lokasi usaha yang diajukan untuk mendapatkan izin.
                </p>
                <p>
                    Satu set file Shapefile terdiri dari minimal 4 komponen yang saling terkait:
                </p>
                <ul class="list-disc pl-6 space-y-1">
                    <li><strong>.shp</strong> &mdash; Menyimpan data geometri (bentuk poligon/batas lahan)</li>
                    <li><strong>.shx</strong> &mdash; Index spasial untuk akses data yang lebih cepat</li>
                    <li><strong>.dbf</strong> &mdash; Tabel atribut/metadata (nama lahan, wilayah, keterangan)</li>
                    <li><strong>.prj</strong> &mdash; Informasi proyeksi koordinat (WGS84/EPSG:4326)</li>
                </ul>
            </div>
        </section>

        {{-- Why need SHP for OSS --}}
        <section class="mb-14">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Mengapa Perlu File SHP untuk OSS?</h2>
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed space-y-4">
                <p>
                    Sistem <strong>OSS (Online Single Submission)</strong> pemerintah Indonesia mewajibkan pelaku usaha untuk mengunggah file SHP sebagai bagian dari proses perizinan, khususnya untuk:
                </p>
                <ul class="list-disc pl-6 space-y-1">
                    <li><strong>Izin Lokasi</strong> &mdash; Menunjukkan batas lahan yang diajukan</li>
                    <li><strong>KKPR (Kesesuaian Kegiatan Pemanfaatan Ruang)</strong> &mdash; Verifikasi kesesuaian tata ruang</li>
                    <li><strong>Persetujuan Lingkungan (AMDAL/UKL-UPL)</strong> &mdash; Batas wilayah dampak lingkungan</li>
                    <li><strong>IMB/PBG (Persetujuan Bangunan Gedung)</strong> &mdash; Lokasi bangunan yang dibangun</li>
                </ul>
                <p>
                    File SHP harus menggunakan proyeksi <strong>WGS84 (EPSG:4326)</strong> agar kompatibel dengan sistem OSS. Polygon SHP Maker otomatis membuat file dengan proyeksi yang benar, sehingga Anda tidak perlu melakukan konversi manual.
                </p>
            </div>
        </section>

        {{-- Features --}}
        <section class="mb-14">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Fitur Unggulan Polygon SHP Maker</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-emerald-50 rounded-xl p-5">
                    <div class="text-emerald-600 text-2xl mb-3"><i class="fas fa-map-marked-alt" aria-hidden="true"></i></div>
                    <h3 class="font-bold text-gray-900 mb-2">Peta Interaktif</h3>
                    <p class="text-sm text-gray-600">Gambar poligon langsung di peta dengan teknologi Leaflet.js. Zoom, geser, dan klik untuk menandai batas lahan secara presisi.</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-5">
                    <div class="text-blue-600 text-2xl mb-3"><i class="fas fa-globe-asia" aria-hidden="true"></i></div>
                    <h3 class="font-bold text-gray-900 mb-2">Proyeksi WGS84</h3>
                    <p class="text-sm text-gray-600">File SHP yang dihasilkan otomatis menggunakan proyeksi WGS84 (EPSG:4326), standar yang diterima oleh sistem OSS dan RBA.</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-5">
                    <div class="text-purple-600 text-2xl mb-3"><i class="fas fa-file-archive" aria-hidden="true"></i></div>
                    <h3 class="font-bold text-gray-900 mb-2">Format ZIP Lengkap</h3>
                    <p class="text-sm text-gray-600">Unduh langsung file ZIP berisi .shp, .shx, .dbf, dan .prj &mdash; siap upload ke sistem OSS tanpa perlu extract manual.</p>
                </div>
                <div class="bg-amber-50 rounded-xl p-5">
                    <div class="text-amber-600 text-2xl mb-3"><i class="fas fa-calculator" aria-hidden="true"></i></div>
                    <h3 class="font-bold text-gray-900 mb-2">Hitung Luas Otomatis</h3>
                    <p class="text-sm text-gray-600">Luas area dihitung otomatis dalam m&sup2; dan hektar menggunakan algoritma Web Mercator Shoelace yang presisi.</p>
                </div>
                <div class="bg-rose-50 rounded-xl p-5">
                    <div class="text-rose-600 text-2xl mb-3"><i class="fas fa-expand-arrows-alt" aria-hidden="true"></i></div>
                    <h3 class="font-bold text-gray-900 mb-2">Resize ke Target Luas</h3>
                    <p class="text-sm text-gray-600">Masukkan luas yang diinginkan dan poligon akan otomatis disesuaikan ukurannya dengan presisi tinggi.</p>
                </div>
                <div class="bg-teal-50 rounded-xl p-5">
                    <div class="text-teal-600 text-2xl mb-3"><i class="fas fa-save" aria-hidden="true"></i></div>
                    <h3 class="font-bold text-gray-900 mb-2">Auto-Save di Browser</h3>
                    <p class="text-sm text-gray-600">Data formulir dan poligon tersimpan otomatis di browser. Jika halaman ter-refresh, data dipulihkan secara otomatis.</p>
                </div>
            </div>
        </section>

        {{-- FAQ Section (visible content matching FAQPage schema) --}}
        <section class="mb-14" id="faq">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Pertanyaan yang Sering Diajukan (FAQ)</h2>
            <div class="space-y-4" x-data="{ openFaq: null }">
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition">
                        <h3 class="font-semibold text-gray-900">Apa itu file SHP (Shapefile)?</h3>
                        <i class="fas text-gray-400" :class="openFaq === 1 ? 'fa-chevron-up' : 'fa-chevron-down'" aria-hidden="true"></i>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        File SHP (Shapefile) adalah format file geospasial standar yang dikembangkan oleh ESRI. File ini digunakan untuk menyimpan data lokasi berupa titik, garis, atau poligon beserta atribut datanya. Dalam konteks perizinan di Indonesia, file SHP digunakan untuk mengunggah data batas lahan ke sistem OSS (Online Single Submission) dan RBA (Risk-Based Approach).
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition">
                        <h3 class="font-semibold text-gray-900">Bagaimana cara membuat file SHP untuk OSS?</h3>
                        <i class="fas text-gray-400" :class="openFaq === 2 ? 'fa-chevron-up' : 'fa-chevron-down'" aria-hidden="true"></i>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Anda bisa membuat file SHP untuk OSS secara gratis menggunakan Polygon SHP Maker di Bizmark.ID. Caranya: (1) Pilih lokasi di peta, (2) Gambar poligon batas lahan dengan klik pada peta, (3) Isi metadata lokasi seperti nama lahan dan wilayah, (4) Klik <em>Buat &amp; Unduh SHP</em>. File ZIP berisi .shp, .shx, .dbf, dan .prj akan langsung terunduh &mdash; siap upload ke OSS.
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition">
                        <h3 class="font-semibold text-gray-900">Apakah Polygon SHP Maker ini gratis?</h3>
                        <i class="fas text-gray-400" :class="openFaq === 3 ? 'fa-chevron-up' : 'fa-chevron-down'" aria-hidden="true"></i>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Ya, Polygon SHP Maker di Bizmark.ID sepenuhnya gratis tanpa batas penggunaan. Tidak perlu membuat akun, tidak ada batasan jumlah file yang bisa dibuat, dan tidak ada watermark pada file SHP yang dihasilkan.
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition">
                        <h3 class="font-semibold text-gray-900">Apa saja komponen file SHP yang dihasilkan?</h3>
                        <i class="fas text-gray-400" :class="openFaq === 4 ? 'fa-chevron-up' : 'fa-chevron-down'" aria-hidden="true"></i>
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Polygon SHP Maker menghasilkan file ZIP yang berisi 4 komponen standar ESRI Shapefile: (1) <strong>.shp</strong> &mdash; data geometri poligon, (2) <strong>.shx</strong> &mdash; index spasial, (3) <strong>.dbf</strong> &mdash; atribut/metadata lokasi, dan (4) <strong>.prj</strong> &mdash; informasi proyeksi WGS84. Keempat file ini wajib ada saat upload ke sistem OSS.
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition">
                        <h3 class="font-semibold text-gray-900">Proyeksi apa yang digunakan pada file SHP?</h3>
                        <i class="fas text-gray-400" :class="openFaq === 5 ? 'fa-chevron-up' : 'fa-chevron-down'" aria-hidden="true"></i>
                    </button>
                    <div x-show="openFaq === 5" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        File SHP yang dihasilkan menggunakan proyeksi <strong>WGS84 (EPSG:4326)</strong>, yaitu sistem koordinat standar yang digunakan oleh GPS dan diterima oleh sistem OSS/RBA pemerintah Indonesia. Proyeksi ini memastikan file SHP Anda kompatibel dan dapat langsung diunggah tanpa konversi tambahan.
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 6 ? null : 6" class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition">
                        <h3 class="font-semibold text-gray-900">Apakah perlu install software untuk membuat file SHP?</h3>
                        <i class="fas text-gray-400" :class="openFaq === 6 ? 'fa-chevron-up' : 'fa-chevron-down'" aria-hidden="true"></i>
                    </button>
                    <div x-show="openFaq === 6" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Tidak perlu. Polygon SHP Maker berjalan sepenuhnya di browser (Chrome, Firefox, Safari, Edge) tanpa perlu menginstal software apapun seperti QGIS atau ArcGIS. Cukup buka halaman, gambar poligon, dan unduh file SHP &mdash; semua bisa dilakukan langsung dari browser.
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 7 ? null : 7" class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition">
                        <h3 class="font-semibold text-gray-900">Berapa banyak titik koordinat yang bisa dimasukkan?</h3>
                        <i class="fas text-gray-400" :class="openFaq === 7 ? 'fa-chevron-up' : 'fa-chevron-down'" aria-hidden="true"></i>
                    </button>
                    <div x-show="openFaq === 7" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Polygon SHP Maker mendukung hingga {{ $maxPoints ?? 500 }} titik koordinat per poligon. Minimal 3 titik diperlukan untuk membentuk sebuah poligon. Anda juga bisa mengimpor koordinat dari file CSV atau teks.
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 8 ? null : 8" class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition">
                        <h3 class="font-semibold text-gray-900">Apakah data saya aman di Polygon SHP Maker?</h3>
                        <i class="fas text-gray-400" :class="openFaq === 8 ? 'fa-chevron-up' : 'fa-chevron-down'" aria-hidden="true"></i>
                    </button>
                    <div x-show="openFaq === 8" x-collapse class="px-5 pb-5 text-gray-600 leading-relaxed">
                        Ya. Data formulir dan poligon otomatis tersimpan sementara di browser (localStorage) selama 24 jam untuk kenyamanan Anda. File SHP yang dihasilkan dihapus dari server secara otomatis setelah proses unduh selesai. Kami tidak menyimpan data geospasial Anda secara permanen di server.
                    </div>
                </div>
            </div>
        </section>

        {{-- Internal Links / Related Services --}}
        <section class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Layanan Terkait</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <a href="{{ route('consultation.index') }}" class="block bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 hover:shadow-lg transition group">
                    <div class="text-purple-600 text-2xl mb-3 group-hover:scale-110 transition"><i class="fas fa-headset" aria-hidden="true"></i></div>
                    <h3 class="font-bold text-gray-900 mb-2">Konsultasi Perizinan</h3>
                    <p class="text-sm text-gray-600">Butuh bantuan pengurusan OSS, KKPR, atau perizinan usaha lainnya? Konsultasi gratis dengan tim ahli kami.</p>
                </a>
                <a href="{{ route('services.index.id') }}" class="block bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-6 hover:shadow-lg transition group">
                    <div class="text-emerald-600 text-2xl mb-3 group-hover:scale-110 transition"><i class="fas fa-briefcase" aria-hidden="true"></i></div>
                    <h3 class="font-bold text-gray-900 mb-2">Layanan Perizinan Usaha</h3>
                    <p class="text-sm text-gray-600">Lihat daftar lengkap layanan pengurusan perizinan usaha, mulai dari OSS, NIB, SIUP, hingga izin khusus lainnya.</p>
                </a>
                <a href="{{ route('blog.index.id') }}" class="block bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 hover:shadow-lg transition group">
                    <div class="text-blue-600 text-2xl mb-3 group-hover:scale-110 transition"><i class="fas fa-newspaper" aria-hidden="true"></i></div>
                    <h3 class="font-bold text-gray-900 mb-2">Artikel & Panduan</h3>
                    <p class="text-sm text-gray-600">Baca artikel terbaru seputar perizinan usaha, OSS, dan tips pengurusan dokumen bisnis di Indonesia.</p>
                </a>
            </div>
        </section>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha384-cxOPjt7s7Iz04uaHJceBmS+qpjv2JkIHNVcuOrM+YHwZOmJGBXI00mdUXEq65HTH" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js" integrity="sha384-JP5UPxIO2Tm2o79Fb0tGYMa44jkWar53aBoCbd8ah0+LcCDoohTIYr+zIXyfGIJN" crossorigin="anonymous"></script>
<script>
// Register the Alpine component before Alpine starts
document.addEventListener('alpine:init', () => {
    Alpine.data('polygonShpApp', () => ({
        // State
        step: 1,
        coordinates: [],
        areaM2: 0,
        areaHa: 0,
        perimeterM: 0,
        loading: false,
        errorMsg: '',
        successMsg: '',
        showCoords: true,
        map: null,
        drawnItems: null,
        drawControl: null,
        calculatingStats: false,
        isDrawing: false,

        // Toast notification
        toastMsg: '',
        toastType: 'info',
        _toastTimeout: null,

        // Import modal
        showImportModal: false,
        importText: '',
        importError: '',

        // Resize area
        targetArea: null,
        resizingArea: false,

        // RTRW (Spatial Zoning) integration
        rtrwEnabled: true,
        rtrwLoading: false,
        rtrwError: '',
        rtrwZones: [],

        rtrwAvailable: false,
        rtrwDisclaimer: '',
        rtrwSource: '',
        rtrwProvince: '',
        rtrwShowLegend: false,

        rtrwZonaColors: {
            // Kawasan Lindung
            'Kawasan Hutan Lindung': '#004D00',
            'Hutan Lindung': '#004D00',
            'Taman Nasional': '#006400',
            'Kawasan Pelestarian Alam': '#00A000',
            'Cagar Alam': '#38A800',
            'Suaka Margasatwa': '#38A800',
            'Taman Hutan Raya': '#2E8B57',
            'Taman Wisata Alam': '#3CB371',
            'Kawasan Perlindungan Setempat': '#73D2DE',
            'Sempadan Sungai': '#73B2FF',
            'Sempadan Pantai': '#87CEEB',
            'Sempadan Danau': '#5DADE2',
            'Ruang Terbuka Hijau': '#56B356',
            'RTH': '#56B356',
            'Kawasan Rawan Bencana': '#FF4444',
            'Badan Air': '#0070FF',
            'Kawasan Resapan Air': '#48C9B0',
            'Kawasan Lindung Geologi': '#A0522D',
            // Kawasan Budi Daya
            'Kawasan Perumahan': '#FFFF00',
            'Kawasan Permukiman': '#FFFF00',
            'Kawasan Permukiman Perkotaan': '#FFD700',
            'Kawasan Permukiman Perdesaan': '#F0E68C',
            'Kawasan Perdagangan dan Jasa': '#FF69B4',
            'Kawasan Perkantoran': '#FFA500',
            'Kawasan Peruntukan Industri': '#8B008B',
            'Kawasan Industri': '#8B008B',
            'Kawasan Tanaman Pangan': '#D7C29E',
            'Kawasan Pertanian': '#D2B48C',
            'Kawasan Perkebunan': '#8FBC8F',
            'Kawasan Hutan Produksi': '#228B22',
            'Kawasan Hutan Produksi Terbatas': '#66AA66',
            'Kawasan Hutan Produksi Konversi': '#9ACD32',
            'Kawasan Hutan Rakyat': '#6B8E23',
            'Kawasan Perikanan': '#4169E1',
            'Kawasan Pertambangan': '#A0522D',
            'Kawasan Pariwisata': '#00CED1',
            'Kawasan Pertahanan dan Keamanan': '#808000',
            'Kawasan Pendidikan': '#9370DB',
            'Kawasan Kesehatan': '#DC143C',
            'Kawasan Peribadatan': '#DAA520',
            'Kawasan Transportasi': '#A9A9A9',
            'Badan Jalan': '#C0C0C0',
            'Kawasan Campuran': '#E8A87C',
        },

        // Address cascading
        loadingAddr: false,
        addrError: '',
        _lastAddrAction: null,
        provinsiList: [],
        kabkotaList: [],
        kecamatanList: [],
        kelurahanList: [],
        selectedProvinsi: '',
        selectedKabkota: '',
        selectedKecamatan: '',
        selectedKelurahan: '',
        locationMarker: null,

        // Form
        form: {
            name: '',
            kelurahan: '',
            kecamatan: '',
            kabkota: '',
            provinsi: '',
            keterangan: '',
            company_name: '',
            contact_person: '',
            email: '',
            phone: '',
        },
        agreedTerms: false,

        // Auth state
        authClient: @json($authClient ?? null),
        emailRegistered: false,
        emailChecking: false,
        _emailCheckTimeout: null,

        // Auto-save
        autoSaveMsg: '',
        restoredMsg: '',
        hasSavedData: false,
        _saveTimeout: null,
        _STORAGE_KEY: 'shp_maker_data',
        _STORAGE_TTL: 24 * 60 * 60 * 1000, // 24 hours

        // Province center coordinates for map navigation
        provinsiCoords: {
            '11': [-4.695135, 96.7493993],  // Aceh
            '12': [2.1153547, 99.5450974],   // Sumatera Utara
            '13': [-0.7399397, 100.8000051], // Sumatera Barat
            '14': [0.2933469, 101.7068294],  // Riau
            '15': [-1.6101229, 103.6131203], // Jambi
            '16': [-3.3194374, 103.914399],  // Sumatera Selatan
            '17': [-3.5778471, 102.3463875], // Bengkulu
            '18': [-4.5585849, 105.4068079], // Lampung
            '19': [-2.7410513, 105.6256749], // Kepulauan Bangka Belitung
            '21': [-0.1529166, 104.5808822], // Kepulauan Riau
            '31': [-6.211544, 106.845172],   // DKI Jakarta
            '32': [-6.9174639, 107.6191228], // Jawa Barat
            '33': [-7.150975, 110.1402594],  // Jawa Tengah
            '34': [-7.8753849, 110.4262088], // DI Yogyakarta
            '35': [-7.5360639, 112.2384017], // Jawa Timur
            '36': [-6.4058172, 106.0640179], // Banten
            '51': [-8.4095178, 115.1889],    // Bali
            '52': [-8.6529334, 117.3616476], // Nusa Tenggara Barat
            '53': [-8.6573819, 121.0793705], // Nusa Tenggara Timur
            '61': [-0.2787808, 111.4752851], // Kalimantan Barat
            '62': [-1.6814878, 116.0341286], // Kalimantan Tengah
            '63': [-3.0926415, 115.2837585], // Kalimantan Selatan
            '64': [1.6406296, 116.419389],   // Kalimantan Timur
            '65': [3.0730929, 116.0413889],  // Kalimantan Utara
            '71': [0.6246932, 123.9750018],  // Sulawesi Utara
            '72': [-1.4300254, 121.4456179], // Sulawesi Tengah
            '73': [-3.6687994, 119.9740534], // Sulawesi Selatan
            '74': [-4.14491, 122.174605],    // Sulawesi Tenggara
            '75': [0.5435442, 123.0567693],  // Gorontalo
            '76': [-2.8441371, 119.2320784], // Sulawesi Barat
            '81': [-3.2384616, 130.1452734], // Maluku
            '82': [1.5709993, 127.8087693],  // Maluku Utara
            '91': [-4.269928, 138.0803529],  // Papua
            '92': [-0.8616892, 134.0564773], // Papua Barat
            '93': [-3.5, 137.5],             // Papua Selatan
            '94': [-3.0, 139.5],             // Papua Tengah
            '95': [-2.5, 140.5],             // Papua Pegunungan
            '96': [-1.0, 134.5],             // Papua Barat Daya
        },

        get canGenerate() {
            return this.coordinates.length >= 3 
                && this.form.name.trim().length > 0 
                && this.form.email.trim().length > 0
                && this.form.phone.trim().length > 0
                && this.agreedTerms
                && !this.loading
                && !(this.emailRegistered && !this.authClient);
        },

        init() {
            // Auto-fill form from logged-in client profile
            if (this.authClient) {
                this.form.email = this.authClient.email || '';
                this.form.phone = this.authClient.phone || '';
                this.form.company_name = this.authClient.company_name || '';
                this.form.contact_person = this.authClient.contact_person || '';
            }

            this.$nextTick(async () => {
                this.initMap();
                await this.loadProvinsi();
                this.restoreFromStorage();
                this.setupAutoSave();
            });
        },

        // --- localStorage persistence ---
        _getSavedData() {
            try {
                const raw = localStorage.getItem(this._STORAGE_KEY);
                if (!raw) return null;
                const data = JSON.parse(raw);
                if (Date.now() - data.timestamp > this._STORAGE_TTL) {
                    localStorage.removeItem(this._STORAGE_KEY);
                    return null;
                }
                return data;
            } catch { return null; }
        },

        saveToStorage() {
            clearTimeout(this._saveTimeout);
            this._saveTimeout = setTimeout(() => {
                const data = {
                    timestamp: Date.now(),
                    form: { ...this.form },
                    agreedTerms: this.agreedTerms,
                    selectedProvinsi: this.selectedProvinsi,
                    selectedKabkota: this.selectedKabkota,
                    selectedKecamatan: this.selectedKecamatan,
                    selectedKelurahan: this.selectedKelurahan,
                    coordinates: this.coordinates,
                    areaM2: this.areaM2,
                    areaHa: this.areaHa,
                    perimeterM: this.perimeterM,
                    step: this.step,
                };
                try {
                    localStorage.setItem(this._STORAGE_KEY, JSON.stringify(data));
                    this.hasSavedData = true;
                    this.autoSaveMsg = 'Tersimpan';
                    setTimeout(() => { this.autoSaveMsg = ''; }, 2000);
                } catch { /* quota exceeded, ignore */ }
            }, 500);
        },

        async restoreFromStorage() {
            const data = this._getSavedData();
            if (!data) { this.hasSavedData = false; return; }

            this.hasSavedData = true;

            // Restore form text fields
            if (data.form) {
                this.form.name = data.form.name || '';
                this.form.keterangan = data.form.keterangan || '';
                this.form.company_name = data.form.company_name || '';
                this.form.contact_person = data.form.contact_person || '';
                this.form.email = data.form.email || '';
                this.form.phone = data.form.phone || '';
            }
            if (data.agreedTerms !== undefined) {
                this.agreedTerms = data.agreedTerms;
            }

            // Restore address cascade with data reloading
            if (data.selectedProvinsi) {
                this.selectedProvinsi = data.selectedProvinsi;
                const prov = this.provinsiList.find(p => p.id === data.selectedProvinsi);
                this.form.provinsi = prov ? prov.nama : data.form?.provinsi || '';

                // Reload kabkota list
                try {
                    const res = await fetch(`https://ibnux.github.io/data-indonesia/kabupaten/${data.selectedProvinsi}.json`);
                    this.kabkotaList = await res.json();
                } catch { /* ignore */ }

                if (data.selectedKabkota) {
                    this.selectedKabkota = data.selectedKabkota;
                    const kab = this.kabkotaList.find(k => k.id === data.selectedKabkota);
                    this.form.kabkota = kab ? kab.nama : data.form?.kabkota || '';

                    // Reload kecamatan list
                    try {
                        const res = await fetch(`https://ibnux.github.io/data-indonesia/kecamatan/${data.selectedKabkota}.json`);
                        this.kecamatanList = await res.json();
                    } catch { /* ignore */ }

                    if (data.selectedKecamatan) {
                        this.selectedKecamatan = data.selectedKecamatan;
                        const kec = this.kecamatanList.find(k => k.id === data.selectedKecamatan);
                        this.form.kecamatan = kec ? kec.nama : data.form?.kecamatan || '';

                        // Reload kelurahan list
                        try {
                            const res = await fetch(`https://ibnux.github.io/data-indonesia/kelurahan/${data.selectedKecamatan}.json`);
                            this.kelurahanList = await res.json();
                        } catch { /* ignore */ }

                        if (data.selectedKelurahan) {
                            this.selectedKelurahan = data.selectedKelurahan;
                            const kel = this.kelurahanList.find(k => k.id === data.selectedKelurahan);
                            this.form.kelurahan = kel ? kel.nama : data.form?.kelurahan || '';
                        }
                    }
                }
            }

            // Restore coordinates and stats
            if (data.coordinates && data.coordinates.length >= 3) {
                this.coordinates = data.coordinates;
                this.areaM2 = data.areaM2 || 0;
                this.areaHa = data.areaHa || 0;
                this.perimeterM = data.perimeterM || 0;
                this.step = data.step || 2;

                // Redraw polygon on map
                this.$nextTick(() => { this.redrawPolygonFromCoords(); });
            }

            // Show restored notification with details
            const parts = [];
            if (data.form?.name) parts.push('nama lahan');
            if (data.selectedProvinsi) parts.push('data wilayah');
            if (data.coordinates?.length >= 3) parts.push(data.coordinates.length + ' titik koordinat');
            if (parts.length) {
                this.restoredMsg = 'Data sebelumnya berhasil dipulihkan: ' + parts.join(', ');
                setTimeout(() => { this.restoredMsg = ''; }, 8000);
            }
        },

        redrawPolygonFromCoords() {
            if (!this.map || !this.drawnItems || this.coordinates.length < 3) return;
            this.drawnItems.clearLayers();
            const latlngs = this.coordinates.map(c => [c[1], c[0]]);
            const polygon = L.polygon(latlngs, {
                color: '#059669',
                weight: 3,
                fillColor: '#10b981',
                fillOpacity: 0.2,
            });
            this.drawnItems.addLayer(polygon);
            this.map.fitBounds(polygon.getBounds(), { padding: [50, 50] });
        },

        setupAutoSave() {
            // Watch form fields
            this.$watch('form', () => { this.saveToStorage(); }, { deep: true });
            this.$watch('selectedProvinsi', () => { this.saveToStorage(); });
            this.$watch('selectedKabkota', () => { this.saveToStorage(); });
            this.$watch('selectedKecamatan', () => { this.saveToStorage(); });
            this.$watch('selectedKelurahan', () => { this.saveToStorage(); });
            this.$watch('coordinates', () => { this.saveToStorage(); });
            this.$watch('step', () => { this.saveToStorage(); });
            this.$watch('agreedTerms', () => { this.saveToStorage(); });
        },

        clearSavedData() {
            localStorage.removeItem(this._STORAGE_KEY);
            this.hasSavedData = false;
            this.autoSaveMsg = '';
            this.restoredMsg = '';
        },

        async loadProvinsi() {
            try {
                this.loadingAddr = true;
                this.addrError = '';
                const res = await fetch('https://ibnux.github.io/data-indonesia/provinsi.json');
                if (!res.ok) throw new Error('HTTP ' + res.status);
                this.provinsiList = await res.json();
            } catch (e) {
                console.error('Failed to load provinsi:', e);
                this.addrError = 'Gagal memuat data provinsi. Periksa koneksi internet.';
                this._lastAddrAction = () => this.loadProvinsi();
            } finally {
                this.loadingAddr = false;
            }
        },

        async onProvinsiChange() {
            // Reset downstream
            this.kabkotaList = [];
            this.kecamatanList = [];
            this.kelurahanList = [];
            this.selectedKabkota = '';
            this.selectedKecamatan = '';
            this.selectedKelurahan = '';
            this.form.kabkota = '';
            this.form.kecamatan = '';
            this.form.kelurahan = '';

            if (!this.selectedProvinsi) {
                this.form.provinsi = '';
                return;
            }

            const prov = this.provinsiList.find(p => p.id === this.selectedProvinsi);
            this.form.provinsi = prov ? prov.nama : '';

            // Navigate map to province
            const coords = this.provinsiCoords[this.selectedProvinsi];
            if (coords && this.map) {
                this.map.flyTo(coords, 8, { duration: 1.5 });
            }

            // Re-query zone if polygon exists
            if (this.coordinates.length >= 3) {
                this.queryRtrwZona();
            }

            try {
                this.loadingAddr = true;
                this.addrError = '';
                const res = await fetch(`https://ibnux.github.io/data-indonesia/kabupaten/${this.selectedProvinsi}.json`);
                if (!res.ok) throw new Error('HTTP ' + res.status);
                this.kabkotaList = await res.json();
            } catch (e) {
                console.error('Failed to load kabupaten:', e);
                this.addrError = 'Gagal memuat data kabupaten/kota.';
                this._lastAddrAction = () => this.onProvinsiChange();
            } finally {
                this.loadingAddr = false;
            }
        },

        async onKabkotaChange() {
            this.kecamatanList = [];
            this.kelurahanList = [];
            this.selectedKecamatan = '';
            this.selectedKelurahan = '';
            this.form.kecamatan = '';
            this.form.kelurahan = '';

            if (!this.selectedKabkota) {
                this.form.kabkota = '';
                return;
            }

            const kab = this.kabkotaList.find(k => k.id === this.selectedKabkota);
            this.form.kabkota = kab ? kab.nama : '';

            // Zoom in closer for kabupaten level
            if (this.map) {
                this.map.setZoom(10, { animate: true });
            }

            try {
                this.loadingAddr = true;
                this.addrError = '';
                const res = await fetch(`https://ibnux.github.io/data-indonesia/kecamatan/${this.selectedKabkota}.json`);
                if (!res.ok) throw new Error('HTTP ' + res.status);
                this.kecamatanList = await res.json();
            } catch (e) {
                console.error('Failed to load kecamatan:', e);
                this.addrError = 'Gagal memuat data kecamatan.';
                this._lastAddrAction = () => this.onKabkotaChange();
            } finally {
                this.loadingAddr = false;
            }
        },

        async onKecamatanChange() {
            this.kelurahanList = [];
            this.selectedKelurahan = '';
            this.form.kelurahan = '';

            if (!this.selectedKecamatan) {
                this.form.kecamatan = '';
                return;
            }

            const kec = this.kecamatanList.find(k => k.id === this.selectedKecamatan);
            this.form.kecamatan = kec ? kec.nama : '';

            // Zoom in for kecamatan level
            if (this.map) {
                this.map.setZoom(13, { animate: true });
            }

            try {
                this.loadingAddr = true;
                this.addrError = '';
                const res = await fetch(`https://ibnux.github.io/data-indonesia/kelurahan/${this.selectedKecamatan}.json`);
                if (!res.ok) throw new Error('HTTP ' + res.status);
                this.kelurahanList = await res.json();
            } catch (e) {
                console.error('Failed to load kelurahan:', e);
                this.addrError = 'Gagal memuat data kelurahan/desa.';
                this._lastAddrAction = () => this.onKecamatanChange();
            } finally {
                this.loadingAddr = false;
            }
        },

        onKelurahanChange() {
            if (!this.selectedKelurahan) {
                this.form.kelurahan = '';
                return;
            }

            const kel = this.kelurahanList.find(k => k.id === this.selectedKelurahan);
            this.form.kelurahan = kel ? kel.nama : '';

            // Navigate to kelurahan coordinates (ibnux API provides lat/lng for kelurahan)
            if (kel && this.map) {
                const lat = parseFloat(kel.latitude);
                const lng = parseFloat(kel.longitude);
                if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                    this.map.flyTo([lat, lng], 15, { duration: 1.5 });

                    // Show location marker
                    if (this.locationMarker) {
                        this.map.removeLayer(this.locationMarker);
                    }
                    this.locationMarker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            html: '<i class="fas fa-map-pin text-2xl text-red-500"></i>',
                            iconSize: [24, 24],
                            iconAnchor: [12, 24],
                            className: 'bg-transparent border-0',
                        }),
                    }).addTo(this.map)
                      .bindPopup(`<b>${kel.nama}</b><br>${this.form.kecamatan}, ${this.form.kabkota}`)
                      .openPopup();
                }
            }
        },

        initMap() {
            // Center on Indonesia
            this.map = L.map('map', {
                center: [-2.5, 118],
                zoom: 5,
                zoomControl: true,
            });

            // Tile layers
            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            });

            const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: '&copy; Esri',
                maxZoom: 19,
            });

            osmLayer.addTo(this.map);

            L.control.layers({
                'Peta': osmLayer,
                'Satelit': satelliteLayer,
            }, {}, { position: 'topright' }).addTo(this.map);

            // Draw layer
            this.drawnItems = new L.FeatureGroup();
            this.map.addLayer(this.drawnItems);

            this.drawControl = new L.Control.Draw({
                position: 'topleft',
                draw: {
                    polygon: {
                        allowIntersection: false,
                        showArea: true,
                        shapeOptions: {
                            color: '#059669',
                            weight: 3,
                            fillColor: '#10b981',
                            fillOpacity: 0.2,
                        },
                    },
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false,
                },
                edit: {
                    featureGroup: this.drawnItems,
                    remove: true,
                    edit: true,
                },
            });
            this.map.addControl(this.drawControl);

            // Events
            this.map.on(L.Draw.Event.CREATED, (e) => {
                // Remove existing polygon (single polygon mode)
                this.drawnItems.clearLayers();
                this.drawnItems.addLayer(e.layer);
                this.extractCoordinates(e.layer);
                this.step = 2;
                this.isDrawing = false;
            });

            this.map.on(L.Draw.Event.EDITED, () => {
                // Extract from drawnItems directly — e.layers can miss midpoint-added vertices
                this.drawnItems.eachLayer((layer) => {
                    if (layer.getLatLngs) {
                        this.extractCoordinates(layer);
                    }
                });
            });

            this.map.on(L.Draw.Event.DELETED, () => {
                this.coordinates = [];
                this.areaM2 = 0;
                this.areaHa = 0;
                this.perimeterM = 0;
                this.step = 1;
                this.successMsg = '';
            });

            // Track drawing state for keyboard hint
            this.map.on('draw:drawstart', () => { this.isDrawing = true; });
            this.map.on('draw:drawstop', () => { this.isDrawing = false; });
        },

        extractCoordinates(layer) {
            // getLatLngs() can return nested arrays — drill down to LatLng objects
            let latlngs = layer.getLatLngs();
            while (latlngs.length && Array.isArray(latlngs[0])) {
                latlngs = latlngs[0];
            }
            // Convert to [lng, lat] format for shapefile (GeoJSON convention)
            this.coordinates = latlngs.map(ll => [ll.lng, ll.lat]);
            this.calculateStats();
            // Auto-query RTRW zona if province is selected
            this.queryRtrwZona();
        },

        calculateStats() {
            if (this.coordinates.length < 3) return;
            this.calculatingStats = true;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                console.warn('CSRF token not found, using client-side calculation');
                this.areaM2 = this.calcAreaClient();
                this.areaHa = this.areaM2 / 10000;
                this.perimeterM = this.calcPerimeterClient();
                this.calculatingStats = false;
                return;
            }

            fetch('/api/shapefile/calculate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ coordinates: this.coordinates }),
            })
            .then(r => r.json())
            .then(data => {
                this.areaM2 = data.area_m2 || 0;
                this.areaHa = data.area_ha || 0;
                this.perimeterM = data.perimeter_m || 0;
            })
            .catch(() => {
                // Fallback: client-side rough calculation
                this.areaM2 = this.calcAreaClient();
                this.areaHa = this.areaM2 / 10000;
                this.perimeterM = this.calcPerimeterClient();
            })
            .finally(() => {
                this.calculatingStats = false;
            });
        },

        calcAreaClient() {
            // Web Mercator EPSG:3857 + Shoelace (matches OSS Mercator Auxiliary Sphere)
            const coords = this.coordinates;
            const n = coords.length;
            if (n < 3) return 0;

            const pts = this._projectToWebMercator(coords);
            let area = 0;
            for (let i = 0; i < n; i++) {
                const j = (i + 1) % n;
                area += pts[i].x * pts[j].y;
                area -= pts[j].x * pts[i].y;
            }
            return Math.abs(area) / 2;
        },

        _projectToWebMercator(coords) {
            const a = 6378137;
            const toRad = Math.PI / 180;
            return coords.map(c => ({
                x: a * c[0] * toRad,
                y: a * Math.log(Math.tan(Math.PI / 4 + c[1] * toRad / 2)),
            }));
        },

        calcPerimeterClient() {
            const coords = this.coordinates;
            let perim = 0;
            for (let i = 0; i < coords.length; i++) {
                const j = (i + 1) % coords.length;
                const dLat = (coords[j][1] - coords[i][1]) * Math.PI / 180;
                const dLon = (coords[j][0] - coords[i][0]) * Math.PI / 180;
                const a = Math.sin(dLat/2)**2 + Math.cos(coords[i][1]*Math.PI/180) * Math.cos(coords[j][1]*Math.PI/180) * Math.sin(dLon/2)**2;
                perim += 6378137 * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            }
            return perim;
        },

        // --- RTRW (Spatial Zoning) Integration ---

        async queryRtrwZona() {
            if (!this.rtrwEnabled || this.coordinates.length < 3 || !this.selectedProvinsi) return;

            // Calculate centroid of polygon
            const centroid = this._calcCentroid(this.coordinates);
            if (!centroid) return;

            this.rtrwLoading = true;
            this.rtrwError = '';
            this.rtrwZones = [];

            // Map Kemendagri province ID to 2-digit BPS code
            const provCode = this._provIdToBpsCode(this.selectedProvinsi);
            if (!provCode) {
                this.rtrwLoading = false;
                this.rtrwAvailable = false;
                this.rtrwError = 'Kode provinsi tidak dikenali untuk RTRW.';
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const params = new URLSearchParams({
                    lat: centroid.lat.toFixed(6),
                    lng: centroid.lng.toFixed(6),
                    province_code: provCode,
                });

                const response = await fetch(`/api/rtrw/zona?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    this.rtrwAvailable = data.available ?? false;
                    this.rtrwError = data.error || 'Gagal mengambil data zona RTRW.';
                    return;
                }

                this.rtrwZones = data.zones || [];
                this.rtrwAvailable = data.available ?? true;
                this.rtrwDisclaimer = data.disclaimer || '';
                this.rtrwSource = data.source || '';
                this.rtrwProvince = data.province || '';

                if (this.rtrwZones.length > 0) {
                    this.showToast(`Ditemukan ${this.rtrwZones.length} zona RTRW`, 'success');
                } else {
                    this.showToast('Tidak ada data zona RTRW di lokasi ini', 'info');
                }
            } catch (e) {
                this.rtrwError = 'Gagal menghubungi server RTRW.';
            } finally {
                this.rtrwLoading = false;
            }
        },

        _getZonaColor(zonaName) {
            if (!zonaName) return '#94A3B8';
            if (this.rtrwZonaColors[zonaName]) return this.rtrwZonaColors[zonaName];
            // Fuzzy match: try partial match against keys
            const lower = zonaName.toLowerCase();
            for (const [key, color] of Object.entries(this.rtrwZonaColors)) {
                if (lower.includes(key.toLowerCase()) || key.toLowerCase().includes(lower)) return color;
            }
            return '#94A3B8';
        },

        _hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1,3), 16);
            const g = parseInt(hex.slice(3,5), 16);
            const b = parseInt(hex.slice(5,7), 16);
            return `rgba(${r},${g},${b},${alpha})`;
        },

        _zonaTextColor(hex) {
            const r = parseInt(hex.slice(1,3), 16);
            const g = parseInt(hex.slice(3,5), 16);
            const b = parseInt(hex.slice(5,7), 16);
            const lum = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
            return lum > 0.6 ? '#1E293B' : '#FFFFFF';
        },

        _calcCentroid(coords) {
            if (!coords || coords.length === 0) return null;
            let sumLng = 0, sumLat = 0;
            for (const c of coords) {
                sumLng += c[0];
                sumLat += c[1];
            }
            return { lng: sumLng / coords.length, lat: sumLat / coords.length };
        },

        _provIdToBpsCode(provId) {
            // Kemendagri API returns province IDs like "11", "12", etc. which match BPS codes
            // But some APIs return longer IDs - extract first 2 digits
            const id = String(provId);
            return id.length >= 2 ? id.substring(0, 2) : null;
        },

        resetPolygon() {
            this.drawnItems.clearLayers();
            this.coordinates = [];
            this.areaM2 = 0;
            this.areaHa = 0;
            this.perimeterM = 0;
            this.step = 1;
            this.errorMsg = '';
            this.successMsg = '';
            // Reset RTRW
            this.rtrwZones = [];
            this.rtrwError = '';
            this.rtrwAvailable = false;
            this.rtrwSource = '';
            this.rtrwProvince = '';
        },

        confirmResetPolygon() {
            if (this.coordinates.length === 0) return;
            if (confirm('Apakah Anda yakin ingin menghapus poligon? Tindakan ini tidak dapat dibatalkan.')) {
                this.resetPolygon();
                this.showToast('Poligon berhasil dihapus', 'info');
            }
        },

        // --- Toast notification ---
        showToast(msg, type = 'info') {
            clearTimeout(this._toastTimeout);
            this.toastMsg = msg;
            this.toastType = type;
            this._toastTimeout = setTimeout(() => { this.toastMsg = ''; }, 3000);
        },

        // --- Copy coordinates ---
        async copyCoordinates() {
            if (this.coordinates.length === 0) return;
            const csv = this.coordinates.map((c, i) => `${c[0].toFixed(8)}, ${c[1].toFixed(8)}`).join('\n');
            const header = 'longitude, latitude\n';
            try {
                await navigator.clipboard.writeText(header + csv);
                this.showToast(`${this.coordinates.length} koordinat disalin ke clipboard`, 'success');
            } catch {
                // Fallback for older browsers
                const ta = document.createElement('textarea');
                ta.value = header + csv;
                ta.style.position = 'fixed'; ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select(); document.execCommand('copy');
                ta.remove();
                this.showToast(`${this.coordinates.length} koordinat disalin ke clipboard`, 'success');
            }
        },

        // --- Resize polygon to target area ---
        async resizeToTargetArea() {
            if (!this.targetArea || this.targetArea <= 0 || this.coordinates.length < 3) return;
            this.resizingArea = true;

            try {
                const target = parseFloat(this.targetArea);
                const a = 6378137;
                const toRad = Math.PI / 180;
                const toDeg = 180 / Math.PI;

                // Work with full-precision coordinates during iteration
                let workingCoords = this.coordinates.map(c => [c[0], c[1]]);

                // Iterative refinement: scale, measure, correct — max 20 passes
                for (let iter = 0; iter < 20; iter++) {
                    const currentArea = this._calcAreaFromCoords(workingCoords);
                    if (currentArea <= 0) {
                        this.showToast('Luas poligon saat ini tidak valid.', 'error');
                        return;
                    }

                    const diff = Math.abs(currentArea - target);
                    // Stop if within 1e-6 m² (micrometer precision)
                    if (diff < 1e-6) break;

                    const scaleFactor = Math.sqrt(target / currentArea);

                    // Calculate centroid
                    const n = workingCoords.length;
                    let cx = 0, cy = 0;
                    for (let i = 0; i < n; i++) {
                        cx += workingCoords[i][0];
                        cy += workingCoords[i][1];
                    }
                    cx /= n;
                    cy /= n;

                    // Project centroid to Web Mercator
                    const cxM = a * cx * toRad;
                    const cyM = a * Math.log(Math.tan(Math.PI / 4 + cy * toRad / 2));

                    // Scale each vertex in projected space (full precision, no rounding)
                    workingCoords = workingCoords.map(c => {
                        const mx = a * c[0] * toRad;
                        const my = a * Math.log(Math.tan(Math.PI / 4 + c[1] * toRad / 2));
                        const sx = cxM + (mx - cxM) * scaleFactor;
                        const sy = cyM + (my - cyM) * scaleFactor;
                        const lng = sx * toDeg / a;
                        const lat = (2 * Math.atan(Math.exp(sy / a)) - Math.PI / 2) * toDeg;
                        return [lng, lat];
                    });
                }

                // Store full float64 precision — NO toFixed rounding
                // Display uses toFixed(8) in the template, but stored data keeps full precision
                this.coordinates = workingCoords;

                this.redrawPolygonFromCoords();

                // Use client-side area (same engine that converged) for immediate precision
                const finalArea = this._calcAreaFromCoords(this.coordinates);
                this.areaM2 = finalArea;
                this.areaHa = finalArea / 10000;
                this.perimeterM = this.calcPerimeterClient();
                this.calculatingStats = false;

                const diff = Math.abs(finalArea - target);
                if (diff < 0.01) {
                    this.showToast(`Luas disesuaikan tepat ke ${this.formatNumber(target)} m²`, 'success');
                } else {
                    this.showToast(`Luas disesuaikan ke ${this.formatNumber(this.areaM2)} m² (selisih ${this.formatNumber(diff)} m²)`, 'success');
                }
            } catch (e) {
                console.error('Resize error:', e);
                this.showToast('Gagal menyesuaikan luas poligon.', 'error');
            } finally {
                this.resizingArea = false;
            }
        },

        // Calculate area from arbitrary coords array (no side effects)
        _calcAreaFromCoords(coords) {
            const n = coords.length;
            if (n < 3) return 0;
            const pts = this._projectToWebMercator(coords);
            let area = 0;
            for (let i = 0; i < n; i++) {
                const j = (i + 1) % n;
                area += pts[i].x * pts[j].y;
                area -= pts[j].x * pts[i].y;
            }
            return Math.abs(area) / 2;
        },

        // --- Import coordinates ---
        importCoordinates() {
            this.importText = '';
            this.importError = '';
            this.showImportModal = true;
        },

        processImport() {
            this.importError = '';
            const lines = this.importText.trim().split('\n').filter(l => l.trim());
            if (lines.length < 3) {
                this.importError = 'Minimal 3 baris koordinat diperlukan.';
                return;
            }

            const coords = [];
            for (let i = 0; i < lines.length; i++) {
                let line = lines[i].trim();
                // Skip header lines
                if (/^(lon|lat|#|no)/i.test(line)) continue;
                // Support comma, semicolon, tab, or space separation
                const parts = line.split(/[,;\t]+/).map(p => p.trim()).filter(Boolean);
                if (parts.length < 2) {
                    this.importError = `Baris ${i + 1}: format tidak valid. Gunakan "longitude, latitude".`;
                    return;
                }
                const lng = parseFloat(parts[0]);
                const lat = parseFloat(parts[1]);
                if (isNaN(lng) || isNaN(lat)) {
                    this.importError = `Baris ${i + 1}: nilai bukan angka.`;
                    return;
                }
                if (lng < -180 || lng > 180 || lat < -90 || lat > 90) {
                    this.importError = `Baris ${i + 1}: koordinat di luar batas (-180..180, -90..90).`;
                    return;
                }
                coords.push([lng, lat]);
            }

            if (coords.length < 3) {
                this.importError = 'Minimal 3 koordinat valid diperlukan (baris header diabaikan).';
                return;
            }

            const maxPoints = {{ $maxPoints }};
            if (coords.length > maxPoints) {
                this.importError = `Maksimal ${maxPoints} titik diperbolehkan. Anda memasukkan ${coords.length} titik.`;
                return;
            }

            this.coordinates = coords;
            this.step = 2;
            this.redrawPolygonFromCoords();
            this.calculateStats();
            this.showImportModal = false;
            this.showToast(`${coords.length} koordinat berhasil diimport`, 'success');
        },

        // --- Address error retry ---
        async retryLoadAddress() {
            this.addrError = '';
            if (this._lastAddrAction) {
                await this._lastAddrAction();
            } else {
                await this.loadProvinsi();
            }
        },

        async checkEmail() {
            const email = this.form.email.trim();
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                this.emailRegistered = false;
                return;
            }
            // Skip check if already logged in as this email
            if (this.authClient && this.authClient.email === email) {
                this.emailRegistered = false;
                return;
            }
            this.emailChecking = true;
            try {
                const res = await fetch('/api/shapefile/check-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email }),
                });
                if (res.ok) {
                    const data = await res.json();
                    this.emailRegistered = data.registered;
                }
            } catch (e) {
                // Silently fail — don't block user
            } finally {
                this.emailChecking = false;
            }
        },

        _debouncedCheckEmail() {
            clearTimeout(this._emailCheckTimeout);
            this._emailCheckTimeout = setTimeout(() => this.checkEmail(), 500);
        },

        async generateShp() {
            if (!this.canGenerate) return;
            this.loading = true;
            this.errorMsg = '';
            this.successMsg = '';
            this.step = 3;

            const payload = {
                coordinates: this.coordinates,
                name: this.form.name.trim(),
                company_name: this.form.company_name.trim() || null,
                contact_person: this.form.contact_person.trim() || null,
                email: this.form.email.trim(),
                phone: this.form.phone.trim(),
                agreed_terms: true,
                metadata: {
                    kelurahan: this.form.kelurahan.trim(),
                    kecamatan: this.form.kecamatan.trim(),
                    kabkota: this.form.kabkota.trim(),
                    provinsi: this.form.provinsi.trim(),
                    keterangan: this.form.keterangan.trim(),
                },
                area_m2: this.areaM2,
                area_ha: this.areaHa,
                perimeter_m: this.perimeterM,
                // RTRW enrichment
                rtrw_zona: this.rtrwZones.length > 0 ? this.rtrwZones.map(z => z.zona).filter(Boolean).join('; ') : null,
                rtrw_perda: this.rtrwZones.length > 0 ? this.rtrwZones.map(z => z.no_perda).filter(Boolean).join('; ') : null,
                rtrw_remark: this.rtrwZones.length > 0 ? this.rtrwZones.map(z => z.remark).filter(Boolean).join('; ') : null,
            };

            try {
                const response = await fetch('/api/shapefile/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/zip',
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    const err = await response.json().catch(() => ({}));
                    throw new Error(err.error || 'Terjadi kesalahan saat membuat file SHP');
                }

                // Download the ZIP
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = this.form.name.trim().replace(/\s+/g, '_') + '_shp.zip';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();

                this.successMsg = 'File SHP berhasil dibuat dan diunduh!';
                this.clearSavedData();
            } catch (e) {
                this.errorMsg = e.message;
                this.step = 2;
            } finally {
                this.loading = false;
            }
        },

        formatNumber(num, decimals = 2) {
            if (!num) return '0';
            return Number(num).toLocaleString('id-ID', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            });
        },
    }));
});
</script>
@if (!isset($__alpineLoaded))
<script defer src="https://unpkg.com/alpinejs@3.14.9/dist/cdn.min.js" integrity="sha384-9Ax3MmS9AClxJyd5/zafcXXjxmwFhZCdsT6HJoJjarvCaAkJlk5QDzjLJm+Wdx5F" crossorigin="anonymous"></script>
@endif
@endpush
