<!-- Generic Service Content - Coming Soon -->

@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappBase = $contact['whatsapp_link'] ?? '';
    $supportEmail = $contact['email'] ?? '';
    $phoneRaw = $contact['phone'] ?? '';
    $phoneHref = $phoneRaw !== '' ? ('tel:' . preg_replace('/\s+/', '', $phoneRaw)) : '';
    $whatsappText = 'Halo, saya ingin tahu lebih lanjut tentang ' . ($service['title'] ?? 'layanan ini');
    $whatsappHref = $whatsappBase . (str_contains($whatsappBase, '?') ? '&' : '?') . 'text=' . rawurlencode($whatsappText);
@endphp

<div class="text-center py-12" data-aos="fade-up">
    <div class="w-24 h-24 rounded-full mx-auto mb-6 flex items-center justify-center" style="background: linear-gradient(135deg, {{ $service['color'] }}20 0%, {{ $service['color'] }}40 100%);">
        <i class="fas {{ $service['icon'] }} text-5xl" style="color: {{ $service['color'] }};"></i>
    </div>
    
    <h2 class="text-3xl font-bold text-gray-900 mb-4">Halaman Sedang Dalam Pengembangan</h2>
    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
        Kami sedang menyiapkan informasi lengkap tentang <strong>{{ $service['title'] }}</strong>. 
        Untuk informasi lebih detail dan konsultasi, silakan hubungi tim kami.
    </p>
    
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="btn btn-primary px-8 py-3">
            <i class="fab fa-whatsapp mr-2"></i>
            Konsultasi via WhatsApp
        </a>
        <a href="{{ $phoneHref }}" class="btn bg-white border-2 border-gray-300 text-gray-700 hover:border-primary hover:text-primary px-8 py-3">
            <i class="fas fa-phone mr-2"></i>
            Telepon Kami
        </a>
    </div>
</div>

<!-- Temporary Info Card -->
<div class="card bg-blue-50 border-2 border-blue-200" data-aos="fade-up">
    <div class="flex items-start gap-4">
        <i class="fas fa-info-circle text-3xl text-blue-600"></i>
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Butuh Informasi Segera?</h3>
            <p class="text-gray-600 mb-4">
                Tim ahli kami siap memberikan penjelasan lengkap tentang layanan {{ $service['title'] }}, 
                persyaratan dokumen, estimasi waktu, dan biaya.
            </p>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    Konsultasi gratis tanpa kewajiban
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    Penawaran harga terbaik dan kompetitif
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    Estimasi waktu pengerjaan yang akurat
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    Panduan lengkap persyaratan dokumen
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Quick Contact -->
<div class="grid md:grid-cols-3 gap-4" data-aos="fade-up">
    <a href="{{ $whatsappBase }}" target="_blank" rel="noopener" class="card hover:shadow-lg transition text-center group">
        <i class="fab fa-whatsapp text-4xl text-green-600 mb-3 group-hover:scale-110 transition-transform"></i>
        <h4 class="font-bold text-gray-900 mb-1">WhatsApp</h4>
        <p class="text-sm text-gray-600">Chat langsung dengan tim</p>
    </a>
    
    <a href="{{ $phoneHref }}" class="card hover:shadow-lg transition text-center group">
        <i class="fas fa-phone text-4xl text-blue-600 mb-3 group-hover:scale-110 transition-transform"></i>
        <h4 class="font-bold text-gray-900 mb-1">Telepon</h4>
        <p class="text-sm text-gray-600">{{ $phoneRaw }}</p>
    </a>
    
    <a href="mailto:{{ $supportEmail }}" class="card hover:shadow-lg transition text-center group">
        <i class="fas fa-envelope text-4xl text-red-600 mb-3 group-hover:scale-110 transition-transform"></i>
        <h4 class="font-bold text-gray-900 mb-1">Email</h4>
        <p class="text-sm text-gray-600">{{ $supportEmail }}</p>
    </a>
</div>
