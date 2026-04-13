<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <meta name="description" content="Hasil analisis perizinan untuk {{ $inquiry->company_name }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hasil Analisis Perizinan - {{ $inquiry->inquiry_number }} | Bizmark.ID</title>

    <!-- Open Graph -->
    <meta property="og:title" content="Hasil Analisis Perizinan — {{ $inquiry->company_name }}">
    <meta property="og:description" content="Lihat rekomendasi izin usaha hasil analisis AI untuk {{ $inquiry->company_name }}.">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Bizmark.ID">
    <meta property="og:locale" content="id_ID">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
                },
                colors: {
                    primary: {
                        50: '#EFF6FF',
                        100: '#DBEAFE',
                        200: '#BFDBFE',
                        300: '#93C5FD',
                        400: '#60A5FA',
                        500: '#0A66C2',
                        600: '#004182',
                        700: '#003161',
                        800: '#002445',
                        900: '#001A33',
                    },
                    accent: {
                        400: '#FB923C',
                        500: '#F97316',
                        600: '#EA580C',
                    }
                },
                boxShadow: {
                    'soft': '0 2px 15px rgba(0, 0, 0, 0.05)',
                    'soft-lg': '0 10px 40px rgba(0, 0, 0, 0.08)',
                    'soft-xl': '0 20px 50px rgba(0, 0, 0, 0.12)',
                }
            }
        }
    }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh2JtHeS4ZL8SaJIs54IVqVdPXgeSrxlL1YgM7GkL4Z3+5eZ5Pg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --color-primary: #0A66C2;
            --color-primary-dark: #004182;
            --color-primary-light: #378FE9;
            --color-secondary: #00A0DC;
            --color-accent: #F97316;
            --color-success: #10B981;
        }

        body { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }

        .bg-mesh {
            background-image:
                radial-gradient(at 20% 20%, rgba(10, 102, 194, 0.06) 0%, transparent 50%),
                radial-gradient(at 80% 80%, rgba(249, 115, 22, 0.04) 0%, transparent 50%);
        }

        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); }

        .animate-fade-in { opacity: 0; transform: translateY(20px); animation: fadeIn 0.6s ease forwards; }
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        @keyframes slideUp { from { transform: translateY(10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .stagger-item { opacity: 0; animation: slideUp 0.5s ease forwards; }
        .stagger-item:nth-child(1) { animation-delay: 0.1s; }
        .stagger-item:nth-child(2) { animation-delay: 0.2s; }
        .stagger-item:nth-child(3) { animation-delay: 0.3s; }
        .stagger-item:nth-child(4) { animation-delay: 0.4s; }
        .stagger-item:nth-child(5) { animation-delay: 0.5s; }
        .stagger-item:nth-child(6) { animation-delay: 0.6s; }
        .stagger-item:nth-child(7) { animation-delay: 0.7s; }
        .stagger-item:nth-child(8) { animation-delay: 0.8s; }

        .btn-lift { transition: all 0.3s ease; }
        .btn-lift:hover { transform: translateY(-1px); box-shadow: 0 10px 25px rgba(10, 102, 194, 0.25); }

        .fab-pulse { animation: fabPulse 2s ease-in-out infinite; }
        @keyframes fabPulse {
            0%, 100% { box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3); }
            50% { box-shadow: 0 8px 25px rgba(37, 211, 102, 0.5); transform: scale(1.05); }
        }

        @media print {
            .no-print { display: none !important; }
            .stagger-item { opacity: 1; animation: none; }
            body { background: white; }
            nav, footer { display: none !important; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body class="font-sans bg-white text-gray-900 min-h-screen flex flex-col">

    @php
        $contact = config('landing_metrics.contact');
    @endphp

    <!-- Sticky Navbar -->
    @php
        $currentLocale = app()->getLocale();
        $landingUrl = $currentLocale === 'en' ? route('landing.en') : route('landing.id');
        $blogUrl = $currentLocale === 'en' ? route('blog.index.en') : route('blog.index.id');
    @endphp
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm no-print" role="navigation" aria-label="{{ $currentLocale === 'id' ? 'Navigasi utama' : 'Main navigation' }}">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ $landingUrl }}" class="text-xl font-bold text-gray-900">
                    <i class="fas fa-certificate mr-2 text-primary-500"></i>Bizmark.ID
                </a>
                <div class="hidden md:flex items-center space-x-2 lg:space-x-3">
                    <a href="{{ $landingUrl }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition">{{ __('landing.nav.home') }}</a>
                    <a href="{{ $landingUrl }}#services" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition">{{ __('landing.nav.services') }}</a>
                    <a href="{{ $landingUrl }}#process" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition">{{ __('landing.nav.process') }}</a>
                    <a href="{{ $landingUrl }}#about" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition">{{ __('landing.nav.about') }}</a>
                    <a href="{{ $blogUrl }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition">{{ __('landing.nav.blog') }}</a>
                    <!-- Locale Switcher -->
                    <div class="relative inline-block text-left" id="localeSwitcher">
                        <button type="button" onclick="document.getElementById('localeDropdown').classList.toggle('hidden')" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border transition-colors text-gray-600 border-gray-200 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-primary-500" aria-expanded="false" aria-haspopup="true" aria-label="{{ $currentLocale === 'id' ? 'Ganti bahasa' : 'Change language' }}">
                            <span class="text-base">{{ $currentLocale === 'en' ? '🇬🇧' : '🇮🇩' }}</span>
                            <span class="hidden sm:inline">{{ $currentLocale === 'en' ? 'EN' : 'ID' }}</span>
                            <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="localeDropdown" class="hidden absolute right-0 z-50 w-48 mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg ring-1 ring-black/5" role="menu">
                            <div class="py-1">
                                <a href="{{ route('locale.set', 'id') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ $currentLocale === 'id' ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}" role="menuitem">
                                    <span class="text-lg">🇮🇩</span><span>Bahasa Indonesia</span>
                                    @if($currentLocale === 'id')<i class="fas fa-check ml-auto text-primary-500 text-xs"></i>@endif
                                </a>
                                <a href="{{ route('locale.set', 'en') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ $currentLocale === 'en' ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}" role="menuitem">
                                    <span class="text-lg">🇬🇧</span><span>English</span>
                                    @if($currentLocale === 'en')<i class="fas fa-check ml-auto text-primary-500 text-xs"></i>@endif
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="{{ $landingUrl }}#contact" class="ml-2 inline-flex items-center px-4 py-2 bg-primary-500 text-white text-sm font-semibold rounded-lg hover:bg-primary-600 transition btn-lift">
                        {{ __('landing.nav.get_started') }}
                    </a>
                </div>
                <button class="md:hidden p-2.5 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-primary-500 transition min-w-[44px] min-h-[44px] flex items-center justify-center no-print" onclick="document.getElementById('mobileMenuPanel').classList.toggle('translate-x-full')" aria-label="{{ $currentLocale === 'id' ? 'Buka menu navigasi' : 'Open navigation menu' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </div>
    </nav>
    <!-- Mobile Menu Panel -->
    <div id="mobileMenuPanel" class="fixed inset-y-0 right-0 z-[60] w-80 max-w-[85vw] bg-white shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out no-print">
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <span class="text-lg font-bold text-gray-900"><i class="fas fa-certificate mr-2 text-primary-500"></i>Menu</span>
            <button onclick="document.getElementById('mobileMenuPanel').classList.add('translate-x-full')" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="{{ $currentLocale === 'id' ? 'Tutup menu' : 'Close menu' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="flex flex-col h-[calc(100%-65px)] overflow-y-auto">
            <div class="p-4 space-y-1 flex-1">
                <a href="{{ $landingUrl }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-500 rounded-lg transition min-h-[44px]"><i class="fas fa-home w-5 text-center text-gray-400"></i>{{ __('landing.nav.home') }}</a>
                <a href="{{ $landingUrl }}#services" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-500 rounded-lg transition min-h-[44px]"><i class="fas fa-briefcase w-5 text-center text-gray-400"></i>{{ __('landing.nav.services') }}</a>
                <a href="{{ $landingUrl }}#process" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-500 rounded-lg transition min-h-[44px]"><i class="fas fa-tasks w-5 text-center text-gray-400"></i>{{ __('landing.nav.process') }}</a>
                <a href="{{ $landingUrl }}#about" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-500 rounded-lg transition min-h-[44px]"><i class="fas fa-info-circle w-5 text-center text-gray-400"></i>{{ __('landing.nav.about') }}</a>
                <a href="{{ $blogUrl }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-500 rounded-lg transition min-h-[44px]"><i class="fas fa-newspaper w-5 text-center text-gray-400"></i>{{ __('landing.nav.blog') }}</a>
                <a href="{{ route('landing.service-inquiry.create') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-primary-500 bg-primary-50 rounded-lg min-h-[44px]"><i class="fas fa-robot w-5 text-center"></i>Analisis AI</a>
            </div>
            <div class="px-4 py-3 border-t border-gray-100">
                <p class="text-gray-400 text-xs uppercase tracking-wider font-semibold mb-3">{{ __('landing.footer.language') }}</p>
                <div class="flex gap-2">
                    <a href="{{ route('locale.set', 'id') }}" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition min-h-[44px] {{ $currentLocale === 'id' ? 'bg-primary-50 font-semibold text-primary-600' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        <span>🇮🇩</span><span class="text-sm">Indonesia</span>
                    </a>
                    <a href="{{ route('locale.set', 'en') }}" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition min-h-[44px] {{ $currentLocale === 'en' ? 'bg-primary-50 font-semibold text-primary-600' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        <span>🇬🇧</span><span class="text-sm">English</span>
                    </a>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100">
                <a href="{{ $landingUrl }}#contact" onclick="document.getElementById('mobileMenuPanel').classList.add('translate-x-full')" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-primary-500 text-white text-sm font-semibold rounded-lg hover:bg-primary-600 transition min-h-[44px]"><i class="fas fa-paper-plane"></i>{{ __('landing.nav.get_started') }}</a>
            </div>
        </div>
    </div>
    <div id="mobileMenuOverlay" class="fixed inset-0 bg-black/30 z-[55] hidden no-print" onclick="document.getElementById('mobileMenuPanel').classList.add('translate-x-full'); this.classList.add('hidden')"></div>
    <script>document.addEventListener('click',function(e){var s=document.getElementById('localeSwitcher');var d=document.getElementById('localeDropdown');if(s&&d&&!s.contains(e.target)){d.classList.add('hidden')}});document.getElementById('mobileMenuPanel').addEventListener('transitionend',function(){this.classList.contains('translate-x-full')?document.getElementById('mobileMenuOverlay').classList.add('hidden'):document.getElementById('mobileMenuOverlay').classList.remove('hidden')});document.querySelectorAll('#mobileMenuPanel a').forEach(function(a){a.addEventListener('click',function(){document.getElementById('mobileMenuPanel').classList.add('translate-x-full')})});</script>

    <!-- Hero Header -->
    <section class="pt-24 pb-8 sm:pt-28 sm:pb-10 bg-gradient-to-b from-[#FDFBF8] via-[#FDFBF8] to-[#F5F3F8] bg-mesh relative overflow-hidden">
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-primary-100/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-accent-400/10 rounded-full blur-3xl"></div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-5xl mx-auto text-center animate-fade-in">
                <!-- Breadcrumb -->
                <nav class="flex items-center justify-center gap-2 text-sm text-gray-400 mb-5" aria-label="Breadcrumb">
                    <a href="{{ route('landing.id') }}" class="hover:text-primary-500 transition">Beranda</a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <a href="{{ route('landing.service-inquiry.create') }}" class="hover:text-primary-500 transition">Analisis Perizinan</a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <span class="text-primary-500 font-medium">Hasil</span>
                </nav>

                @php
                    $analysis = $inquiry->ai_analysis ?? [];
                    $isStillProcessing = in_array($inquiry->status, ['processing', 'new']) && empty($analysis);
                    $hasError = $inquiry->status === 'error' && empty($analysis);
                    $hasAnalysis = !empty($analysis);
                @endphp

                @if($hasAnalysis)
                    <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">
                        <i class="fas fa-check-circle"></i> Analisis Selesai
                    </div>
                @elseif($isStillProcessing)
                    <div class="inline-flex items-center gap-2 bg-primary-50 border border-primary-200 text-primary-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">
                        <i class="fas fa-spinner fa-spin"></i> Sedang Diproses
                    </div>
                @elseif($hasError)
                    <div class="inline-flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">
                        <i class="fas fa-exclamation-triangle"></i> Gagal Diproses
                    </div>
                @endif

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 mb-2 tracking-tight" style="line-height:1.15">
                    Hasil Analisis
                    <span style="background: linear-gradient(135deg, #0A66C2, #00A0DC); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Perizinan</span>
                </h1>
                <p class="text-base text-gray-500 mb-1">
                    Untuk: <strong class="text-gray-700">{{ $inquiry->company_name }}</strong>
                </p>
                <p class="text-xs text-gray-400">
                    No. Inquiry: {{ $inquiry->inquiry_number }} &middot; {{ $inquiry->created_at->format('d M Y, H:i') }} WIB
                </p>

                @if($hasAnalysis)
                <div class="mt-4 no-print">
                    <button onclick="window.print()" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-primary-500 transition px-3 py-1.5 rounded-lg hover:bg-primary-50">
                        <i class="fas fa-print"></i> Cetak Hasil
                    </button>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8 sm:py-10 bg-gradient-to-b from-[#F5F3F8] to-white bg-mesh flex-1">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $permits = $analysis['recommended_permits'] ?? [];
            $totalCost = $analysis['total_estimated_cost'] ?? [];
            $riskFactors = $analysis['risk_factors'] ?? [];
            $nextSteps = $analysis['next_steps'] ?? [];
            $limitations = $analysis['limitations'] ?? '';
            $requiredDocuments = $analysis['required_documents'] ?? [];
            $riskClassification = $analysis['risk_classification'] ?? null;
            $kbliSuggestion = $analysis['kbli_suggestion'] ?? null;

            // Smart cost formatter — adaptive unit (rb/jt/M)
            $formatCost = function($value) {
                $value = (float) ($value ?? 0);
                if ($value >= 1000000000) {
                    return 'Rp ' . number_format($value / 1000000000, 1, ',', '.') . 'M';
                } elseif ($value >= 1000000) {
                    $formatted = number_format($value / 1000000, 1, ',', '.');
                    // Remove trailing ,0
                    $formatted = rtrim(rtrim($formatted, '0'), ',');
                    return 'Rp ' . $formatted . 'jt';
                } elseif ($value >= 1000) {
                    return 'Rp ' . number_format($value / 1000, 0, ',', '.') . 'rb';
                } elseif ($value > 0) {
                    return 'Rp ' . number_format($value, 0, ',', '.');
                }
                return 'Rp 0';
            };

            $formatRange = function($min, $max) use ($formatCost) {
                $min = (float) ($min ?? 0);
                $max = (float) ($max ?? 0);
                if ($min == 0 && $max == 0) return 'Rp 0';
                if ($min == $max) return $formatCost($min);
                return $formatCost($min) . ' - ' . $formatCost($max);
            };
        @endphp

        {{-- Processing State --}}
        @if($isStillProcessing)
        <div class="max-w-2xl mx-auto text-center" x-data="{ dots: '.' }" x-init="setInterval(() => dots = dots.length >= 3 ? '.' : dots + '.', 600)">
            <div class="bg-white rounded-2xl shadow-soft-lg border border-gray-100 p-8 sm:p-12 card-hover">
                <div id="poll-spinner" class="w-16 h-16 mx-auto mb-6 relative">
                    <svg class="animate-spin w-full h-full text-primary-500" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">AI Masih Menganalisis<span x-text="dots"></span></h2>
                <p class="text-gray-500 mb-6">Analisis sedang diproses, halaman ini akan otomatis ter-refresh ketika selesai.</p>
                <div class="flex flex-wrap items-center justify-center gap-6 text-sm text-gray-400">
                    <span class="flex items-center gap-1.5"><i class="far fa-clock"></i> Estimasi 10-30 detik</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-envelope"></i> Hasil juga dikirim via email</span>
                </div>

                {{-- Timeout message - shown after multiple reload cycles --}}
                <div id="poll-timeout-msg" style="display:none" class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-amber-800 font-semibold mb-2"><i class="fas fa-clock text-amber-500 mr-1"></i> Analisis membutuhkan waktu lebih lama dari biasanya</p>
                    <p class="text-amber-700 text-sm mb-3">Hasil akan dikirim ke email <strong>{{ $inquiry->email }}</strong> segera setelah selesai.</p>
                    <div class="flex flex-col sm:flex-row gap-2 justify-center">
                        <a href="{{ $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855' }}?text={{ rawurlencode('Halo, analisis saya memakan waktu lama. Nomor inquiry: ' . $inquiry->inquiry_number) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp"></i> Hubungi via WhatsApp
                        </a>
                        <button onclick="sessionStorage.removeItem('poll_reloads_{{ $inquiry->inquiry_number }}'); window.location.reload();"
                                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-redo"></i> Coba Lagi
                        </button>
                    </div>
                </div>
            </div>
            <script>
                (function() {
                    var inquiryNum = '{{ $inquiry->inquiry_number }}';
                    var attempts = 0, maxAttempts = 60;
                    var reloadKey = 'poll_reloads_' + inquiryNum;
                    var totalReloads = parseInt(sessionStorage.getItem(reloadKey) || '0');

                    // If we've already reloaded 3+ times and still processing, show timeout message
                    if (totalReloads >= 3) {
                        document.getElementById('poll-timeout-msg').style.display = 'block';
                        document.getElementById('poll-spinner').style.display = 'none';
                        return;
                    }

                    function pollPage() {
                        attempts++;
                        if (attempts > maxAttempts) {
                            // Track reload count to prevent infinite loop
                            sessionStorage.setItem(reloadKey, String(totalReloads + 1));
                            window.location.reload();
                            return;
                        }
                        fetch('/konsultasi-gratis/api/status/' + inquiryNum)
                            .then(function(r) { return r.json(); })
                            .then(function(d) {
                                if (d.status === 'completed' || d.status === 'analyzed' || d.status === 'error') {
                                    sessionStorage.removeItem(reloadKey);
                                    window.location.reload();
                                }
                                else { setTimeout(pollPage, 3000); }
                            })
                            .catch(function() { setTimeout(pollPage, 5000); });
                    }
                    setTimeout(pollPage, 3000);
                })();
            </script>
        </div>

        @elseif($hasError)
        {{-- Error State --}}
        <div class="max-w-2xl mx-auto text-center">
            <div class="bg-white rounded-2xl shadow-soft-lg border border-red-100 p-8 sm:p-12 card-hover">
                <div class="w-16 h-16 mx-auto mb-6 bg-red-50 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Analisis Gagal Diproses</h2>
                <p class="text-gray-500 mb-6">Maaf, terjadi kendala saat menganalisis data Anda. Tim kami akan menghubungi Anda melalui email <strong class="text-gray-700">{{ $inquiry->email }}</strong> dengan hasil analisis manual.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('landing.service-inquiry.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-500 text-white font-semibold rounded-xl hover:bg-primary-600 transition-all btn-lift">
                        <i class="fas fa-redo"></i> Coba Lagi
                    </a>
                    <a href="{{ $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855' }}?text={{ rawurlencode('Halo, analisis saya gagal. Nomor inquiry: ' . $inquiry->inquiry_number) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                        <i class="fab fa-whatsapp text-green-500"></i> Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>

        @else
        {{-- Normal Analysis Results --}}
        <div class="max-w-5xl mx-auto space-y-5">

            <!-- Summary Card -->
            <div class="rounded-2xl shadow-soft-lg p-5 sm:p-6 text-white stagger-item" style="background: linear-gradient(135deg, #0A66C2, #004182);">
                <h2 class="text-lg font-bold mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-bar"></i> Ringkasan Analisis
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-white/15 backdrop-blur rounded-xl p-3">
                        <div class="text-xs opacity-80 mb-1">Total Estimasi Biaya</div>
                        <div class="text-xl font-bold">
                            @if(isset($totalCost['grand_total']['min']) && isset($totalCost['grand_total']['max']))
                                {{ $formatRange($totalCost['grand_total']['min'], $totalCost['grand_total']['max']) }}
                            @elseif(isset($totalCost['min']) && isset($totalCost['max']))
                                {{ $formatRange($totalCost['min'], $totalCost['max']) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="bg-white/15 backdrop-blur rounded-xl p-3">
                        <div class="text-xs opacity-80 mb-1">Timeline Estimasi</div>
                        <div class="text-xl font-bold">{{ $analysis['total_estimated_timeline'] ?? '-' }}</div>
                    </div>
                    <div class="bg-white/15 backdrop-blur rounded-xl p-3">
                        <div class="text-xs opacity-80 mb-1">Kompleksitas</div>
                        <div class="text-xl font-bold">{{ $analysis['complexity_score'] ?? '0' }}/10</div>
                    </div>
                    <div class="bg-white/15 backdrop-blur rounded-xl p-3">
                        <div class="text-xs opacity-80 mb-1">Klasifikasi Risiko</div>
                        <div class="text-lg font-bold capitalize">
                            @php
                                $riskLabel = match($riskClassification) {
                                    'rendah' => 'Rendah',
                                    'menengah_rendah' => 'Menengah Rendah',
                                    'menengah_tinggi' => 'Menengah Tinggi',
                                    'tinggi' => 'Tinggi',
                                    default => $riskClassification ?? '-',
                                };
                            @endphp
                            {{ $riskLabel }}
                        </div>
                    </div>
                </div>

                {{-- Cost breakdown --}}
                @if(isset($totalCost['government_fees']) && isset($totalCost['consultant_fees']))
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                    <div class="bg-white/10 rounded-xl px-3 py-2 flex items-center justify-between">
                        <span class="text-white/80 text-xs">Biaya Pemerintah</span>
                        <span class="font-semibold">
                            {{ $formatRange($totalCost['government_fees']['min'] ?? 0, $totalCost['government_fees']['max'] ?? 0) }}
                        </span>
                    </div>
                    <div class="bg-white/10 rounded-xl px-3 py-2 flex items-center justify-between">
                        <span class="text-white/80 text-xs">Jasa Konsultan BizMark</span>
                        <span class="font-semibold">
                            {{ $formatRange($totalCost['consultant_fees']['min'] ?? 0, $totalCost['consultant_fees']['max'] ?? 0) }}
                        </span>
                    </div>
                </div>
                @endif
            </div>

            {{-- KBLI Suggestion --}}
            @if($kbliSuggestion && !empty($kbliSuggestion['code']))
            <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-5 sm:p-6 stagger-item card-hover">
                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-tag text-primary-500"></i> Kode KBLI yang Disarankan
                </h2>
                <div class="bg-primary-50 border border-primary-100 rounded-xl p-4 flex items-center gap-4">
                    <div class="text-2xl font-mono font-bold text-primary-500">{{ $kbliSuggestion['code'] }}</div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 text-sm">{{ $kbliSuggestion['description'] ?? '' }}</div>
                        @if(!empty($kbliSuggestion['confidence']))
                        <div class="text-xs mt-1 text-gray-500">
                            Tingkat keyakinan:
                            <span class="font-semibold
                                @if($kbliSuggestion['confidence'] === 'high') text-green-600
                                @elseif($kbliSuggestion['confidence'] === 'medium') text-amber-600
                                @else text-gray-600
                                @endif">
                                {{ match($kbliSuggestion['confidence']) {
                                    'high' => 'Tinggi',
                                    'medium' => 'Sedang',
                                    'low' => 'Rendah',
                                    default => $kbliSuggestion['confidence']
                                } }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">* Verifikasi kode KBLI dengan konsultan untuk memastikan kesesuaian dengan aktivitas usaha Anda.</p>
            </div>
            @endif

            {{-- Permit Flow Visualization --}}
            @php
                $hasFlow = false;
                foreach ($permits as $p) {
                    if (!empty($p['prerequisites']) || !empty($p['triggers_next'])) { $hasFlow = true; break; }
                }
                $categoryLabels = [
                    'foundational' => ['Dasar', 'bg-blue-100 text-blue-700', 'fas fa-layer-group'],
                    'environmental' => ['Lingkungan', 'bg-emerald-100 text-emerald-700', 'fas fa-leaf'],
                    'technical' => ['Teknikal', 'bg-purple-100 text-purple-700', 'fas fa-cogs'],
                    'operational' => ['Operasional', 'bg-amber-100 text-amber-700', 'fas fa-play-circle'],
                    'sectoral' => ['Sektoral', 'bg-pink-100 text-pink-700', 'fas fa-industry'],
                ];
            @endphp

            @if($hasFlow && count($permits) > 1)
            <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-5 sm:p-6 stagger-item card-hover">
                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-project-diagram text-primary-500"></i> Alur Pengurusan Izin
                </h2>
                <p class="text-xs text-gray-500 mb-4">Urutan yang direkomendasikan berdasarkan dependensi antar izin.</p>
                <div class="relative">
                    {{-- Vertical connector line --}}
                    <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-primary-100 hidden sm:block"></div>
                    <div class="space-y-3">
                        @foreach($permits as $fIdx => $fp)
                            <div class="flex items-start gap-3 relative">
                                {{-- Step circle --}}
                                <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                    @if(($fp['priority'] ?? '') === 'critical') bg-red-500 text-white
                                    @elseif(($fp['priority'] ?? '') === 'high') bg-orange-500 text-white
                                    @else bg-primary-500 text-white
                                    @endif">
                                    {{ $fIdx + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-gray-900 text-sm truncate">{{ $fp['name'] ?? 'Izin' }}</span>
                                        @php $cat = $fp['category'] ?? ''; @endphp
                                        @if($cat && isset($categoryLabels[$cat]))
                                            <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded {{ $categoryLabels[$cat][1] }}">
                                                <i class="{{ $categoryLabels[$cat][2] }} mr-0.5"></i>{{ $categoryLabels[$cat][0] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap gap-1.5 mt-1 text-[11px]">
                                        @if(!empty($fp['prerequisites']))
                                            <span class="text-gray-400"><i class="fas fa-arrow-left mr-0.5"></i>Butuh: {{ implode(', ', array_map(fn($p) => is_string($p) ? $p : '', $fp['prerequisites'])) }}</span>
                                        @endif
                                        @if(!empty($fp['triggers_next']))
                                            <span class="text-primary-400"><i class="fas fa-arrow-right mr-0.5"></i>Membuka: {{ implode(', ', array_map(fn($p) => is_string($p) ? $p : '', $fp['triggers_next'])) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <span class="text-[11px] text-gray-400"><i class="far fa-clock mr-0.5"></i>{{ $fp['estimated_timeline'] ?? '-' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Recommended Permits -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-5 sm:p-6 stagger-item card-hover">
                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-primary-500"></i> Izin yang Direkomendasikan
                    @if(count($permits) > 0)
                    <span class="text-xs font-normal text-gray-400 ml-1">({{ count($permits) }} izin)</span>
                    @endif
                </h2>

                @if(count($permits) > 0)
                    <div class="space-y-3">
                        @foreach($permits as $index => $permit)
                            <div class="border-l-4
                                @if(($permit['priority'] ?? '') === 'critical') border-red-500 bg-red-50/60
                                @elseif(($permit['priority'] ?? '') === 'high') border-orange-500 bg-orange-50/60
                                @else border-primary-500 bg-primary-50/40
                                @endif
                                rounded-xl p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                            <span class="font-bold text-gray-900 text-sm">{{ $index + 1 }}. {{ $permit['name'] ?? 'Izin' }}</span>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                                @if(($permit['priority'] ?? '') === 'critical') bg-red-500 text-white
                                                @elseif(($permit['priority'] ?? '') === 'high') bg-orange-500 text-white
                                                @else bg-primary-500 text-white
                                                @endif">
                                                {{ strtoupper($permit['priority'] ?? 'medium') }}
                                            </span>
                                            @if(!empty($permit['code']))
                                            <span class="px-2 py-0.5 text-xs font-mono bg-gray-200 text-gray-700 rounded">{{ $permit['code'] }}</span>
                                            @endif
                                            @php $cat = $permit['category'] ?? ''; @endphp
                                            @if($cat && isset($categoryLabels[$cat]))
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded {{ $categoryLabels[$cat][1] }}">
                                                <i class="{{ $categoryLabels[$cat][2] }} mr-0.5"></i>{{ $categoryLabels[$cat][0] }}
                                            </span>
                                            @endif
                                        </div>
                                        <p class="text-gray-700 text-sm mb-2 leading-relaxed">{{ $permit['description'] ?? '' }}</p>
                                        <div class="flex flex-wrap gap-3 text-xs">
                                            <div class="flex items-center gap-1.5 text-gray-500">
                                                <i class="far fa-clock"></i>
                                                <span>{{ $permit['estimated_timeline'] ?? '-' }}</span>
                                            </div>
                                            @if(isset($permit['government_fee']))
                                            <div class="flex items-center gap-1.5 text-primary-500" title="Biaya Pemerintah">
                                                <i class="fas fa-building"></i>
                                                <span>Gov: {{ $formatRange($permit['government_fee']['min'] ?? 0, $permit['government_fee']['max'] ?? 0) }}</span>
                                            </div>
                                            @endif
                                            @if(isset($permit['consultant_fee']))
                                            <div class="flex items-center gap-1.5 text-green-600" title="Biaya Konsultan">
                                                <i class="fas fa-user-tie"></i>
                                                <span>Konsultan: {{ $formatRange($permit['consultant_fee']['min'] ?? 0, $permit['consultant_fee']['max'] ?? 0) }}</span>
                                            </div>
                                            @endif
                                            <div class="flex items-center gap-1.5 text-gray-800 font-semibold">
                                                <i class="fas fa-coins"></i>
                                                <span>Total: {{ $permit['total_cost_range'] ?? '-' }}</span>
                                            </div>
                                        </div>

                                        {{-- Dependency info --}}
                                        @if(!empty($permit['prerequisites']) || !empty($permit['triggers_next']))
                                        <div class="mt-2.5 pt-2.5 border-t border-gray-200/60 flex flex-wrap gap-3 text-xs">
                                            @if(!empty($permit['prerequisites']))
                                            <div class="flex items-start gap-1.5 text-amber-600">
                                                <i class="fas fa-lock mt-0.5"></i>
                                                <span><strong>Prasyarat:</strong> {{ implode(', ', array_filter(array_map(fn($p) => is_string($p) ? $p : '', $permit['prerequisites']))) }}</span>
                                            </div>
                                            @endif
                                            @if(!empty($permit['triggers_next']))
                                            <div class="flex items-start gap-1.5 text-emerald-600">
                                                <i class="fas fa-unlock mt-0.5"></i>
                                                <span><strong>Membuka:</strong> {{ implode(', ', array_filter(array_map(fn($p) => is_string($p) ? $p : '', $permit['triggers_next']))) }}</span>
                                            </div>
                                            @endif
                                        </div>
                                        @endif

                                        @if(!empty($permit['issuing_authority']) || !empty($permit['legal_basis']))
                                        <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-500">
                                            @if(!empty($permit['issuing_authority']))
                                            <span class="bg-gray-100 px-2 py-0.5 rounded"><i class="fas fa-building-columns mr-1 text-gray-400"></i>{{ $permit['issuing_authority'] }}</span>
                                            @endif
                                            @if(!empty($permit['legal_basis']))
                                            <span class="bg-gray-100 px-2 py-0.5 rounded"><i class="fas fa-gavel mr-1 text-gray-400"></i>{{ $permit['legal_basis'] }}</span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">Tidak ada rekomendasi izin tersedia.</p>
                @endif
            </div>

            <!-- Risk Factors -->
            @if(count($riskFactors) > 0)
            <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-5 sm:p-6 stagger-item card-hover">
                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-500"></i> Faktor Risiko & Perhatian
                </h2>
                <ul class="space-y-2">
                    @foreach($riskFactors as $risk)
                        <li class="flex items-start gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-bold mt-0.5">!</span>
                            <span class="text-gray-700 text-sm leading-relaxed">{{ $risk }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Required Documents -->
            @if(count($requiredDocuments) > 0)
            <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-5 sm:p-6 stagger-item card-hover">
                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-file-alt text-primary-500"></i> Dokumen yang Perlu Disiapkan
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($requiredDocuments as $index => $doc)
                        <div class="flex items-start gap-2.5 bg-gray-50 rounded-lg px-3 py-2">
                            <span class="flex-shrink-0 w-5 h-5 rounded bg-primary-100 text-primary-500 flex items-center justify-center text-xs font-bold mt-0.5">{{ $index + 1 }}</span>
                            <span class="text-gray-700 text-sm">{{ $doc }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Next Steps -->
            @if(count($nextSteps) > 0)
            <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-5 sm:p-6 stagger-item card-hover">
                <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-arrow-right text-primary-500"></i> Langkah Selanjutnya
                </h2>
                <ol class="space-y-2">
                    @foreach($nextSteps as $index => $step)
                        <li class="flex items-start gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-primary-500 text-white flex items-center justify-center text-xs font-bold mt-0.5">{{ $index + 1 }}</span>
                            <span class="text-gray-700 text-sm leading-relaxed">{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
            @endif

            <!-- Limitations Notice -->
            @if($limitations)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 sm:p-6 stagger-item">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-amber-500 flex-shrink-0 mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="font-bold text-amber-900 mb-2 text-sm">Catatan Penting</h3>
                        <p class="text-amber-800 text-sm leading-relaxed">{{ $limitations }}</p>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <div class="flex items-center gap-2"><i class="fas fa-check-circle text-amber-400"></i><span class="text-amber-800">Dokumen checklist detail</span></div>
                            <div class="flex items-center gap-2"><i class="fas fa-check-circle text-amber-400"></i><span class="text-amber-800">Timeline breakdown</span></div>
                            <div class="flex items-center gap-2"><i class="fas fa-check-circle text-amber-400"></i><span class="text-amber-800">Pendampingan konsultan</span></div>
                            <div class="flex items-center gap-2"><i class="fas fa-check-circle text-amber-400"></i><span class="text-amber-800">Portal monitoring real-time</span></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- CTA Section -->
            <div class="rounded-2xl shadow-soft-lg p-6 sm:p-8 text-white text-center stagger-item no-print relative overflow-hidden" style="background: linear-gradient(135deg, #0A66C2, #004182);">
                <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white/5 rounded-full blur-lg"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <i class="fas fa-bolt text-2xl text-amber-300"></i>
                        <h2 class="text-2xl font-extrabold tracking-tight">Siap Mulai Proses Perizinan?</h2>
                    </div>
                    <p class="text-sm opacity-90 mb-5 max-w-2xl mx-auto leading-relaxed">
                        Daftar sekarang untuk mendapatkan <strong>analisis lengkap</strong>,
                        pendampingan konsultan bersertifikat, dan akses portal monitoring 24/7.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                        <a href="{{ route('client.register') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary-600 font-bold rounded-xl hover:bg-primary-50 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            <i class="fas fa-check-circle"></i> Daftar Portal Lengkap
                        </a>
                        <a href="{{ $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855' }}?text={{ rawurlencode('Halo, saya tertarik dengan hasil analisis ' . $inquiry->inquiry_number) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 backdrop-blur border-2 border-white/30 text-white font-semibold rounded-xl hover:bg-white/20 transition-all">
                            <i class="fab fa-whatsapp text-lg"></i> Chat via WhatsApp
                        </a>
                    </div>
                    <p class="mt-4 text-xs opacity-75">
                        Email hasil analisis sudah dikirim ke <strong>{{ $inquiry->email }}</strong>
                    </p>
                </div>
            </div>

            <!-- Social Proof -->
            <div class="text-center py-4 stagger-item no-print">
                <p class="text-xs text-gray-500 mb-3">
                    <i class="fas fa-users text-primary-400"></i>
                    <strong class="text-gray-600">{{ Cache::remember('inquiry_total_count', 3600, fn() => App\Models\ServiceInquiry::count()) }}</strong> perusahaan telah menggunakan fitur analisis AI kami
                </p>
                <div class="flex items-center justify-center gap-6 text-xs text-gray-400">
                    <span class="flex items-center gap-1.5"><i class="fas fa-gift text-primary-400"></i> Gratis</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-lock text-primary-400"></i> Data Aman</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-bolt text-primary-400"></i> Hasil Cepat</span>
                </div>
            </div>

        </div>
        @endif

        </div>
    </section>

    <!-- WhatsApp FAB -->
    <a href="{{ $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855' }}" target="_blank" rel="noopener noreferrer"
       class="fixed bottom-6 right-6 z-40 w-14 h-14 flex items-center justify-center rounded-full text-white shadow-lg fab-pulse no-print"
       style="background: linear-gradient(135deg, #25D366, #128C7E);" aria-label="Chat WhatsApp">
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>

    <!-- Footer -->
    @php
        $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
        $whatsappNumber = $contact['whatsapp'] ?? '6283879602855';
        $whatsappLink = $contact['whatsapp_link'] ?? ('https://wa.me/' . $whatsappNumber);
        $emailAddress = $contact['email'] ?? 'info@bizmark.id';
    @endphp
    <footer class="bg-gray-900 text-gray-300 py-8 mt-auto no-print">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="text-xl font-bold text-white mb-4"><i class="fas fa-certificate mr-2 text-primary-400"></i>Bizmark.ID</div>
                    <p class="text-sm text-gray-300">{{ __('landing.footer.tagline') }}</p>
                    <div class="mt-4 flex gap-4">
                        <a href="https://www.linkedin.com/company/bizmark-id" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary-400 transition" aria-label="LinkedIn"><i class="fab fa-linkedin text-2xl"></i></a>
                        <a href="https://www.facebook.com/bizmark.id" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary-400 transition" aria-label="Facebook"><i class="fab fa-facebook text-2xl"></i></a>
                        <a href="https://www.instagram.com/bizmark.id" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary-400 transition" aria-label="Instagram"><i class="fab fa-instagram text-2xl"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.navigation') }}</h4>
                    @php
                        $footerLandingRoute = app()->getLocale() === 'en' ? route('landing.en') : route('landing.id');
                        $footerBlogRoute = app()->getLocale() === 'en' ? route('blog.index.en') : route('blog.index.id');
                    @endphp
                    <ul class="space-y-1 text-sm">
                        <li><a href="{{ $footerLandingRoute }}#services" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.services') }}</a></li>
                        <li><a href="{{ $footerLandingRoute }}#process" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.process') }}</a></li>
                        <li><a href="{{ $footerLandingRoute }}#about" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.about') }}</a></li>
                        <li><a href="{{ $footerBlogRoute }}" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.blog') }}</a></li>
                        <li><a href="{{ route('career.index') }}" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.footer.careers') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.contact_us') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="mailto:{{ $emailAddress }}" class="text-gray-300 hover:text-white transition"><i class="fas fa-envelope mr-2"></i>{{ $emailAddress }}</a></li>
                        <li><a href="tel:{{ str_replace(' ', '', $phoneNumber) }}" class="text-gray-300 hover:text-white transition"><i class="fas fa-phone mr-2"></i>{{ $phoneNumber }}</a></li>
                        <li><a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition inline-block py-1.5"><i class="fab fa-whatsapp mr-2"></i>{{ __('landing.footer.whatsapp') }}</a></li>
                        <li class="text-gray-300 py-1.5"><i class="fas fa-map-marker-alt mr-2"></i>{{ __('landing.footer.location') }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.legal') }}</h4>
                    <ul class="space-y-2 text-sm">
                        @php
                            $privacyRoute = app()->getLocale() === 'en' ? route('privacy.policy.en') : route('privacy.policy.id');
                            $termsRoute = app()->getLocale() === 'en' ? route('terms.conditions.en') : route('terms.conditions.id');
                        @endphp
                        <li><a href="{{ $privacyRoute }}" class="text-gray-300 hover:text-white transition">{{ __('landing.footer.privacy_policy') }}</a></li>
                        <li><a href="{{ $termsRoute }}" class="text-gray-300 hover:text-white transition">{{ __('landing.footer.terms_conditions') }}</a></li>
                    </ul>
                    <h4 class="text-white font-semibold mb-4 mt-6">{{ __('landing.footer.language') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('locale.set', 'id') }}" class="hover:text-white transition {{ app()->getLocale() == 'id' ? 'font-semibold text-primary-400' : 'text-gray-300' }}">🇮🇩 Indonesia</a></li>
                        <li><a href="{{ route('locale.set', 'en') }}" class="hover:text-white transition {{ app()->getLocale() == 'en' ? 'font-semibold text-primary-400' : 'text-gray-300' }}">🇬🇧 English</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 pb-0 text-center text-sm text-gray-400">
                <p class="mb-1">{{ __('landing.footer.copyright', ['year' => date('Y')]) }}</p>
                <p class="text-gray-400 text-xs mb-0">{{ __('landing.footer.tagline') }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
