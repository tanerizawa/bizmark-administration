@extends('mobile-landing.layouts.content')

@section('title', $vacancy->title . ' - Karir Bizmark.ID')
@section('meta_description', $vacancy->summary ?? 'Lamar posisi ' . $vacancy->title . ' di Bizmark.ID')

@section('content')

<!-- Hero Section -->
<section class="magazine-section bg-gradient-to-br from-[#0077B5] to-[#005582] text-white">
    <div class="content-container">
        <!-- Back Button -->
        <a href="{{ route('career.index') }}" class="inline-flex items-center gap-2 text-sm text-white/90 mb-6">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Daftar Lowongan</span>
        </a>
        
        <!-- Title & Status -->
        <div class="flex items-start justify-between gap-3 mb-4">
            <h1 class="headline text-3xl flex-1">
                {{ $vacancy->title }}
            </h1>
            @if($vacancy->is_urgent)
            <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full flex-shrink-0 flex items-center gap-1">
                <i class="fas fa-fire"></i> Urgent
            </span>
            @endif
        </div>
        
        <p class="text-base text-white/90 mb-4">
            <i class="fas fa-building mr-2"></i>{{ $vacancy->department }}
        </p>

        <!-- Key Info -->
        <div class="grid grid-cols-2 gap-3 bg-white/10 backdrop-blur-lg rounded-xl p-4">
            <div class="text-center">
                <i class="fas fa-map-marker-alt text-xl mb-1"></i>
                <div class="text-xs text-white/80">Lokasi</div>
                <div class="font-semibold">{{ $vacancy->location ?? 'Indonesia' }}</div>
            </div>
            <div class="text-center">
                <i class="fas fa-briefcase text-xl mb-1"></i>
                <div class="text-xs text-white/80">Tipe</div>
                <div class="font-semibold">{{ ucfirst(str_replace('-', ' ', $vacancy->employment_type ?? 'Full Time')) }}</div>
            </div>
            @if(isset($vacancy->position) && $vacancy->position)
            <div class="text-center">
                <i class="fas fa-chart-line text-xl mb-1"></i>
                <div class="text-xs text-white/80">Posisi</div>
                <div class="font-semibold">{{ $vacancy->position }}</div>
            </div>
            @endif
            @if($vacancy->salary_range && $vacancy->salary_range !== 'Not specified')
            <div class="text-center">
                <i class="fas fa-money-bill-wave text-xl mb-1"></i>
                <div class="text-xs text-white/80">Gaji</div>
                <div class="font-semibold text-sm">{{ $vacancy->salary_range }}</div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Summary -->
@if(isset($vacancy->summary) && $vacancy->summary)
<section class="magazine-section bg-white">
    <div class="content-container">
        <div class="bg-blue-50 border-l-4 border-[#0077B5] p-4 rounded-r-xl">
            <p class="text-sm text-gray-700 leading-relaxed">
                {{ $vacancy->summary }}
            </p>
        </div>
    </div>
</section>
@endif

<!-- Description -->
@if(isset($vacancy->description) && $vacancy->description)
<section class="magazine-section bg-gray-50">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-4">
            <i class="fas fa-file-alt text-[#0077B5] mr-2"></i>
            Deskripsi Pekerjaan
        </h2>
        <div class="bg-white rounded-xl p-5 shadow-sm">
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                {!! nl2br(e($vacancy->description)) !!}
            </div>
        </div>
    </div>
</section>
@endif

<!-- Responsibilities -->
@if(!empty($responsibilities))
<section class="magazine-section bg-white">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-4">
            <i class="fas fa-tasks text-[#0077B5] mr-2"></i>
            Tanggung Jawab
        </h2>
        <div class="space-y-3">
            @foreach($responsibilities as $responsibility)
            <div class="flex items-start gap-3 bg-gray-50 p-4 rounded-xl">
                <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                <span class="text-sm text-gray-700 flex-1">{{ $responsibility }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Qualifications -->
@if(!empty($qualifications))
<section class="magazine-section bg-gray-50">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-4">
            <i class="fas fa-graduation-cap text-[#0077B5] mr-2"></i>
            Kualifikasi
        </h2>
        <div class="space-y-3">
            @foreach($qualifications as $qualification)
            <div class="flex items-start gap-3 bg-white p-4 rounded-xl shadow-sm">
                <i class="fas fa-star text-yellow-500 mt-1 flex-shrink-0"></i>
                <span class="text-sm text-gray-700 flex-1">{{ $qualification }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Benefits -->
@if(!empty($benefits))
<section class="magazine-section bg-white">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-4">
            <i class="fas fa-gift text-[#0077B5] mr-2"></i>
            Benefit & Fasilitas
        </h2>
        <div class="grid gap-3">
            @foreach($benefits as $benefit)
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-700 font-medium">{{ $benefit }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Application CTA -->
<section class="magazine-section bg-gradient-to-br from-[#0077B5] to-[#005582] text-white">
    <div class="content-container text-center">
        <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-paper-plane text-3xl"></i>
        </div>
        <h2 class="headline text-3xl mb-3">
            Tertarik untuk Bergabung?
        </h2>
        <p class="text-base text-white/90 mb-6 leading-relaxed">
            Kirimkan CV dan surat lamaran Anda sekarang!
        </p>
        
        <div class="space-y-3">
            <a href="{{ route('career.apply', $vacancy->id) }}" 
               class="block w-full bg-white text-[#0077B5] font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-2xl transition-all">
                <i class="fas fa-file-upload mr-2"></i> Lamar Sekarang
            </a>
            <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID%2C%20saya%20ingin%20bertanya%20tentang%20posisi%20{{ urlencode($vacancy->title) }}" 
               target="_blank"
               class="block w-full bg-white/10 backdrop-blur text-white font-semibold py-4 px-6 rounded-xl border-2 border-white/30">
                <i class="fab fa-whatsapp mr-2"></i> Tanya via WhatsApp
            </a>
        </div>

        <p class="text-xs text-white/70 mt-6">
            @if(isset($vacancy->deadline) && $vacancy->deadline)
            Batas Akhir: {{ \Carbon\Carbon::parse($vacancy->deadline)->format('d F Y') }}
            @else
            Lamaran terbuka hingga kuota terpenuhi
            @endif
        </p>
    </div>
</section>

<!-- Other Positions -->
@php
    $otherVacancies = App\Models\JobVacancy::open()
        ->where('id', '!=', $vacancy->id)
        ->limit(3)
        ->get();
@endphp

@if($otherVacancies->count() > 0)
<section class="magazine-section bg-gray-50">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-6">
            Posisi Lainnya
        </h2>
        <div class="space-y-4">
            @foreach($otherVacancies as $other)
            <a href="{{ route('career.show', $other->slug) }}" 
               class="block bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <h3 class="font-bold text-gray-900">{{ $other->title }}</h3>
                    @if($other->is_urgent)
                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full flex-shrink-0">
                        Urgent
                    </span>
                    @endif
                </div>
                <p class="text-sm text-gray-600 mb-3">{{ $other->department }}</p>
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span><i class="fas fa-map-marker-alt text-[#0077B5] mr-1"></i>{{ $other->location }}</span>
                    <span><i class="fas fa-briefcase text-[#0077B5] mr-1"></i>{{ $other->type }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
