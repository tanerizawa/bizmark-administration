@extends('landing.layout')

@section('title', 'FAQ Perizinan Lingkungan & Industri | Bizmark.ID')
@section('meta_title', 'FAQ Perizinan Lingkungan & Industri - Pertanyaan Umum | Bizmark.ID')
@section('meta_description', 'Kumpulan pertanyaan dan jawaban seputar perizinan lingkungan, AMDAL, UKL-UPL, Limbah B3, dan perizinan industri lainnya.')
@section('meta_keywords', 'faq perizinan, pertanyaan perizinan lingkungan, tanya jawab amdal, faq limbah b3, pertanyaan ukl upl')

@section('content')

{{-- Breadcrumbs --}}
<section class="pt-24 pb-4 px-4 bg-white border-b border-gray-100">
    <div class="container mx-auto max-w-6xl">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <a href="/" class="hover:text-primary transition"><i class="fas fa-home"></i></a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium">FAQ</span>
        </nav>
    </div>
</section>

{{-- Hero --}}
<section class="py-12 md:py-16 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
    <div class="container mx-auto max-w-4xl px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-extrabold mb-4">Pusat Bantuan & FAQ</h1>
        <p class="text-lg text-gray-300 max-w-2xl mx-auto">Temukan jawaban untuk pertanyaan umum seputar perizinan lingkungan dan industri di Indonesia.</p>
    </div>
</section>

{{-- Topic Cards --}}
<section class="py-12 md:py-16 bg-white">
    <div class="container mx-auto max-w-6xl px-4">
        <div class="grid sm:grid-cols-2 gap-6">
            @foreach($topics as $slug => $topic)
            <a href="{{ url('/faq/' . $topic['slug']) }}"
               class="group block bg-white rounded-xl border border-gray-200 p-6 hover:border-sky-300 hover:shadow-lg transition">
                <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center mb-4 group-hover:bg-sky-100 transition">
                    <i class="fas {{ $topic['icon'] }} text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition">{{ $topic['title'] }}</h2>
                <p class="text-sm text-gray-600 mb-3">{{ $topic['description'] }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">{{ $topic['faq_count'] }} artikel terkait</span>
                    <span class="text-sm font-medium text-sky-600 group-hover:text-sky-700">
                        Lihat FAQ <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-16 bg-gradient-to-r from-slate-900 to-slate-800 text-white text-center">
    <div class="container mx-auto max-w-3xl px-4">
        <h2 class="text-2xl md:text-3xl font-bold mb-4">Tidak Menemukan Jawaban?</h2>
        <p class="text-gray-300 mb-8">Hubungi tim kami untuk konsultasi langsung. Kami siap membantu menjawab semua pertanyaan Anda.</p>
        <a href="https://wa.me/6283879602855?text={{ urlencode('Halo Bizmark, saya memiliki pertanyaan tentang perizinan') }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white text-lg font-semibold rounded-xl transition shadow-lg shadow-green-500/20">
            <i class="fab fa-whatsapp mr-3 text-xl"></i> Tanya Langsung
        </a>
    </div>
</section>

@endsection
