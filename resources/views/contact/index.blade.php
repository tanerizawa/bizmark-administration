@extends('landing.layout')

@php
    $isEnglish = app()->getLocale() === 'en';
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $phone = $contact['phone'] ?? '+62 838 7960 2855';
    $phoneHref = preg_replace('/\s+/', '', $phone);
    $email = $contact['email'] ?? 'info@bizmark.id';
    $waLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $hours = $contact['hours'] ?? ($isEnglish ? 'Available 24/7' : 'Portal Aktif 24/7');
@endphp

@section('title', $isEnglish ? 'Contact Us - Bizmark.ID' : 'Hubungi Kami - Bizmark.ID')
@section('meta_title', $isEnglish ? 'Contact Bizmark.ID' : 'Hubungi Bizmark.ID')
@section('meta_description', $isEnglish
    ? 'Contact Bizmark.ID for OSS, AMDAL, UKL-UPL, PBG/SLF, and industrial permit consultation.'
    : 'Hubungi Bizmark.ID untuk konsultasi OSS, AMDAL, UKL-UPL, PBG/SLF, dan perizinan industri.')

@section('content')
<section class="relative overflow-hidden pt-24 pb-14" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <div class="max-w-3xl">
            <span class="section-badge mb-4">{{ $isEnglish ? 'Contact' : 'Kontak' }}</span>
            <h1 class="section-title mb-4">{{ $isEnglish ? 'Talk to our permit experts' : 'Konsultasi dengan tim ahli perizinan' }}</h1>
            <p class="text-lg leading-relaxed mb-0" style="color:var(--text-secondary);">
                {{ $isEnglish ? 'Tell us about your business. We will help map the permits you need and the most efficient path to compliance.' : 'Ceritakan jenis usaha Anda. Kami bantu memetakan izin yang dibutuhkan dan jalur kepatuhan yang paling efisien.' }}
            </p>
        </div>
    </div>
</section>

<section class="section-sm" aria-label="{{ $isEnglish ? 'Contact channels' : 'Saluran Komunikasi' }}">
    <div class="container-wide">
        <div class="grid md:grid-cols-3 gap-6">
            <a href="tel:{{ $phoneHref }}" class="card hover-lift" aria-label="{{ $isEnglish ? 'Call us' : 'Telepon kami' }}">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:rgba(15,23,42,.06);">
                        <i class="fas fa-phone" style="color:var(--color-primary);"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold" style="color:var(--text-primary);">{{ $isEnglish ? 'Phone' : 'Telepon' }}</div>
                        <div class="text-sm" style="color:var(--text-secondary);">{{ $hours }}</div>
                        <div class="text-sm font-semibold mt-2" style="color:var(--text-primary);">{{ $phone }}</div>
                    </div>
                </div>
            </a>

            <a href="mailto:{{ $email }}" class="card hover-lift" aria-label="{{ $isEnglish ? 'Email us' : 'Email kami' }}">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:rgba(14,165,233,.12);">
                        <i class="fas fa-envelope" style="color:var(--color-accent);"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold" style="color:var(--text-primary);">Email</div>
                        <div class="text-sm" style="color:var(--text-secondary);">{{ $isEnglish ? 'Reply within 1 business day' : 'Respon dalam 1 hari kerja' }}</div>
                        <div class="text-sm font-semibold mt-2" style="color:var(--text-primary);">{{ $email }}</div>
                    </div>
                </div>
            </a>

            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="card hover-lift" aria-label="WhatsApp">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:rgba(22,163,74,.12);">
                        <i class="fab fa-whatsapp" style="color:var(--color-success);"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold" style="color:var(--text-primary);">WhatsApp</div>
                        <div class="text-sm" style="color:var(--text-secondary);">{{ $isEnglish ? 'Fast response for quick questions' : 'Respon cepat untuk pertanyaan singkat' }}</div>
                        <div class="text-sm font-semibold mt-2" style="color:var(--text-primary);">{{ $isEnglish ? 'Chat now' : 'Chat sekarang' }}</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<section class="section" aria-labelledby="contact-form-heading" style="background:var(--surface-warm);">
    <div class="container-wide">
        <div class="grid lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-5">
                <h2 id="contact-form-heading" class="section-title mb-4">{{ $isEnglish ? 'Send a message' : 'Kirim pesan' }}</h2>
                <p class="text-base leading-relaxed mb-8" style="color:var(--text-secondary);">
                    {{ $isEnglish ? 'Fill out the form and we will get back to you with a recommended next step.' : 'Isi formulir berikut dan tim kami akan menghubungi Anda dengan rekomendasi langkah selanjutnya.' }}
                </p>

                <div class="card">
                    <div class="text-sm font-semibold mb-2" style="color:var(--text-primary);">{{ $isEnglish ? 'Office base' : 'Basis kantor' }}</div>
                    <div class="text-sm mb-0" style="color:var(--text-secondary);">
                        {{ $isEnglish ? 'Karawang, West Java (remote-first consultations available)' : 'Karawang, Jawa Barat (konsultasi daring tersedia)' }}
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
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

                @if($errors->any())
                    <div class="card mb-6" style="border-color:rgba(220,38,38,.35);">
                        <div class="text-sm font-semibold mb-2" style="color:var(--text-primary);">{{ $isEnglish ? 'Please review the highlighted fields.' : 'Mohon periksa kembali input yang ditandai.' }}</div>
                        <ul class="text-sm m-0 pl-5" style="color:var(--text-secondary);">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" class="card">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label for="contact-name" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">
                                {{ $isEnglish ? 'Full name' : 'Nama lengkap' }} <span class="text-red-600">*</span>
                            </label>
                            <input id="contact-name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name"
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>

                        <div>
                            <label for="contact-email" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">
                                Email <span class="text-red-600">*</span>
                            </label>
                            <input id="contact-email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>

                        <div>
                            <label for="contact-phone" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">
                                {{ $isEnglish ? 'Phone' : 'Nomor telepon' }} <span class="text-red-600">*</span>
                            </label>
                            <input id="contact-phone" name="phone" type="text" value="{{ old('phone') }}" required autocomplete="tel"
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="contact-subject" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">
                                {{ $isEnglish ? 'Subject' : 'Subjek' }} <span class="text-red-600">*</span>
                            </label>
                            <input id="contact-subject" name="subject" type="text" value="{{ old('subject') }}" required
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="contact-message" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">
                                {{ $isEnglish ? 'Message' : 'Pesan' }} <span class="text-red-600">*</span>
                            </label>
                            <textarea id="contact-message" name="message" required rows="6"
                                      class="w-full rounded-lg border px-4 py-3 text-base"
                                      style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">{{ old('message') }}</textarea>
                            <div class="text-xs mt-2" style="color:var(--text-tertiary);">
                                {{ $isEnglish ? 'Max 2000 characters.' : 'Maksimal 2000 karakter.' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                        <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto">
                            <i class="fas fa-paper-plane"></i> {{ $isEnglish ? 'Send' : 'Kirim' }}
                        </button>
                        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-lg w-full sm:w-auto">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

