<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ Cookie::get('portal-theme', 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0a66c2">
    <title>@yield('title', 'Bizmark.ID — Portal Klien')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/pavicon.png') }}">

    @vite(['resources/css/client.css', 'resources/js/client.js'])

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased;
            min-height: 100svh;
        }
        .auth-glow {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 60% at 60% 30%, rgba(255,255,255,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-card {
            background: var(--surface-elevated);
            border: 1px solid var(--border-subtle);
            border-radius: 1.25rem;
            box-shadow: 0 8px 40px rgba(0,0,0,0.10);
        }
        .auth-input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            background: var(--surface-cool);
            border: 1px solid var(--border-subtle);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-primary);
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }
        .auth-input:focus {
            border-color: var(--client-primary);
            box-shadow: 0 0 0 3px rgba(10,102,194,0.12);
        }
        .auth-btn-primary {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--client-primary);
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            transition: filter 0.15s;
        }
        .auth-btn-primary:hover { filter: brightness(1.1); }
        .auth-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        .auth-label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.375rem; }
        .auth-error { font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem; }
    </style>

    @stack('head')
</head>
<body style="background: var(--surface-base, #f1f5f9);" class="flex min-h-screen">

    {{-- Left: Branding panel (hidden on mobile) --}}
    <div class="hidden lg:flex lg:w-[420px] xl:w-[480px] flex-col justify-between relative overflow-hidden flex-shrink-0"
         style="background: linear-gradient(160deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 50%, #001020) 100%);">
        <div class="auth-glow" aria-hidden="true"></div>
        {{-- Glow orb --}}
        <div class="absolute top-12 right-0 w-72 h-72 rounded-full pointer-events-none"
             style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.12) 0%, transparent 70%);" aria-hidden="true"></div>

        <div class="relative px-8 py-10 flex-1 flex flex-col justify-center">
            {{-- Logo --}}
            <div class="flex items-center gap-3 mb-10">
                <img src="{{ asset('images/logo-white.png') }}" alt="Bizmark.ID" class="h-8" onerror="this.style.display='none'">
                <span class="text-xl font-extrabold text-white tracking-tight">Bizmark<span style="opacity:0.7">.ID</span></span>
            </div>

            {{-- Headline --}}
            <h2 class="text-3xl font-extrabold text-white leading-tight mb-4">
                @yield('hero-title', 'Platform Perizinan Usaha Terpadu')
            </h2>
            <p class="text-base text-white/70 leading-relaxed mb-8">
                @yield('hero-subtitle', 'Urus semua perizinan usaha Anda — NIB, IMB, SIUP, dan 40+ izin lainnya — dalam satu platform cerdas.')
            </p>

            {{-- Feature chips --}}
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach([
                    ['icon'=>'fas fa-shield-check', 'text'=>'Terverifikasi OSS'],
                    ['icon'=>'fas fa-robot',         'text'=>'AI KBLI Matcher'],
                    ['icon'=>'fas fa-clock',          'text'=>'Estimasi 7-14 Hari'],
                    ['icon'=>'fas fa-headset',        'text'=>'Konsultan Berpengalaman'],
                ] as $chip)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-white"
                      style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="{{ $chip['icon'] }} text-[9px]" aria-hidden="true"></i>
                    {{ $chip['text'] }}
                </span>
                @endforeach
            </div>

            {{-- Testimonial --}}
            <div class="mt-auto p-4 rounded-xl" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                <p class="text-sm text-white/80 italic leading-relaxed">
                    "BizMark.ID membantu kami mendapatkan semua izin usaha dalam waktu 12 hari kerja. Luar biasa!"
                </p>
                <div class="flex items-center gap-2 mt-3">
                    <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="fas fa-user text-white text-[10px]" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">Budi Santoso</p>
                        <p class="text-[10px] text-white/60">Direktur, PT. Maju Bersama</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Form panel --}}
    <div class="flex-1 flex flex-col items-center justify-center px-4 sm:px-8 py-10">
        {{-- Mobile logo --}}
        <div class="lg:hidden flex items-center gap-2 mb-8">
            <span class="text-lg font-extrabold tracking-tight" style="color: var(--client-primary);">Bizmark<span style="color: var(--text-tertiary)">.ID</span></span>
        </div>

        <div class="w-full max-w-[400px]">
            @yield('form')
        </div>

        <p class="mt-6 text-[11px] text-center" style="color: var(--text-tertiary);">
            &copy; {{ date('Y') }} Bizmark.ID — Hak Cipta Dilindungi &middot;
            <a href="{{ route('landing.id') }}" style="color: var(--client-primary);" class="hover:underline">Kembali ke Beranda</a>
        </p>
    </div>

    @stack('scripts')
</body>
</html>
