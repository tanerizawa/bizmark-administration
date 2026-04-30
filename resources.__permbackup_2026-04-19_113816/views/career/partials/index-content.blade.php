@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide text-center">
        <span class="section-badge mb-4">Karir</span>
        <h1 class="section-title mb-4">Bergabung dengan Tim Bizmark.ID</h1>
        <p class="section-description mx-auto mb-8">Berkembang bersama tim profesional di industri perizinan, compliance, dan solusi digital.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="btn btn-success"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <a href="{{ route('contact.index') }}" class="btn btn-outline-primary"><i class="fas fa-envelope"></i> Kontak</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container-wide">
        @if(session('success'))
            <div class="card mb-6" style="border-color:rgba(22,163,74,.35);box-shadow:var(--shadow-ring);">
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle mt-0.5" style="color:var(--color-success);"></i>
                    <div class="text-sm" style="color:var(--text-secondary);">{{ session('success') }}</div>
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

        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-2" style="color:var(--text-primary);">Lowongan Tersedia</h2>
            <p class="mb-0" style="color:var(--text-secondary);">Temukan posisi yang sesuai dengan keahlian dan minat Anda.</p>
        </div>

        @if($vacancies->count() === 0)
            <div class="card text-center">
                <h3 class="text-xl font-bold mb-2" style="color:var(--text-primary);">Belum Ada Lowongan</h3>
                <p class="mb-6" style="color:var(--text-secondary);">Saat ini belum ada posisi yang dibuka. Silakan cek kembali nanti.</p>
                <a href="{{ route('landing.id') }}" class="btn btn-primary"><i class="fas fa-home"></i> Kembali ke Beranda</a>
            </div>
        @else
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($vacancies as $vacancy)
                    <article class="card h-full flex flex-col">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <span class="chip active">
                                {{ ucfirst(str_replace('-', ' ', $vacancy->employment_type)) }}
                            </span>
                            @if($vacancy->deadline)
                                <span class="text-xs" style="color:var(--text-tertiary);"><i class="far fa-clock mr-1"></i>{{ $vacancy->deadline->diffForHumans() }}</span>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold mb-2" style="color:var(--text-primary);">{{ $vacancy->title }}</h3>
                        <div class="text-sm mb-4" style="color:var(--text-secondary);">
                            <div class="mb-1"><i class="fas fa-map-marker-alt mr-2" style="color:var(--text-tertiary);"></i>{{ $vacancy->location }}</div>
                            <div><i class="fas fa-wallet mr-2" style="color:var(--text-tertiary);"></i>{{ $vacancy->salary_range }}</div>
                        </div>

                        <div class="mt-auto pt-4 border-t flex items-center justify-between" style="border-color:var(--border-light);">
                            <span class="text-xs" style="color:var(--text-tertiary);"><i class="fas fa-users mr-1"></i>{{ $vacancy->applications_count }} pelamar</span>
                            <a href="{{ route('career.show', $vacancy->slug) }}" class="link-primary text-sm inline-flex items-center">Lihat Detail <i class="fas fa-arrow-right ml-2"></i></a>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($vacancies->hasPages())
                <div class="mt-10">
                    {{ $vacancies->links() }}
                </div>
            @endif
        @endif
    </div>
</section>

