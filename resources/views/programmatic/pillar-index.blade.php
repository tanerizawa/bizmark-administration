@extends('landing.layout')

@section('title', 'Panduan Lengkap Perizinan ' . $year . ' | Bizmark.ID')
@section('meta_title', 'Panduan Lengkap Perizinan Lingkungan & Industri ' . $year . ' | Bizmark.ID')
@section('meta_description', 'Kumpulan panduan lengkap (pillar guide) untuk semua layanan perizinan lingkungan dan industri. Pelajari persyaratan, prosedur, dan tips.')
@section('meta_keywords', 'panduan perizinan, panduan amdal, panduan ukl-upl, panduan limbah b3, panduan oss nib, panduan pbg slf')

@section('content')

<section class="relative overflow-hidden pt-28 pb-16 bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <span class="eyebrow mb-4">Panduan</span>
        <h1 class="display-lg mb-4">Panduan Lengkap Perizinan {{ $year }}</h1>
        <p class="text-lg leading-relaxed text-gray-600 mb-8" style="margin-left:0;">Koleksi panduan mendalam (pillar guide) untuk setiap jenis perizinan. Dari dasar hukum hingga tips praktis.</p>
        <a href="{{ route('services.index.id') }}" class="btn btn-ghost"><i class="fas fa-layer-group"></i> Lihat Layanan</a>
    </div>
</section>

<section class="section">
    <div class="container-wide">
        @if($clusters->isEmpty())
            <div class="premium-card text-center">
                <h2 class="text-lg font-bold mb-2 text-gray-900 dark:text-white">Panduan sedang disiapkan</h2>
                <p class="mb-0 text-gray-600">Kunjungi kembali nanti untuk melihat pembaruan.</p>
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($clusters as $cluster)
                    <a href="{{ url('/panduan/' . $cluster['slug']) }}" class="premium-card h-full flex flex-col">
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="chip"><i class="fas fa-sitemap"></i> {{ $cluster['subtopics_count'] }} subtopik</span>
                            <span class="chip"><i class="fas fa-file-alt"></i> {{ $cluster['articles_count'] }} artikel</span>
                        </div>
                        <h2 class="text-lg font-bold mb-2 card-title text-gray-900 dark:text-white">{{ $cluster['title'] }}</h2>
                        <p class="text-sm mb-4 text-gray-600">{{ $cluster['description'] }}</p>
                        <div class="mt-auto pt-3 border-t border-gray-200">
                            <span class="text-xs text-gray-400"><i class="fas fa-eye mr-1"></i>{{ number_format($cluster['total_views']) }} views</span>
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
