@extends('landing.layout')

@section('title', 'Perbandingan Layanan Perizinan | Bizmark.ID')
@section('meta_title', 'Perbandingan Layanan Perizinan Lingkungan & Industri 2026 | Bizmark.ID')
@section('meta_description', 'Panduan perbandingan lengkap berbagai jenis perizinan: AMDAL vs UKL-UPL, Limbah B3 vs AMDAL, dan lainnya. Pahami perbedaan sebelum mengurus izin.')
@section('meta_keywords', 'perbandingan perizinan, perbedaan amdal ukl-upl, perbedaan perizinan, panduan memilih izin')

@section('content')

{{-- Breadcrumbs --}}
<section class="pt-24 pb-4 px-4 bg-white border-b border-gray-100">
    <div class="container mx-auto max-w-6xl">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <a href="/" class="hover:text-primary transition"><i class="fas fa-home"></i></a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('services.index.id') }}" class="hover:text-primary transition">Layanan</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium">Perbandingan</span>
        </nav>
    </div>
</section>

{{-- Hero --}}
<section class="py-12 md:py-16 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
    <div class="container mx-auto max-w-4xl px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-extrabold mb-4">Perbandingan Layanan Perizinan</h1>
        <p class="text-lg text-gray-300 max-w-2xl mx-auto">Pahami perbedaan antara berbagai jenis perizinan lingkungan dan industri sebelum Anda memulai proses pengurusan.</p>
    </div>
</section>

{{-- Comparison Cards --}}
<section class="py-12 md:py-16 bg-white">
    <div class="container mx-auto max-w-6xl px-4">
        <div class="grid sm:grid-cols-2 gap-6">
            @foreach($pairs as $slug => $pair)
            <a href="{{ url('/layanan/perbandingan/' . $slug) }}"
               class="group block bg-white rounded-xl border border-gray-200 p-6 hover:border-sky-300 hover:shadow-lg transition">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background-color: {{ $pair['a']['color'] ?? '#0f172a' }}">
                        <i class="fas {{ $pair['a']['icon'] ?? 'fa-file-alt' }}"></i>
                    </div>
                    <span class="text-lg font-bold text-gray-400">VS</span>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background-color: {{ $pair['b']['color'] ?? '#0f172a' }}">
                        <i class="fas {{ $pair['b']['icon'] ?? 'fa-file-alt' }}"></i>
                    </div>
                </div>
                <h2 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary transition">
                    {{ $pair['a']['title'] }} vs {{ $pair['b']['title'] }}
                </h2>
                <p class="text-sm text-gray-600">Perbedaan, persyaratan, dan kapan Anda membutuhkan masing-masing layanan.</p>
                <div class="mt-4 text-sm font-medium text-sky-600 group-hover:text-sky-700">
                    Baca Perbandingan <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-16 bg-gradient-to-r from-slate-900 to-slate-800 text-white text-center">
    <div class="container mx-auto max-w-3xl px-4">
        <h2 class="text-2xl md:text-3xl font-bold mb-4">Masih Bingung Memilih?</h2>
        <p class="text-gray-300 mb-8">Konsultasi gratis dengan tim ahli kami untuk menentukan perizinan yang tepat untuk usaha Anda.</p>
        <a href="https://wa.me/6283879602855?text={{ urlencode('Halo Bizmark, saya ingin konsultasi tentang perizinan yang tepat untuk usaha saya') }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white text-lg font-semibold rounded-xl transition shadow-lg shadow-green-500/20">
            <i class="fab fa-whatsapp mr-3 text-xl"></i> Konsultasi Gratis
        </a>
    </div>
</section>

@endsection
