@php
    $footerMetrics = config('landing.footer_metrics', []);
    $footerLinks = config('landing.footer_links', []);

    $resolveHref = function ($link) {
        if (!empty($link['type']) && $link['type'] === 'route') {
            return route($link['href']);
        }
        return $link['href'] ?? '#';
    };
@endphp

<footer class="bg-slate-950 text-slate-200">
    <div class="container-wide py-16 lg:py-20 grid lg:grid-cols-[1.2fr_0.8fr_0.8fr_0.7fr] gap-10">
        <div class="space-y-5">
            <div>
                <span class="text-xs uppercase tracking-[0.4em] text-slate-500">Bizmark.ID</span>
                <p class="text-2xl font-semibold text-white">PT Cangah Pajaratan Mandiri</p>
            </div>
            <p class="text-sm text-slate-400 leading-relaxed">
                Konsultan perizinan industri. Kami menyelaraskan dokumen, instansi, dan jadwal agar seluruh izin berjalan presisi.
            </p>
            <div class="grid grid-cols-2 gap-4 text-xs uppercase tracking-[0.35em] text-slate-500">
                @foreach($footerMetrics as $metric)
                    <div>
                        <p class="text-slate-200 text-lg font-semibold">{{ $metric['value'] }}</p>
                        {{ $metric['label'] }}
                    </div>
                @endforeach
            </div>
        </div>

        @foreach($footerLinks as $section => $links)
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.4em] text-slate-500">{{ $section }}</p>
                <ul class="space-y-2 text-sm text-slate-300">
                    @foreach($links as $link)
                        <li>
                            @if(!empty($link['href']))
                                <a href="{{ $resolveHref($link) }}" class="hover:text-white transition">{{ $link['label'] }}</a>
                            @else
                                {{ $link['label'] }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
    <div class="border-t border-slate-800 py-6">
        <div class="container-wide flex flex-col lg:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <p>&copy; {{ now()->year }} Bizmark.ID • PT Cangah Pajaratan Mandiri</p>
            <p>Made in Indonesia</p>
        </div>
    </div>
</footer>
