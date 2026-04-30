@extends('landing.layout')

@section('title', 'Kalkulator Perizinan - Estimasi Biaya & Waktu')
@section('description', 'Hitung estimasi biaya dan waktu pengurusan perizinan usaha Anda dengan Kalkulator Perizinan Bizmark.id. Gratis dan akurat.')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .calculator-page {
        background:
            radial-gradient(1200px 420px at 0% 0%, rgba(15, 23, 42, 0.08) 0%, rgba(15, 23, 42, 0) 70%),
            radial-gradient(900px 360px at 100% 0%, rgba(249, 115, 22, 0.12) 0%, rgba(249, 115, 22, 0) 70%),
            linear-gradient(180deg, var(--surface-cool) 0%, var(--surface) 52%, var(--surface-warm) 100%);
    }

    .calculator-shell {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
    }

    .calc-panel {
        background: var(--surface);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        padding: 1.5rem;
    }

    .calc-group { margin-bottom: 1rem; }

    .calc-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .calc-input,
    .calc-select {
        width: 100%;
        border: 1px solid var(--border-medium);
        border-radius: var(--radius-md);
        background: #fff;
        color: var(--text-primary);
        padding: 0.75rem 0.875rem;
        font-size: 0.95rem;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .calc-input:focus,
    .calc-select:focus {
        border-color: var(--color-accent);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        outline: none;
    }

    .calc-options {
        display: grid;
        gap: 0.625rem;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .calc-option { position: relative; }

    .calc-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .calc-option-card {
        border: 1px solid var(--border-light);
        border-radius: var(--radius-lg);
        background: #fff;
        padding: 0.875rem 0.75rem;
        text-align: center;
        min-height: 102px;
        transition: all .2s ease;
        cursor: pointer;
    }

    .calc-option-card i {
        display: block;
        font-size: 1.125rem;
        margin-bottom: 0.375rem;
        color: var(--text-secondary);
    }

    .calc-option-card strong {
        display: block;
        font-size: 0.875rem;
        color: var(--text-primary);
        line-height: 1.35;
    }

    .calc-option-card span {
        display: block;
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-top: 0.125rem;
    }

    .calc-option input:checked + .calc-option-card {
        border-color: var(--color-accent);
        background: rgba(14, 165, 233, 0.08);
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.12);
        transform: translateY(-1px);
    }

    .calc-option-card:hover {
        border-color: var(--border-medium);
        box-shadow: var(--shadow-sm);
    }

    .result-summary {
        background: linear-gradient(140deg, var(--color-primary-dark) 0%, var(--color-primary) 58%, #1e3a5f 100%);
        color: var(--text-inverse);
        border-radius: var(--radius-xl);
        padding: 1.25rem;
        box-shadow: var(--shadow-lg);
    }

    .result-metrics {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-top: 0.75rem;
    }

    .result-metric {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: var(--radius-lg);
        padding: 0.75rem;
    }

    .result-metric .label {
        font-size: 0.75rem;
        color: rgba(248, 250, 252, 0.8);
        margin-bottom: 0.25rem;
    }

    .result-metric .value {
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.2;
        color: #fff;
    }

    .result-docs {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 0.625rem;
    }

    .result-docs li {
        display: flex;
        gap: 0.5rem;
        align-items: flex-start;
        color: var(--text-secondary);
        font-size: 0.92rem;
    }

    .result-docs i {
        color: var(--color-success);
        margin-top: 0.15rem;
    }

    .result-placeholder {
        border: 1px dashed var(--border-medium);
        border-radius: var(--radius-lg);
        padding: 1rem;
        color: var(--text-secondary);
        background: var(--surface-cool);
        font-size: 0.9rem;
    }

    .calc-error {
        border: 1px solid rgba(220, 38, 38, 0.25);
        background: rgba(254, 242, 242, 0.85);
        color: #991b1b;
        border-radius: var(--radius-md);
        padding: 0.65rem 0.8rem;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 1023px) {
        .calculator-shell {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .calc-panel { padding: 1rem; }
        .calc-options {
            grid-template-columns: 1fr;
        }
        .calc-option-card {
            min-height: initial;
            text-align: left;
            padding: 0.875rem;
        }
        .calc-option-card i {
            display: inline;
            margin-bottom: 0;
            margin-right: 0.35rem;
        }
        .result-metrics {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="calculator-page section-sm">
    <div class="container max-w-6xl" x-data="calculatorApp()">
        <div class="text-center mb-10">
            <span class="section-badge">Alat Perizinan</span>
            <h1 class="section-title mb-3">Kalkulator Perizinan Usaha</h1>
            <p class="section-description mx-auto">
                Simulasikan estimasi biaya, jadwal, dan daftar dokumen untuk membantu perencanaan pengurusan izin sebelum konsultasi.
            </p>
        </div>

        <div class="calculator-shell">
            <section class="calc-panel" aria-label="Form kalkulator perizinan">
                <h2 class="text-2xl font-bold mb-1 text-slate-900">Masukkan Data Usaha</h2>
                <p class="text-sm text-slate-600 mb-6">Isi seluruh data agar estimasi lebih relevan.</p>

                <div x-show="errorMessage" x-cloak class="calc-error" x-text="errorMessage"></div>

                <form @submit.prevent="calculate()" novalidate>
                    <div class="calc-group">
                        <label class="calc-label" for="industry">Bidang Usaha</label>
                        <select id="industry" x-model="formData.industry" required class="calc-select">
                            <option value="">Pilih Bidang Usaha</option>
                            @foreach($industries as $industry)
                                <option value="{{ $industry }}">{{ $industry }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="calc-group">
                        <label class="calc-label" for="permitType">Jenis Izin</label>
                        <select id="permitType" x-model="formData.permit_type_id" required class="calc-select">
                            <option value="">Pilih Jenis Izin</option>
                            @foreach($permitTypes as $permit)
                                <option value="{{ $permit->id }}">{{ $permit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="calc-group">
                        <label class="calc-label" for="city">Lokasi Usaha</label>
                        <select id="city" x-model="formData.city" required class="calc-select">
                            <option value="">Pilih Kota</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="calc-group">
                        <p class="calc-label">Skala Usaha</p>
                        <div class="calc-options">
                            <label class="calc-option">
                                <input type="radio" x-model="formData.company_size" value="small" required>
                                <span class="calc-option-card">
                                    <i class="fas fa-store"></i>
                                    <strong>Kecil</strong>
                                    <span>1-20 orang</span>
                                </span>
                            </label>
                            <label class="calc-option">
                                <input type="radio" x-model="formData.company_size" value="medium">
                                <span class="calc-option-card">
                                    <i class="fas fa-building"></i>
                                    <strong>Menengah</strong>
                                    <span>21-100 orang</span>
                                </span>
                            </label>
                            <label class="calc-option">
                                <input type="radio" x-model="formData.company_size" value="large">
                                <span class="calc-option-card">
                                    <i class="fas fa-city"></i>
                                    <strong>Besar</strong>
                                    <span>>100 orang</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="calc-group mb-6">
                        <p class="calc-label">Kecepatan Proses</p>
                        <div class="calc-options">
                            <label class="calc-option">
                                <input type="radio" x-model="formData.urgency" value="normal" required>
                                <span class="calc-option-card">
                                    <i class="fas fa-clock"></i>
                                    <strong>Normal</strong>
                                    <span>Biaya standar</span>
                                </span>
                            </label>
                            <label class="calc-option">
                                <input type="radio" x-model="formData.urgency" value="fast">
                                <span class="calc-option-card">
                                    <i class="fas fa-bolt"></i>
                                    <strong>Cepat</strong>
                                    <span>Biaya +50%</span>
                                </span>
                            </label>
                            <label class="calc-option">
                                <input type="radio" x-model="formData.urgency" value="express">
                                <span class="calc-option-card">
                                    <i class="fas fa-rocket"></i>
                                    <strong>Express</strong>
                                    <span>Biaya +100%</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" :disabled="loading" class="btn btn-primary btn-lg w-full disabled:opacity-60 disabled:cursor-not-allowed">
                        <span x-show="!loading" x-cloak><i class="fas fa-calculator mr-2"></i>Hitung Estimasi</span>
                        <span x-show="loading" x-cloak><i class="fas fa-spinner fa-spin mr-2"></i>Menghitung...</span>
                    </button>
                </form>
            </section>

            <section class="space-y-4" aria-live="polite">
                <div class="result-summary" x-show="result" x-transition x-cloak>
                    <h3 class="text-xl font-bold mb-2">Estimasi Hasil</h3>
                    <p class="text-sm text-slate-200 mb-2">
                        Simulasi berdasarkan parameter yang Anda pilih.
                    </p>

                    <div class="result-metrics">
                        <div class="result-metric">
                            <p class="label">Estimasi Biaya</p>
                            <p class="value" x-text="'Rp ' + (result?.data?.estimated_cost || '0')"></p>
                        </div>
                        <div class="result-metric">
                            <p class="label">Estimasi Waktu</p>
                            <p class="value" x-text="(result?.data?.estimated_timeline || '0') + ' hari'"></p>
                        </div>
                    </div>

                    <div class="result-metric mt-3">
                        <p class="label">Tingkat Kompleksitas</p>
                        <p class="value" x-text="result?.data?.complexity || '-'" style="font-size:1.05rem"></p>
                    </div>
                </div>

                <div class="calc-panel" x-show="result" x-transition x-cloak>
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Dokumen yang Dibutuhkan</h3>
                    <ul class="result-docs">
                        <template x-for="doc in result?.data?.documents" :key="doc">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span x-text="doc"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                <div class="calc-panel" x-show="!result" x-cloak>
                    <div class="result-placeholder">
                        Hasil estimasi akan muncul di sini setelah Anda menekan tombol Hitung Estimasi.
                    </div>
                </div>

                <div class="calc-panel text-center">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Butuh Angka yang Lebih Presisi?</h3>
                    <p class="text-sm text-slate-600 mb-5">
                        Tim Bizmark dapat validasi kebutuhan izin, strategi dokumen, dan estimasi biaya final sesuai kondisi bisnis Anda.
                    </p>
                    @php
                        $whatsappBase = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');
                        $whatsappText = 'Halo, saya ingin konsultasi hasil dari kalkulator perizinan.';
                        $whatsappHref = $whatsappBase . (str_contains($whatsappBase, '?') ? '&' : '?') . 'text=' . rawurlencode($whatsappText);
                    @endphp
                    <a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="btn btn-secondary">
                        <i class="fab fa-whatsapp"></i>
                        Lanjut Konsultasi via WhatsApp
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
function calculatorApp() {
    return {
        formData: {
            industry: '',
            permit_type_id: '',
            city: '',
            company_size: '',
            urgency: ''
        },
        result: null,
        errorMessage: '',
        loading: false,

        async calculate() {
            this.loading = true;
            this.errorMessage = '';
            
            try {
                const response = await fetch('{{ route("calculator.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.result = data;
                    
                    // Scroll to results
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.querySelector('[x-show="result"]').offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }, 100);
                    
                    // Track conversion
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'calculator_complete', {
                            'event_category': 'Tools',
                            'event_label': 'Permit Calculator'
                        });
                    }
                } else {
                    this.errorMessage = 'Data belum lengkap. Silakan periksa input Anda.';
                }
            } catch (error) {
                console.error('Error:', error);
                this.errorMessage = 'Terjadi kendala saat menghitung estimasi. Silakan coba lagi dalam beberapa saat.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
