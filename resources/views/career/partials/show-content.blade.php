@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Halo, saya ingin bertanya tentang lowongan: ' . $vacancy->title;
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp

<section class="relative overflow-hidden pt-28 pb-16 bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <a href="{{ route('career.index') }}" class="link-primary text-sm inline-flex items-center mb-5"><i class="fas fa-arrow-left mr-2"></i>Kembali ke Karir</a>

        <span class="section-badge mb-4">Lowongan</span>
        <h1 class="section-title mb-4">{{ $vacancy->title }}</h1>

        <div class="flex flex-wrap gap-2 mb-6">
            <span class="chip active">{{ ucfirst(str_replace('-', ' ', $vacancy->employment_type)) }}</span>
            <span class="chip">{{ $vacancy->location }}</span>
            <span class="chip">{{ $vacancy->salary_range }}</span>
            @if($vacancy->deadline)
                <span class="chip">{{ $vacancy->deadline->format('d M Y') }}</span>
            @endif
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('career.apply', $vacancy->id) }}" class="btn btn-secondary"><i class="fas fa-paper-plane"></i> Lamar Sekarang</a>
            <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary"><i class="fab fa-whatsapp"></i> Tanya via WhatsApp</a>
        </div>
    </div>
</section>

<section class="section pt-0">
    <div class="container-wide">
        @if(session('success'))
            <div class="card mb-6 border-amber-500/35" style="box-shadow:var(--shadow-ring);">
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle mt-0.5 text-amber-500"></i>
                    <div class="text-sm text-gray-600">{{ session('success') }}</div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="card mb-6" style="border-color:rgba(220,38,38,.35);box-shadow:0 0 0 4px rgba(220,38,38,.08);">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle mt-0.5" style="color:#dc2626;"></i>
                    <div class="text-sm" style="color:var(--text-secondary);">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8">
                <div class="card mb-6">
                    <h2 class="text-xl font-bold mb-3 text-gray-900">Deskripsi Pekerjaan</h2>
                    <div class="content-prose">{!! \App\Support\HtmlSanitizer::clean($vacancy->description) !!}</div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="card">
                        <h3 class="text-base font-bold mb-3 text-gray-900">Tanggung Jawab</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            @foreach(($responsibilities ?? []) as $item)
                                <li class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-amber-500"></i><span>{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card">
                        <h3 class="text-base font-bold mb-3 text-gray-900">Kualifikasi</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            @foreach(($qualifications ?? []) as $item)
                                <li class="flex items-start gap-2"><i class="fas fa-check-circle mt-1 text-amber-500"></i><span>{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @if(!empty($benefits))
                    <div class="card mt-6">
                        <h3 class="text-base font-bold mb-3 text-gray-900">Benefit</h3>
                        <ul class="grid sm:grid-cols-2 gap-2 text-sm text-gray-600">
                            @foreach($benefits as $item)
                                <li class="flex items-start gap-2"><i class="fas fa-star mt-1" style="color:var(--color-warning);"></i><span>{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-4">
                <div class="card mb-6">
                    <div class="text-xs font-semibold uppercase tracking-widest mb-2 text-gray-400">Ringkasan</div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center justify-between gap-3"><span>Lokasi</span><span class="font-semibold text-gray-900">{{ $vacancy->location }}</span></div>
                        <div class="flex items-center justify-between gap-3"><span>Tipe</span><span class="font-semibold text-gray-900">{{ ucfirst(str_replace('-', ' ', $vacancy->employment_type)) }}</span></div>
                        <div class="flex items-center justify-between gap-3"><span>Gaji</span><span class="font-semibold text-gray-900">{{ $vacancy->salary_range }}</span></div>
                        <div class="flex items-center justify-between gap-3"><span>Pelamar</span><span class="font-semibold text-gray-900">{{ $vacancy->applications_count }}</span></div>
                    </div>
                    <div class="mt-6 flex flex-col gap-3">
                        <a href="{{ route('career.apply', $vacancy->id) }}" class="btn btn-secondary w-full"><i class="fas fa-paper-plane"></i> Lamar Sekarang</a>
                        <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-full"><i class="fab fa-whatsapp"></i> Tanya via WhatsApp</a>
                    </div>
                </div>

                <div class="card">
                    <div class="text-xs font-semibold uppercase tracking-widest mb-2 text-gray-400">Tips</div>
                    <div class="text-sm text-gray-600">
                        Pastikan CV terbaru dan cantumkan pengalaman yang relevan dengan posisi ini.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
