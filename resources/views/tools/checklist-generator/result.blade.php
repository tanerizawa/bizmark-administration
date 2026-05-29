@extends('landing.layout')

@section('title', 'Checklist Dokumen - ' . $checklist->permit_type . ' | Bizmark')
@section('description', 'Checklist dokumen lengkap untuk ' . $checklist->permit_type . ' KBLI ' . $checklist->kbli_code . ' di ' . $checklist->city)

@push('styles')
<style>
    .result-page {
        background: linear-gradient(180deg, var(--surface-cool) 0%, var(--surface) 100%);
        min-height: 100vh;
    }

    .result-hero {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #a855f7 100%);
        color: #fff;
        border-radius: var(--radius-xl);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .result-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .result-tag {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 999px;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .category-card {
        background: var(--surface);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .category-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background: var(--surface-cool);
        border-bottom: 1px solid var(--border-light);
        cursor: pointer;
    }

    .category-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .category-count {
        font-size: 0.75rem;
        color: var(--text-secondary);
        background: var(--border-light);
        padding: 0.125rem 0.5rem;
        border-radius: 999px;
    }

    .doc-list { padding: 0.75rem 1.25rem; }

    .doc-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.625rem 0;
        border-bottom: 1px solid var(--border-light);
    }

    .doc-item:last-child { border-bottom: none; }

    .doc-check {
        width: 1.125rem;
        height: 1.125rem;
        border: 2px solid #6366f1;
        border-radius: 0.25rem;
        flex-shrink: 0;
        margin-top: 1px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .doc-required .doc-check { background: #6366f1; border-color: #6366f1; }

    .doc-info { flex: 1; }

    .doc-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .doc-notes {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-top: 0.125rem;
    }

    .doc-copies {
        font-size: 0.6875rem;
        font-weight: 600;
        color: #6366f1;
        background: rgba(99,102,241,0.08);
        padding: 0.125rem 0.375rem;
        border-radius: 4px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .tips-section {
        background: linear-gradient(135deg, #fffbeb, #fef9c3);
        border: 1px solid #fde68a;
        border-radius: var(--radius-xl);
        padding: 1.25rem;
        margin-bottom: 1rem;
    }

    .action-bar {
        position: sticky;
        bottom: 1rem;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 1.5rem;
    }

    @media print {
        .action-bar, nav, header, footer { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="result-page">
    <div class="container py-10 md:py-14">

        {{-- Back link --}}
        <a href="{{ route('checklist.index') }}" class="text-sm text-indigo-600 hover:underline mb-5 inline-flex items-center gap-1">
            <i class="fas fa-arrow-left text-xs"></i> Buat Checklist Baru
        </a>

        {{-- Hero --}}
        <div class="result-hero">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider opacity-70 mb-1">Checklist Dokumen AI</p>
                    <h1 class="font-bold" style="font-size:1.25rem;line-height:1.3">
                        {{ $checklist->permit_type }}
                    </h1>
                    @if(!empty($checklist->checklist_data['summary']))
                        <p class="text-sm mt-1 opacity-80">{{ $checklist->checklist_data['summary'] }}</p>
                    @endif
                </div>
                <div class="text-center flex-shrink-0">
                    <p class="text-3xl font-black">{{ $checklist->checklist_data['estimated_days'] ?? 30 }}</p>
                    <p class="text-xs opacity-70">hari estimasi</p>
                </div>
            </div>
            <div class="result-meta">
                <span class="result-tag"><i class="fas fa-barcode mr-1"></i>KBLI {{ $checklist->kbli_code }}</span>
                <span class="result-tag"><i class="fas fa-location-dot mr-1"></i>{{ $checklist->city }}</span>
                <span class="result-tag"><i class="fas fa-scale-balanced mr-1"></i>{{ ucfirst($checklist->business_scale) }}</span>
                <span class="result-tag"><i class="fas fa-sparkles mr-1"></i>AI Generated</span>
            </div>
        </div>

        <div class="max-w-3xl">

            {{-- Dokumen per kategori --}}
            @php $categories = $checklist->checklist_data['categories'] ?? []; @endphp

            @if(count($categories))
                <h2 class="font-bold text-slate-900 mb-3" style="font-size:.9375rem">
                    Daftar Dokumen
                    <span class="ml-1 text-xs font-normal text-slate-500">
                        ({{ collect($categories)->sum(fn($c) => count($c['documents'] ?? [])) }} total dokumen)
                    </span>
                </h2>

                @foreach($categories as $idx => $category)
                <div class="category-card" x-data="{ open: true }">
                    <div class="category-header" @click="open = !open">
                        <span class="category-title">
                            <i class="fas fa-folder text-indigo-500 text-sm"></i>
                            {{ $category['name'] }}
                            <span class="category-count">{{ count($category['documents'] ?? []) }} dok</span>
                        </span>
                        <i class="fas text-slate-400 text-sm transition-transform" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </div>
                    <div class="doc-list" x-show="open" x-collapse>
                        @foreach($category['documents'] ?? [] as $doc)
                        <div class="doc-item {{ ($doc['required'] ?? true) ? 'doc-required' : '' }}">
                            <div class="doc-check">
                                @if($doc['required'] ?? true)
                                    <svg width="8" height="6" viewBox="0 0 8 6" fill="none">
                                        <path d="M1 3l2 2 4-4" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="doc-info">
                                <p class="doc-name">{{ $doc['name'] }}</p>
                                @if(!empty($doc['notes']))
                                    <p class="doc-notes">{{ $doc['notes'] }}</p>
                                @endif
                            </div>
                            @if(!empty($doc['copies']) && $doc['copies'] > 1)
                                <span class="doc-copies">×{{ $doc['copies'] }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @endif

            {{-- Tips --}}
            @php $tips = $checklist->checklist_data['tips'] ?? []; @endphp
            @if(count($tips))
            <div class="tips-section">
                <h3 class="font-bold text-amber-800 mb-2 flex items-center gap-2" style="font-size:.875rem">
                    <i class="fas fa-lightbulb text-amber-500"></i> Tips dari Konsultan
                </h3>
                <ul class="space-y-1.5">
                    @foreach($tips as $tip)
                        <li class="text-sm text-amber-900 flex items-start gap-2">
                            <i class="fas fa-circle-dot text-amber-400 mt-0.5 text-xs flex-shrink-0"></i>
                            {{ $tip }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Disclaimer --}}
            <p class="text-xs text-slate-400 mt-2 mb-20">
                <i class="fas fa-info-circle mr-1"></i>
                Checklist ini dibuat oleh AI berdasarkan regulasi umum. Persyaratan dapat berbeda tergantung kebijakan daerah dan perubahan regulasi terkini. Selalu verifikasi ke instansi terkait.
            </p>

        </div>

        {{-- Action Bar --}}
        <div class="action-bar">
            <a href="{{ route('checklist.download', $checklist->id) }}"
               class="btn btn-primary"
               style="background:linear-gradient(135deg,#6366f1,#8b5cf6);border-color:#6366f1">
                <i class="fas fa-file-pdf mr-1.5"></i> Unduh PDF
            </a>
            @php
                $waBase = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');
                $waText = 'Halo, saya sudah punya checklist untuk ' . $checklist->permit_type . ' KBLI ' . $checklist->kbli_code . '. Saya butuh bantuan pengurusan.';
                $waHref = $waBase . (str_contains($waBase, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
            @endphp
            <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-secondary">
                <i class="fab fa-whatsapp"></i> Urus Sekarang
            </a>
        </div>

    </div>
</div>
@endsection
