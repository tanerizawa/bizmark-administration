@extends('landing.layout')

@section('title', $title ?? $service['title'] . ' - Bizmark.ID')
@section('meta_description', $meta_description ?? $service['short_description'])
@section('meta_keywords', $service['meta_keywords'] ?? '')

@section('content')

<!-- Breadcrumb -->
<section class="bg-gray-50 py-6 mt-20">
    <div class="container">
        <nav class="flex items-center text-sm text-gray-600" aria-label="Breadcrumb">
            <a href="{{ route('landing.id') }}" class="hover:text-primary transition">
                <i class="fas fa-home mr-2"></i>Beranda
            </a>
            <i class="fas fa-chevron-right mx-3 text-gray-400 text-xs"></i>
            <a href="{{ route('services.index.id') }}" class="hover:text-primary transition">Layanan</a>
            <i class="fas fa-chevron-right mx-3 text-gray-400 text-xs"></i>
            <span class="text-gray-900 font-medium">{{ $service['title'] }}</span>
        </nav>
    </div>
</section>

<!-- Hero Section -->
<section class="section bg-gradient-to-br from-white via-gray-50 to-white pt-12 pb-16">
    <div class="container">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row items-start gap-8" data-aos="fade-up">
                <!-- Icon -->
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-3xl flex items-center justify-center flex-shrink-0 shadow-lg" style="background: linear-gradient(135deg, {{ $service['color'] }}20 0%, {{ $service['color'] }}40 100%);">
                    <i class="fas {{ $service['icon'] }} text-4xl md:text-5xl" style="color: {{ $service['color'] }};"></i>
                </div>
                
                <!-- Content -->
                <div class="flex-1">
                    <span class="inline-block px-4 py-1 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">
                        Layanan Profesional
                    </span>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-gray-900">
                        {{ $service['title'] }}
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 leading-relaxed mb-6">
                        {{ $service['short_description'] }}
                    </p>
                    
                    <!-- Quick Actions -->
                    <div class="flex flex-wrap gap-3">
                        <a href="https://wa.me/6283879602855?text=Halo,%20saya%20tertarik%20dengan%20layanan%20{{ urlencode($service['title']) }}" target="_blank" class="btn btn-primary">
                            <i class="fab fa-whatsapp mr-2"></i>
                            Konsultasi Gratis
                        </a>
                        @if(!in_array($service['slug'], ['perizinan-lb3', 'amdal']))
                            <a href="tel:+6283879602855" class="btn bg-white border-2 border-gray-300 text-gray-700 hover:border-primary hover:text-primary">
                                <i class="fas fa-phone mr-2"></i>
                                Telepon Kami
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="section bg-white">
    <div class="container">
        <div class="max-w-5xl mx-auto">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content Column -->
                <div class="lg:col-span-2 space-y-12">
                    
                    <!-- Narrative Introduction -->
                    @if(!view()->exists('services.partials.' . $service['slug']))
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-l-4 border-blue-600 rounded-r-xl p-8 shadow-sm">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-lightbulb text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">Memahami {{ $service['title'] }}</h2>
                                <p class="text-sm text-blue-900 font-medium">Panduan komprehensif untuk pengusaha Indonesia</p>
                            </div>
                        </div>
                        
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">{{ $service['short_description'] }}</p>
                            
                            <p>Menjalankan usaha di Indonesia memerlukan pemahaman mendalam tentang regulasi dan prosedur perizinan yang berlaku. Meskipun pemerintah telah melakukan berbagai penyederhanaan melalui sistem digital, keberhasilan dalam mendapatkan izin tetap membutuhkan keahlian lokal, hubungan yang baik dengan instansi terkait, dan manajemen kepatuhan yang proaktif.</p>
                            
                            <p><strong>Mengapa pendampingan profesional penting:</strong> Regulasi Indonesia sering memberikan kerangka umum, dengan persyaratan detail muncul melalui peraturan menteri, pedoman teknis, dan interpretasi yang terus berkembang. Apa yang terlihat sederhana dalam perundang-undangan bisa melibatkan persyaratan kompleks dalam praktiknya. Konsultan profesional menjembatani kesenjangan antara teks regulasi dan realitas operasional.</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">Pengalaman kami melayani klien di berbagai industri telah membangun pengetahuan komprehensif tentang lanskap regulasi Indonesia. Kami tidak hanya memproses aplikasi—kami memposisikan bisnis Anda secara strategis untuk kesuksesan operasional jangka panjang, membantu Anda menghindari jebakan umum yang menunda atau menggagalkan proyek perizinan.</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Overview (Dynamically loaded based on slug) -->
                    @if(view()->exists('services.partials.' . $service['slug']))
                    @include('services.partials.' . $service['slug'])
                    @else
                    <!-- Default Service Content for services without custom partial -->
                    <div class="space-y-8">
                        <!-- Service Description -->
                        <div>
                            <h2 class="text-2xl font-bold mb-4 text-gray-900">Gambaran Layanan</h2>
                            <div class="prose max-w-none text-gray-600 leading-relaxed">
                                <p>{{ $service['short_description'] }}</p>
                                @if(isset($service['long_description']))
                                <p class="mt-4">{{ $service['long_description'] }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Process/Benefits -->
                        @if(isset($service['process_time']))
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div class="p-6 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <i class="fas fa-clock text-2xl" style="color: {{ $service['color'] }};"></i>
                                    <span class="text-sm text-gray-600">Waktu Proses</span>
                                </div>
                                <p class="text-xl font-bold text-gray-900">{{ $service['process_time'] }}</p>
                            </div>
                            @if(isset($service['price']))
                            <div class="p-6 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <i class="fas fa-tag text-2xl text-green-600"></i>
                                    <span class="text-sm text-gray-600">Harga Mulai</span>
                                </div>
                                <p class="text-xl font-bold text-gray-900">{{ $service['price'] }}</p>
                            </div>
                            @endif
                            <div class="p-6 bg-gradient-to-br from-purple-50 to-white rounded-xl border border-purple-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <i class="fas fa-shield-alt text-2xl text-purple-600"></i>
                                    <span class="text-sm text-gray-600">Jaminan</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900">Transparan</p>
                            </div>
                        </div>
                        @endif

                        <!-- Key Features -->
                        <div>
                            <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                                <i class="fas fa-check-circle mr-3" style="color: {{ $service['color'] }};"></i>
                                Yang Anda Dapatkan
                            </h2>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-green-50 to-white rounded-lg border border-green-100">
                                    <i class="fas fa-check-double text-green-600 mt-1 flex-shrink-0"></i>
                                    <span class="text-gray-700">Konsultasi awal dan assessment kebutuhan</span>
                                </div>
                                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-green-50 to-white rounded-lg border border-green-100">
                                    <i class="fas fa-check-double text-green-600 mt-1 flex-shrink-0"></i>
                                    <span class="text-gray-700">Persiapan dan kurasi dokumen lengkap</span>
                                </div>
                                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-green-50 to-white rounded-lg border border-green-100">
                                    <i class="fas fa-check-double text-green-600 mt-1 flex-shrink-0"></i>
                                    <span class="text-gray-700">Pengajuan dan koordinasi dengan instansi</span>
                                </div>
                                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-green-50 to-white rounded-lg border border-green-100">
                                    <i class="fas fa-check-double text-green-600 mt-1 flex-shrink-0"></i>
                                    <span class="text-gray-700">Monitoring progress secara real-time</span>
                                </div>
                                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-green-50 to-white rounded-lg border border-green-100">
                                    <i class="fas fa-check-double text-green-600 mt-1 flex-shrink-0"></i>
                                    <span class="text-gray-700">Pendampingan hingga izin terbit</span>
                                </div>
                                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-green-50 to-white rounded-lg border border-green-100">
                                    <i class="fas fa-check-double text-green-600 mt-1 flex-shrink-0"></i>
                                    <span class="text-gray-700">Dukungan pasca-penerbitan izin</span>
                                </div>
                            </div>
                        </div>

                        <!-- Process Steps -->
                        <div>
                            <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                                <i class="fas fa-route mr-3" style="color: {{ $service['color'] }};"></i>
                                Proses Kami
                            </h2>
                            <div class="space-y-4">
                                @foreach(['Konsultasi & Analisis Kebutuhan', 'Persiapan Dokumen & Persyaratan', 'Pengajuan ke Instansi Terkait', 'Monitoring & Follow-up', 'Penyerahan Izin & Pelaporan'] as $index => $step)
                                <div class="flex gap-4 p-5 bg-gradient-to-r from-blue-50 to-white rounded-lg border border-blue-100 hover:shadow-md transition">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-blue-900 to-blue-700 text-white flex items-center justify-center font-bold text-lg shadow-lg">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1 pt-2">
                                        <h3 class="font-semibold text-gray-900">{{ $step }}</h3>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div>
                            <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                                <i class="fas fa-file-alt mr-3 text-yellow-600"></i>
                                Dokumen Yang Diperlukan
                            </h2>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                                <ul class="space-y-3">
                                    <li class="flex items-start gap-3">
                                        <i class="fas fa-file-alt text-yellow-600 mt-1"></i>
                                        <span class="text-gray-700">Akta pendirian perusahaan dan perubahannya</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <i class="fas fa-file-alt text-yellow-600 mt-1"></i>
                                        <span class="text-gray-700">NIB (Nomor Induk Berusaha)</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <i class="fas fa-file-alt text-yellow-600 mt-1"></i>
                                        <span class="text-gray-700">NPWP Perusahaan dan Direktur</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <i class="fas fa-file-alt text-yellow-600 mt-1"></i>
                                        <span class="text-gray-700">Surat keterangan domisili usaha</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <i class="fas fa-file-alt text-yellow-600 mt-1"></i>
                                        <span class="text-gray-700">Dokumen teknis sesuai jenis layanan</span>
                                    </li>
                                </ul>
                                <div class="mt-4 p-3 bg-white rounded-lg text-sm text-gray-600">
                                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                    Persyaratan lengkap akan dijelaskan saat konsultasi awal sesuai kebutuhan spesifik bisnis Anda.
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                </div>
                
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        
                        <!-- Contextual Sidebar Card -->
                        @if($service['slug'] === 'perizinan-lb3')
                            <div class="card border-2" style="border-color: {{ $service['color'] }}22;">
                                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-clipboard-check mr-2" style="color: {{ $service['color'] }};"></i>
                                    Ringkasan Pengajuan
                                </h3>
                                <ul class="space-y-3 text-sm text-gray-700">
                                    <li class="flex items-start"><i class="fas fa-clock mr-2" style="color: {{ $service['color'] }};"></i><span>Timeline OSS ±30–60 hari kerja setelah dokumen lengkap.</span></li>
                                    <li class="flex items-start"><i class="fas fa-folder-open mr-2" style="color: {{ $service['color'] }};"></i><span>Dokumen kunci: NIB, UKL-UPL/AMDAL, layout & foto TPS B3, SOP darurat.</span></li>
                                    <li class="flex items-start"><i class="fas fa-hard-hat mr-2" style="color: {{ $service['color'] }};"></i><span>Inspeksi lapangan: siapkan PIC, logbook, dan sarpras sesuai standar.</span></li>
                                    <li class="flex items-start"><i class="fas fa-file-signature mr-2" style="color: {{ $service['color'] }};"></i><span>Kewajiban pasca-izin: manifest, laporan berkala, kontrak mitra berizin.</span></li>
                                </ul>
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                                    <i class="fas fa-info-circle mr-2" style="color: {{ $service['color'] }};"></i>
                                    Lihat checklist lengkap di bawah atau unduh template internal Anda sendiri.
                                </div>
                                <div class="mt-4 flex items-center text-sm text-gray-600">
                                    <i class="fab fa-whatsapp text-lg mr-2" style="color: {{ $service['color'] }};"></i>
                                    <a href="https://wa.me/6283879602855?text=Halo,%20saya%20ingin%20membahas%20perizinan%20Limbah%20B3" target="_blank" class="text-primary hover:underline">
                                        Diskusi teknis via WhatsApp
                                    </a>
                                </div>
                            </div>
                        @elseif($service['slug'] === 'amdal')
                            <div class="card border-2" style="border-color: {{ $service['color'] }}22;">
                                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-project-diagram mr-2" style="color: {{ $service['color'] }};"></i>
                                    Ringkasan Penyusunan AMDAL
                                </h3>
                                <ul class="space-y-3 text-sm text-gray-700">
                                    <li class="flex items-start"><i class="fas fa-stream mr-2" style="color: {{ $service['color'] }};"></i><span>Tahapan: KA-ANDAL → ANDAL → RKL → RPL dengan konsultasi publik.</span></li>
                                    <li class="flex items-start"><i class="fas fa-users mr-2" style="color: {{ $service['color'] }};"></i><span>Tim wajib bersertifikat dan mencakup ahli fisik, biotik, sosial, dan K3.</span></li>
                                    <li class="flex items-start"><i class="fas fa-map mr-2" style="color: {{ $service['color'] }};"></i><span>Kumpulkan data baseline: kualitas udara/air, flora-fauna, sosial-ekonomi.</span></li>
                                    <li class="flex items-start"><i class="fas fa-file-signature mr-2" style="color: {{ $service['color'] }};"></i><span>Output akhir: persetujuan lingkungan + kewajiban pelaporan RKL-RPL.</span></li>
                                </ul>
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                                    <i class="fas fa-info-circle mr-2" style="color: {{ $service['color'] }};"></i>
                                    Lihat panduan lengkap di bagian "Proses" dan "Checklist Baseline" di halaman ini.
                                </div>
                                <div class="mt-4 flex items-center text-sm text-gray-600">
                                    <i class="fab fa-whatsapp text-lg mr-2" style="color: {{ $service['color'] }};"></i>
                                    <a href="https://wa.me/6283879602855?text=Halo,%20saya%20ingin%20membahas%20penyusunan%20AMDAL" target="_blank" class="text-primary hover:underline">
                                        Konsultasi tahapan AMDAL
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="card bg-gradient-to-br from-primary to-primary-dark text-white">
                                <h3 class="text-xl font-bold mb-4 flex items-center">
                                    <i class="fas fa-headset mr-2"></i>
                                    Butuh Bantuan?
                                </h3>
                                <p class="text-white/90 text-sm mb-4">
                                    Tim kami siap membantu Anda 24/7
                                </p>
                                <div class="space-y-3 mb-6">
                                    <a href="https://wa.me/6283879602855" target="_blank" class="flex items-center text-white hover:text-white/80 transition">
                                        <i class="fab fa-whatsapp text-xl mr-3"></i>
                                        <div>
                                            <div class="text-xs text-white/70">WhatsApp</div>
                                            <div class="font-semibold">+62 813-8260-5030</div>
                                        </div>
                                    </a>
                                    <a href="tel:+6283879602855" class="flex items-center text-white hover:text-white/80 transition">
                                        <i class="fas fa-phone text-xl mr-3"></i>
                                        <div>
                                            <div class="text-xs text-white/70">Telepon</div>
                                            <div class="font-semibold">+62 813-8260-5030</div>
                                        </div>
                                    </a>
                                    <a href="mailto:cs@bizmark.id" class="flex items-center text-white hover:text-white/80 transition">
                                        <i class="fas fa-envelope text-xl mr-3"></i>
                                        <div>
                                            <div class="text-xs text-white/70">Email</div>
                                            <div class="font-semibold text-sm">cs@bizmark.id</div>
                                        </div>
                                    </a>
                                </div>
                                <a href="#contact" class="btn bg-white text-primary hover:bg-gray-100 w-full justify-center">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Kirim Pesan
                                </a>
                            </div>
                        @endif
                        
                        <!-- Other Services -->
                        @if(count($relatedServices) > 0)
                        <div class="card">
                            <h3 class="text-lg font-bold mb-4 text-gray-900">Layanan Lainnya</h3>
                            <div class="space-y-3">
                                @foreach($relatedServices as $slug => $related)
                                @php
                                    $serviceRoute = app()->getLocale() === 'en' ? route('services.show.en', $slug) : route('services.show.id', $slug);
                                @endphp
                                <a href="{{ $serviceRoute }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition group">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: {{ $related['color'] }}20;">
                                        <i class="fas {{ $related['icon'] }} text-sm" style="color: {{ $related['color'] }};"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-gray-900 group-hover:text-primary transition truncate">
                                            {{ $related['title'] }}
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-xs text-gray-400 group-hover:text-primary transition"></i>
                                </a>
                                @endforeach
                            </div>
                            <a href="{{ route('services.index.id') }}" class="btn bg-gray-100 hover:bg-gray-200 text-gray-700 w-full justify-center mt-4">
                                Lihat Layanan
                            </a>
                        </div>
                        @endif
                        
                        <!-- Support Card (contextual) -->
                        @if($service['slug'] === 'perizinan-lb3')
                        <div class="card bg-gray-50 border-2 border-dashed border-gray-300">
                            <div class="text-center">
                                <i class="fas fa-clipboard-check text-4xl" style="color: {{ $service['color'] }};"></i>
                                <h4 class="font-bold text-gray-900 mb-2">Checklist Persiapan</h4>
                                <p class="text-sm text-gray-600 mb-4">Cek dokumen & sarpras sebelum pengajuan</p>
                                <a href="#checklist-lb3" class="btn btn-primary w-full justify-center text-sm">
                                    <i class="fas fa-list mr-2"></i>
                                    Lihat Checklist
                                </a>
                            </div>
                        </div>
                        @elseif($service['slug'] === 'amdal')
                        <div class="card bg-gray-50 border-2 border-dashed border-gray-300">
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt text-4xl" style="color: {{ $service['color'] }};"></i>
                                <h4 class="font-bold text-gray-900 mb-2">Checklist AMDAL</h4>
                                <p class="text-sm text-gray-600 mb-4">Pastikan data baseline & dokumen siap sebelum pengajuan</p>
                                <a href="#baseline" class="btn btn-primary w-full justify-center text-sm">
                                    <i class="fas fa-list mr-2"></i>
                                    Buka Checklist
                                </a>
                            </div>
                        </div>
                        @else
                        <div class="card bg-gray-50 border-2 border-dashed border-gray-300">
                            <div class="text-center">
                                <i class="fas fa-file-pdf text-4xl text-red-600 mb-3"></i>
                                <h4 class="font-bold text-gray-900 mb-2">Download Brosur</h4>
                                <p class="text-sm text-gray-600 mb-4">Informasi lengkap layanan kami</p>
                                <button onclick="alert('Fitur download akan segera tersedia')" class="btn btn-primary w-full justify-center text-sm">
                                    <i class="fas fa-download mr-2"></i>
                                    Download PDF
                                </button>
                            </div>
                        </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($service['slug'] === 'perizinan-lb3')
    @include('services.partials._next-steps-lb3')
@elseif($service['slug'] === 'amdal')
    @include('services.partials._next-steps-amdal')
@else
    <!-- CTA Section -->
    <section class="section bg-gradient-to-br from-gray-900 to-gray-800 text-white">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">
                    Siap Memulai dengan {{ $service['title'] }}?
                </h2>
                <p class="text-lg md:text-xl mb-8 text-white/80">
                    Hubungi kami sekarang untuk konsultasi gratis dan penawaran terbaik
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="https://wa.me/6283879602855?text=Halo,%20saya%20ingin%20konsultasi%20tentang%20{{ urlencode($service['title']) }}" target="_blank" class="btn bg-secondary hover:bg-secondary-dark px-8 py-4 text-lg">
                        <i class="fab fa-whatsapp mr-2"></i>
                        Konsultasi Sekarang
                    </a>
                    <a href="{{ route('services.index.id') }}" class="btn bg-white/10 hover:bg-white/20 text-white border border-white/30 px-8 py-4 text-lg">
                        <i class="fas fa-th-large mr-2"></i>
                        Lihat Layanan Lain
                    </a>
                </div>
            </div>
        </div>
    </section>
@endif

@endsection
