@extends('landing.layout')

@section('title', 'Panduan Lengkap Perizinan ' . $year . ' | Bizmark.ID')
@section('meta_title', 'Panduan Lengkap Perizinan Lingkungan & Industri ' . $year . ' | Bizmark.ID')
@section('meta_description', 'Kumpulan panduan lengkap (pillar guide) untuk semua layanan perizinan lingkungan dan industri. Pelajari persyaratan, prosedur, dan tips.')
@section('meta_keywords', 'panduan perizinan, panduan amdal, panduan ukl-upl, panduan limbah b3, panduan oss nib, panduan pbg slf')

@section('content')

{{-- Breadcrumbs --}}
<section class="pt-24 pb-4 px-4 bg-white border-b border-gray-100">
    <div class="container mx-auto max-w-6xl">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <a href="/" class="hover:text-primary transition"><i class="fas fa-home"></i></a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium">Panduan</span>
        </nav>
    </div>
</section>

{{-- Hero --}}
<section class="py-12 md:py-16 bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-900 text-white">
    <div class="container mx-auto max-w-4xl px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-extrabold mb-4">Panduan Lengkap Perizinan {{ $year }}</h1>
        <p class="text-lg text-emerald-100 max-w-2xl mx-auto">Koleksi panduan mendalam (pillar guide) untuk setiap jenis perizinan. Dari dasar hukum hingga tips praktis.</p>
    </div>
</section>

{{-- Pillar Cards --}}
<section class="py-12 md:py-16 bg-white">
    <div class="container mx-auto max-w-6xl px-4">
        @if($clusters->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">Panduan sedang dalam tahap persiapan. Kunjungi kembali nanti.</p>
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($clusters as $cluster)
                <a href="{{ url('/panduan/' . $cluster['slug']) }}" class="group block bg-white rounded-xl border border-gray-200 p-6 hover:border-emerald-300 hover:shadow-lg transition">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                            {{ $cluster['subtopics_count'] }} subtopik
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            {{ $cluster['articles_count'] }} artikel
                        </span>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition">
                        {{ $cluster['title'] }}
                    </h2>
                    <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $cluster['description'] }}</p>
                    <div class="flex items-center text-sm text-gray-400">
                        <i class="fas fa-eye mr-1"></i> {{ number_format($cluster['total_views']) }} views
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "CollectionPage",
    "name": "Panduan Lengkap Perizinan {{ $year }}",
    "description": "Kumpulan panduan lengkap perizinan lingkungan dan industri di Indonesia.",
    "url": "{{ url('/panduan') }}",
    "publisher": {
        "@@type": "Organization",
        "name": "Bizmark.ID",
        "url": "{{ url('/') }}"
    }
}
</script>
@endsection
