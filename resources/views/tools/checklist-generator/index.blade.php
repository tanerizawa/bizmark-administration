@extends('landing.layout')

@section('title', 'Checklist Dokumen AI - Generator Otomatis Perizinan')
@section('description', 'Generate checklist dokumen perizinan usaha lengkap secara otomatis menggunakan AI. Gratis, akurat, dan langsung bisa diunduh sebagai PDF.')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .checklist-page {
        background:
            radial-gradient(1200px 420px at 0% 0%, rgba(15, 23, 42, 0.07) 0%, rgba(15, 23, 42, 0) 70%),
            radial-gradient(900px 360px at 100% 0%, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0) 70%),
            linear-gradient(180deg, var(--surface-cool) 0%, var(--surface) 52%, var(--surface-warm) 100%);
        min-height: 100vh;
    }

    .cl-shell {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    }

    @media (max-width: 768px) {
        .cl-shell { grid-template-columns: 1fr; }
    }

    .cl-panel {
        background: var(--surface);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        padding: 1.5rem;
    }

    .cl-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .cl-input,
    .cl-select {
        width: 100%;
        border: 1px solid var(--border-medium);
        border-radius: var(--radius-md);
        background: #fff;
        color: var(--text-primary);
        padding: 0.75rem 0.875rem;
        font-size: 0.95rem;
        transition: border-color .2s ease, box-shadow .2s ease;
        margin-bottom: 1rem;
    }

    .cl-input:focus,
    .cl-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }

    .cl-scale-options {
        display: grid;
        gap: 0.5rem;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin-bottom: 1rem;
    }

    .cl-scale-opt { position: relative; }
    .cl-scale-opt input { position: absolute; opacity: 0; pointer-events: none; }

    .cl-scale-card {
        border: 1px solid var(--border-light);
        border-radius: var(--radius-lg);
        background: #fff;
        padding: 0.75rem 0.5rem;
        text-align: center;
        cursor: pointer;
        transition: all .2s ease;
    }

    .cl-scale-card i {
        display: block;
        font-size: 1rem;
        margin-bottom: 0.25rem;
        color: var(--text-secondary);
    }

    .cl-scale-card strong {
        display: block;
        font-size: 0.8125rem;
        color: var(--text-primary);
    }

    .cl-scale-opt input:checked + .cl-scale-card {
        border-color: #6366f1;
        background: rgba(99, 102, 241, 0.07);
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.12);
    }

    .cl-scale-card:hover {
        border-color: var(--border-medium);
        box-shadow: var(--shadow-sm);
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
    }

    .feature-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .feature-list li i {
        color: #6366f1;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .ai-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.625rem;
        border-radius: 999px;
        letter-spacing: 0.02em;
    }
</style>
@endpush

@section('content')
<div class="checklist-page">
    <div class="container py-12 md:py-16">

        {{-- Page Header --}}
        <div class="max-w-2xl mb-8">
            <span class="eyebrow mb-2">Alat Gratis</span>
            <h1 class="display-md mt-1 mb-2">
                Checklist Dokumen <span style="background:linear-gradient(135deg,#6366f1,#8b5cf6);-webkit-background-clip:text;-webkit-text-fill-color:transparent">AI Generator</span>
            </h1>
            <p class="text-sm text-slate-600 max-w-xl">
                Masukkan kode KBLI dan jenis izin yang dibutuhkan — AI kami akan menghasilkan checklist dokumen lengkap yang bisa langsung Anda unduh.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-6 text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="cl-shell">
            {{-- Form Panel --}}
            <section>
                <div class="cl-panel">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="editorial-icon-badge" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                            <i class="fas fa-list-check icon-xl" style="color:#fff"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-900" style="font-size:.9375rem">Generate Checklist</h2>
                            <p class="text-xs text-slate-500">Isi form di bawah ini</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('checklist.generate') }}" x-data="{ loading: false }" @submit="loading = true">
                        @csrf

                        <label class="cl-label" for="kbli_code">Kode KBLI <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="kbli_code"
                            name="kbli_code"
                            value="{{ old('kbli_code') }}"
                            required
                            maxlength="5"
                            pattern="\d{5}"
                            placeholder="contoh: 47111"
                            class="cl-input"
                        >

                        <label class="cl-label" for="permit_type">Jenis Izin yang Diajukan <span class="text-red-500">*</span></label>
                        <select id="permit_type" name="permit_type" required class="cl-select">
                            <option value="">Pilih jenis izin...</option>
                            <option value="NIB (Nomor Induk Berusaha)" @selected(old('permit_type') === 'NIB (Nomor Induk Berusaha)')>NIB (Nomor Induk Berusaha)</option>
                            <option value="Izin Usaha Mikro dan Kecil (IUMK)" @selected(old('permit_type') === 'Izin Usaha Mikro dan Kecil (IUMK)')>Izin Usaha Mikro dan Kecil (IUMK)</option>
                            <option value="Sertifikat Standar" @selected(old('permit_type') === 'Sertifikat Standar')>Sertifikat Standar</option>
                            <option value="Izin (Risiko Tinggi)" @selected(old('permit_type') === 'Izin (Risiko Tinggi)')>Izin (Risiko Tinggi)</option>
                            <option value="SIUP (Surat Izin Usaha Perdagangan)" @selected(old('permit_type') === 'SIUP (Surat Izin Usaha Perdagangan)')>SIUP (Surat Izin Usaha Perdagangan)</option>
                            <option value="TDP (Tanda Daftar Perusahaan)" @selected(old('permit_type') === 'TDP (Tanda Daftar Perusahaan)')>TDP (Tanda Daftar Perusahaan)</option>
                            <option value="SITU (Surat Izin Tempat Usaha)" @selected(old('permit_type') === 'SITU (Surat Izin Tempat Usaha)')>SITU (Surat Izin Tempat Usaha)</option>
                            <option value="HO (Izin Gangguan)" @selected(old('permit_type') === 'HO (Izin Gangguan)')>HO (Izin Gangguan)</option>
                            <option value="AMDAL / UKL-UPL" @selected(old('permit_type') === 'AMDAL / UKL-UPL')>AMDAL / UKL-UPL</option>
                            <option value="IMB / PBG (Persetujuan Bangunan Gedung)" @selected(old('permit_type') === 'IMB / PBG (Persetujuan Bangunan Gedung)')>IMB / PBG (Persetujuan Bangunan Gedung)</option>
                            <option value="Izin Industri" @selected(old('permit_type') === 'Izin Industri')>Izin Industri</option>
                            <option value="Izin Edar Produk" @selected(old('permit_type') === 'Izin Edar Produk')>Izin Edar Produk</option>
                        </select>

                        <label class="cl-label" for="city">Kota / Kabupaten <span class="text-red-500">*</span></label>
                        <select id="city" name="city" required class="cl-select">
                            <option value="">Pilih kota...</option>
                            @foreach(['Jakarta Pusat','Jakarta Selatan','Jakarta Barat','Jakarta Utara','Jakarta Timur','Bogor','Depok','Tangerang','Tangerang Selatan','Bekasi','Bandung','Bandung Barat','Surabaya','Semarang','Yogyakarta','Malang','Medan','Makassar','Palembang','Balikpapan','Denpasar','Lainnya'] as $c)
                                <option value="{{ $c }}" @selected(old('city') === $c)>{{ $c }}</option>
                            @endforeach
                        </select>

                        <label class="cl-label mb-2 block">Skala Usaha <span class="text-red-500">*</span></label>
                        <div class="cl-scale-options">
                            @foreach(['mikro' => ['icon' => 'fa-store', 'label' => 'Mikro'], 'kecil' => ['icon' => 'fa-shop', 'label' => 'Kecil'], 'menengah' => ['icon' => 'fa-building', 'label' => 'Menengah'], 'besar' => ['icon' => 'fa-city', 'label' => 'Besar']] as $val => $meta)
                            <label class="cl-scale-opt">
                                <input type="radio" name="business_scale" value="{{ $val }}" @checked(old('business_scale', 'kecil') === $val) required>
                                <span class="cl-scale-card">
                                    <i class="fas {{ $meta['icon'] }}"></i>
                                    <strong>{{ $meta['label'] }}</strong>
                                </span>
                            </label>
                            @endforeach
                        </div>

                        <label class="cl-label" for="email">Email (opsional)</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="untuk menerima salinan via email"
                            class="cl-input"
                        >

                        <button
                            type="submit"
                            :disabled="loading"
                            class="btn btn-primary w-full disabled:opacity-60 disabled:cursor-not-allowed"
                            style="background:linear-gradient(135deg,#6366f1,#8b5cf6);border-color:#6366f1"
                        >
                            <span x-show="!loading" x-cloak><i class="fas fa-wand-magic-sparkles mr-2"></i>Generate Checklist dengan AI</span>
                            <span x-show="loading" x-cloak><i class="fas fa-spinner fa-spin mr-2"></i>AI sedang memproses...</span>
                            <span x-show="!loading && true"><i class="fas fa-wand-magic-sparkles mr-2"></i>Generate Checklist dengan AI</span>
                        </button>
                    </form>
                </div>
            </section>

            {{-- Info Panel --}}
            <section class="space-y-4">
                <div class="cl-panel">
                    <span class="ai-badge mb-3 inline-flex"><i class="fas fa-sparkles"></i> Powered by AI</span>
                    <h3 class="font-bold text-slate-900 mb-3" style="font-size:.9375rem">Apa yang Anda Dapatkan?</h3>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i><span>Checklist dokumen <strong>lengkap & terstruktur</strong> sesuai KBLI dan jenis izin</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Dibagi per kategori: identitas, legalitas, lokasi, teknis, lingkungan</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Estimasi waktu proses dan jumlah salinan yang dibutuhkan</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Tips dari konsultan berpengalaman</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Unduh sebagai <strong>PDF</strong> — siap cetak</span></li>
                    </ul>
                </div>

                <div class="cl-panel">
                    <h3 class="font-bold text-slate-900 mb-2" style="font-size:.9375rem">Cara Menggunakan</h3>
                    <ol class="space-y-2 text-sm text-slate-600" style="padding-left:1.25rem">
                        <li>Cari kode KBLI usaha Anda (5 digit) di <a href="https://oss.go.id" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">OSS-RBA</a></li>
                        <li>Pilih jenis izin yang akan diajukan</li>
                        <li>Isi kota dan skala usaha</li>
                        <li>Klik Generate — AI menyiapkan checklist dalam ~10 detik</li>
                        <li>Unduh PDF atau konsultasi lebih lanjut dengan tim kami</li>
                    </ol>
                </div>

                <div class="cl-panel text-center">
                    <h3 class="font-bold text-slate-900 mb-1" style="font-size:.9375rem">Butuh Pendampingan?</h3>
                    <p class="text-xs text-slate-500 mb-3">Tim Bizmark siap membantu pengurusan dari A sampai Z.</p>
                    @php
                        $waBase = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');
                        $waText = 'Halo, saya ingin konsultasi perizinan usaha.';
                        $waHref = $waBase . (str_contains($waBase, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
                    @endphp
                    <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-secondary text-sm">
                        <i class="fab fa-whatsapp"></i> Konsultasi Gratis
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
