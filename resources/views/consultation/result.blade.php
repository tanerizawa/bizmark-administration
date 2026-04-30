@extends('consultation.layout')

@section('meta_title', 'Hasil Estimasi Biaya - Bizmark.ID')

@push('styles')
<!-- BreadcrumbList Structured Data -->
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
            "name": "Estimasi Biaya Perizinan",
            "item": "{{ route('consultation.index') }}"
        },
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "Hasil Estimasi",
            "item": "{{ url()->current() }}"
        }
    ]
}
</script>
@endpush

@section('content')
<div class="min-h-screen consultation-section" style="padding-top: 5rem;">
    <!-- Breadcrumb -->
    <div style="background: var(--surface); border-bottom: 1px solid var(--border-light);">
        <div class="container-wide py-4">
            <nav aria-label="Breadcrumb" class="text-xs" style="color: var(--text-tertiary);">
                <a href="{{ url('/') }}" style="color: var(--text-secondary);">Beranda</a>
                <span class="mx-2">/</span>
                <a href="{{ route('consultation.index') }}" style="color: var(--text-secondary);">Estimasi Biaya Perizinan</a>
                <span class="mx-2">/</span>
                <span>Hasil Estimasi</span>
            </nav>
        </div>
    </div>

    <div class="container-wide py-12">
        <div class="max-w-5xl mx-auto">
            
            <!-- Success Header -->
            <div class="form-card p-8 mb-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background: rgba(22, 163, 74, 0.1);">
                        <i class="fas fa-check-circle text-3xl" style="color: var(--color-success);"></i>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold mb-2" style="color: var(--text-primary);">
                            Estimasi Biaya Berhasil!
                        </h1>
                        <p style="color: var(--text-secondary);">
                            Request ID: <span class="font-mono font-bold">#{{ $consultation->id }}</span> 
                            <span class="mx-2">•</span>
                            {{ $consultation->created_at->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                </div>

                <!-- KBLI Info -->
                <div class="p-4 rounded-lg" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.15);">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono font-bold" style="color: var(--color-accent);">{{ $consultation->kbli->code }}</span>
                                <span class="px-2 py-0.5 text-xs font-medium rounded capitalize" style="background: rgba(14, 165, 233, 0.1); color: var(--color-accent);">
                                    {{ $consultation->kbli->complexity_level }}
                                </span>
                            </div>
                            <p class="font-semibold" style="color: var(--text-primary);">{{ $consultation->kbli->description }}</p>
                            <p class="text-sm mt-1" style="color: var(--text-secondary);">{{ $consultation->kbli->category }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost Summary -->
            <div class="rounded-2xl shadow-xl p-8 mb-6 text-white" style="background: linear-gradient(135deg, var(--color-success) 0%, #059669 100%);">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                    <i class="fas fa-calculator"></i>
                    Total Estimasi Biaya
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                        <p class="text-sm opacity-80 mb-1">Subtotal</p>
                        <p class="text-2xl font-bold">{{ $consultation->estimate_data['cost_summary']['formatted']['subtotal'] ?? '-' }}</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur rounded-lg p-4 ring-2 ring-white/50">
                        <p class="text-sm opacity-80 mb-1">Total Estimasi</p>
                        <p class="text-4xl font-bold">{{ $consultation->estimate_data['cost_summary']['formatted']['grand_total'] ?? '-' }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                        <p class="text-sm opacity-80 mb-1">Kisaran Biaya</p>
                        <p class="text-xl font-semibold">{{ $consultation->estimate_data['cost_summary']['formatted']['range'] ?? '-' }}</p>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-brain text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold">AI Confidence Score</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-32 h-2 bg-white/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-white rounded-full" style="width: {{ ($consultation->estimate_data['confidence_score'] ?? 0) * 100 }}%"></div>
                                </div>
                                <span class="font-bold">{{ number_format(($consultation->estimate_data['confidence_score'] ?? 0) * 100, 0) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost Breakdown -->
            <div class="form-card p-8 mb-6">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-3" style="color: var(--text-primary);">
                    <i class="fas fa-file-invoice-dollar" style="color: #a855f7;"></i>
                    Rincian Biaya
                </h2>

                @php
                    $breakdown = $consultation->estimate_data['cost_breakdown'] ?? [];
                @endphp

                <!-- Investment Context -->
                @if(isset($consultation->estimate_data['investment_percentage']) && isset($consultation->estimate_data['investment_value']))
                <div class="mb-6 p-4 rounded-lg" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.15);">
                    <h3 class="font-bold mb-2 flex items-center gap-2" style="color: var(--color-accent);">
                        <i class="fas fa-chart-pie"></i>
                        Basis Perhitungan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p style="color: var(--text-secondary);">Nilai Investasi</p>
                            <p class="font-bold" style="color: var(--text-primary);">Rp {{ number_format($consultation->estimate_data['investment_value'], 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p style="color: var(--text-secondary);">Persentase Perizinan</p>
                            <p class="font-bold" style="color: var(--color-accent);">{{ number_format($consultation->estimate_data['investment_percentage'], 1) }}%</p>
                        </div>
                        <div>
                            <p style="color: var(--text-secondary);">Kompleksitas</p>
                            <p class="font-bold capitalize" style="color: var(--text-primary);">{{ $consultation->kbli->complexity_level }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Biaya Pemerintah -->
                <div class="mb-6">
                    <h3 class="font-bold mb-3 flex items-center justify-between" style="color: var(--text-primary);">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-landmark" style="color: var(--color-accent);"></i>
                            Biaya Pemerintah
                        </span>
                        <span style="color: var(--color-accent);">Rp {{ number_format($breakdown['biaya_pemerintah']['total'] ?? 0, 0, ',', '.') }}</span>
                    </h3>
                    <div class="space-y-2 pl-4" style="border-left: 2px solid var(--color-accent);">
                        @foreach(($breakdown['biaya_pemerintah']['breakdown'] ?? []) as $key => $value)
                            <div class="flex items-center justify-between text-sm">
                                <span class="capitalize" style="color: var(--text-secondary);">{{ str_replace('_', ' ', $key) }}</span>
                                <span class="font-semibold" style="color: var(--text-primary);">Rp {{ number_format($value, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs mt-2 pl-4" style="color: var(--text-tertiary);">
                        <i class="fas fa-info-circle mr-1"></i>
                        PNBP, retribusi daerah, dan biaya resmi lainnya
                    </p>
                </div>

                <!-- Biaya Konsultan -->
                <div class="mb-6">
                    <h3 class="font-bold mb-3 flex items-center justify-between" style="color: var(--text-primary);">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-user-tie" style="color: #a855f7;"></i>
                            Biaya Konsultan
                        </span>
                        <span style="color: #a855f7;">Rp {{ number_format($breakdown['biaya_konsultan']['total'] ?? 0, 0, ',', '.') }}</span>
                    </h3>
                    <div class="space-y-2 pl-4" style="border-left: 2px solid #a855f7;">
                        @foreach(($breakdown['biaya_konsultan']['breakdown'] ?? []) as $key => $detail)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex-1">
                                    <span class="capitalize" style="color: var(--text-secondary);">{{ str_replace('_', ' ', $key) }}</span>
                                    @if(isset($detail['hours']))
                                        <span class="text-xs ml-2" style="color: var(--text-tertiary);">({{ $detail['hours'] }} jam @ Rp {{ number_format($detail['rate'], 0, ',', '.') }})</span>
                                    @endif
                                </div>
                                <span class="font-semibold" style="color: var(--text-primary);">Rp {{ number_format($detail['cost'] ?? $detail, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="pt-2" style="border-top: 1px solid var(--border-light);">
                            <div class="flex items-center justify-between text-sm font-medium">
                                <span style="color: var(--text-secondary);">Total Jam Kerja</span>
                                <span style="color: var(--text-primary);">{{ $breakdown['biaya_konsultan']['total_hours'] ?? 0 }} jam</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs mt-2 pl-4" style="color: var(--text-tertiary);">
                        <i class="fas fa-info-circle mr-1"></i>
                        Jasa konsultasi, persiapan dokumen, dan pengurusan
                    </p>
                </div>

                <!-- Overhead -->
                <div class="mb-6">
                    <h3 class="font-bold mb-3 flex items-center justify-between" style="color: var(--text-primary);">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-cog" style="color: var(--color-secondary);"></i>
                            Overhead ({{ $breakdown['overhead']['percentage'] ?? 20 }}%)
                        </span>
                        <span style="color: var(--color-secondary);">Rp {{ number_format($breakdown['overhead']['amount'] ?? 0, 0, ',', '.') }}</span>
                    </h3>
                    <p class="text-xs pl-4" style="color: var(--text-tertiary);">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $breakdown['overhead']['description'] ?? 'Administrasi, koordinasi, dan manajemen project' }}
                    </p>
                </div>
            </div>

            <!-- AI Analysis Results -->
            @if(isset($consultation->estimate_data['ai_analysis']))
            <div class="form-card p-8 mb-6">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-3" style="color: var(--text-primary);">
                    <i class="fas fa-robot" style="color: var(--color-accent);"></i>
                    AI Analysis
                </h2>

                @php
                    $aiAnalysis = $consultation->estimate_data['ai_analysis'];
                @endphp

                <!-- Permits Count -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="rounded-lg p-4" style="background: rgba(14, 165, 233, 0.05);">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: rgba(14, 165, 233, 0.1);">
                                <i class="fas fa-file-alt text-xl" style="color: var(--color-accent);"></i>
                            </div>
                            <div>
                                <p class="text-sm" style="color: var(--text-secondary);">Total Dokumen Perizinan</p>
                                <p class="text-2xl font-bold" style="color: var(--color-accent);">{{ $aiAnalysis['permits_count'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg p-4" style="background: rgba(168, 85, 247, 0.05);">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: rgba(168, 85, 247, 0.1);">
                                <i class="fas fa-microchip text-xl" style="color: #a855f7;"></i>
                            </div>
                            <div>
                                <p class="text-sm" style="color: var(--text-secondary);">AI Model</p>
                                <p class="text-sm font-semibold" style="color: #a855f7;">{{ $aiAnalysis['model_used'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                @if(isset($aiAnalysis['timeline']))
                <div class="rounded-lg p-6" style="border: 1px solid var(--border-light);">
                    <h3 class="font-bold mb-4 flex items-center gap-2" style="color: var(--text-primary);">
                        <i class="fas fa-clock" style="color: var(--color-success);"></i>
                        Estimasi Timeline
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="text-center">
                            <p class="text-sm mb-1" style="color: var(--text-secondary);">Minimum</p>
                            <p class="text-2xl font-bold" style="color: var(--text-primary);">{{ $aiAnalysis['timeline']['minimum_days'] ?? 0 }} hari</p>
                        </div>
                        <div class="text-center rounded-lg py-2" style="background: rgba(22, 163, 74, 0.05);">
                            <p class="text-sm mb-1" style="color: var(--text-secondary);">Realistis</p>
                            <p class="text-3xl font-bold" style="color: var(--color-success);">{{ $aiAnalysis['timeline']['realistic_days'] ?? 0 }} hari</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm mb-1" style="color: var(--text-secondary);">Maximum</p>
                            <p class="text-2xl font-bold" style="color: var(--text-primary);">{{ $aiAnalysis['timeline']['maximum_days'] ?? 0 }} hari</p>
                        </div>
                    </div>

                    @if(isset($aiAnalysis['timeline']['critical_path']) && count($aiAnalysis['timeline']['critical_path']) > 0)
                    <div class="mt-4 pt-4" style="border-top: 1px solid var(--border-light);">
                        <p class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Critical Path:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($aiAnalysis['timeline']['critical_path'] as $permit)
                                <span class="px-3 py-1 text-sm font-medium rounded-lg" style="background: rgba(249, 115, 22, 0.1); color: var(--color-secondary);">
                                    {{ $permit }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endif
            {{-- RAG Regulation Insights Component --}}
            @include('consultation.partials.rag-insights')


            <!-- Next Steps -->
            <div class="form-card p-8 mb-6">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-3" style="color: var(--text-primary);">
                    <i class="fas fa-tasks" style="color: var(--color-success);"></i>
                    Langkah Selanjutnya
                </h2>

                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 rounded-lg" style="background: rgba(14, 165, 233, 0.05);">
                        <div class="w-8 h-8 text-white rounded-full flex items-center justify-center font-bold shrink-0" style="background: var(--color-accent);">1</div>
                        <div>
                            <p class="font-semibold" style="color: var(--text-primary);">Tim kami akan menghubungi Anda</p>
                            <p class="text-sm mt-1" style="color: var(--text-secondary);">Dalam 24 jam ke nomor WhatsApp yang Anda berikan untuk diskusi lebih lanjut.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-lg" style="background: rgba(168, 85, 247, 0.05);">
                        <div class="w-8 h-8 text-white rounded-full flex items-center justify-center font-bold shrink-0" style="background: #a855f7;">2</div>
                        <div>
                            <p class="font-semibold" style="color: var(--text-primary);">Cek email Anda</p>
                            <p class="text-sm mt-1" style="color: var(--text-secondary);">Laporan detail estimasi biaya telah dikirim ke email Anda.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-lg" style="background: rgba(22, 163, 74, 0.05);">
                        <div class="w-8 h-8 text-white rounded-full flex items-center justify-center font-bold shrink-0" style="background: var(--color-success);">3</div>
                        <div>
                            <p class="font-semibold" style="color: var(--text-primary);">Daftar ke Client Portal</p>
                            <p class="text-sm mt-1" style="color: var(--text-secondary);">Untuk tracking progress dan manajemen project secara real-time.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                @php
                    $whatsappBase = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');
                    $whatsappText = "Halo Bizmark.ID, saya sudah dapat estimasi biaya (Request ID: #{$consultation->id}) dan ingin konsultasi lebih lanjut";
                    $whatsappHref = $whatsappBase . (str_contains($whatsappBase, '?') ? '&' : '?') . 'text=' . rawurlencode($whatsappText);
                @endphp
                <a href="{{ $whatsappHref }}"
                   target="_blank"
                   rel="noopener"
                   class="btn btn-secondary btn-lg flex-1 flex items-center justify-center gap-3">
                    <i class="fab fa-whatsapp text-2xl"></i>
                    <span>Hubungi via WhatsApp</span>
                </a>

                <a href="/estimasi-biaya"
                   class="flex-1 flex items-center justify-center gap-3 font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all"
                   style="background: var(--surface-warm); color: var(--text-primary); border: 1px solid var(--border-light);">
                    <i class="fas fa-redo"></i>
                    <span>Buat Estimasi Baru</span>
                </a>
            </div>

            <!-- Download Report -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a
                    href="{{ route('consultation.pdf', $consultation->id) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium transition pdf-download-btn"
                    style="background: rgba(14, 165, 233, 0.1); color: var(--color-accent);"
                >
                    <i class="fas fa-file-pdf"></i>
                    <span>Download Laporan PDF</span>
                </a>
                <button
                    class="inline-flex items-center gap-2 transition print-btn"
                    style="color: var(--text-tertiary);"
                    onclick="window.print()"
                >
                    <i class="fas fa-print"></i>
                    <span>Print Halaman</span>
                </button>
            </div>
            
            <!-- Recommendation Box -->
            <div class="mt-8 p-6 rounded-2xl" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.05) 0%, rgba(168, 85, 247, 0.05) 100%); border: 1px solid var(--border-light);">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2" style="color: var(--text-primary);">
                    <i class="fas fa-thumbs-up" style="color: var(--color-success);"></i>
                    Rekomendasi Selanjutnya
                </h3>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <!-- Recommendation 1: Verify permits -->
                    <div class="p-4 rounded-xl" style="background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.2);">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--color-accent); color: white;">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm mb-1" style="color: var(--text-primary);">Pastikan Izin Sudah Lengkap</h4>
                                <p class="text-xs mb-3" style="color: var(--text-secondary);">Gunakan AI untuk memverifikasi apakah ada izin lain yang mungkin Anda butuhkan berdasarkan aktivitas bisnis.</p>
                                <a href="/konsultasi-gratis" class="inline-flex items-center gap-2 text-sm font-semibold" style="color: var(--color-accent);">
                                    Analisis Gratis <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recommendation 2: Contact consultant -->
                    <div class="p-4 rounded-xl" style="background: rgba(22, 163, 74, 0.08); border: 1px solid rgba(22, 163, 74, 0.2);">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--color-success); color: white;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm mb-1" style="color: var(--text-primary);">Konsultasi dengan Ahli</h4>
                                <p class="text-xs mb-3" style="color: var(--text-secondary);">Diskusikan estimasi ini dengan konsultan kami untuk mendapatkan penawaran pasti dan jadwal pengerjaan secara rinci.</p>
                                @php
                                    $whatsappBase = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');
                                    $whatsappText = "Halo, saya sudah dapat estimasi biaya (ID: #{$consultation->id}). Saya ingin konsultasi lebih lanjut.";
                                    $whatsappHref = $whatsappBase . (str_contains($whatsappBase, '?') ? '&' : '?') . 'text=' . rawurlencode($whatsappText);
                                @endphp
                                <a href="{{ $whatsappHref }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-semibold" style="color: var(--color-success);">
                                    <i class="fab fa-whatsapp"></i> Chat Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
