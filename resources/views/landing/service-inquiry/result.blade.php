<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <meta name="description" content="Hasil analisis perizinan untuk {{ $inquiry->company_name }}">
    <title>Hasil Analisis Perizinan - {{ $inquiry->inquiry_number }} | Bizmark.ID</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        linkedin: {
                            50: '#e7f3f8',
                            100: '#cce7f1',
                            200: '#99cfe3',
                            500: '#0077B5',
                            600: '#005582',
                            700: '#004161',
                            900: '#001820',
                        },
                        gold: {
                            400: '#F2CD49',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.6s ease forwards;
        }
        
        @keyframes slideUp {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .stagger-item {
            opacity: 0;
            animation: slideUp 0.5s ease forwards;
        }
        
        .stagger-item:nth-child(1) { animation-delay: 0.1s; }
        .stagger-item:nth-child(2) { animation-delay: 0.2s; }
        .stagger-item:nth-child(3) { animation-delay: 0.3s; }
        .stagger-item:nth-child(4) { animation-delay: 0.4s; }
        .stagger-item:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>
<body class="bg-gradient-to-br from-linkedin-50 via-white to-linkedin-100 min-h-screen">
    
    <div class="min-h-screen py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="max-w-5xl mx-auto mb-6 animate-fadeIn">
            <div class="text-center">
                <!-- Logo -->
                <a href="{{ route('landing') }}" class="inline-flex items-center justify-center mb-3">
                    <img src="{{ asset('images/logo.svg') }}" alt="Bizmark.ID Logo" class="h-12 sm:h-14">
                </a>
                
                <!-- Success Badge -->
                
                <h1 class="text-2xl sm:text-3xl font-bold text-linkedin-900 mb-1.5">
                    Hasil Analisis Perizinan
                </h1>
                <p class="text-base text-gray-600">
                    Untuk: <strong>{{ $inquiry->company_name }}</strong>
                </p>
                <p class="text-xs text-gray-500">
                    No. Inquiry: {{ $inquiry->inquiry_number }} · {{ $inquiry->created_at->format('d M Y, H:i') }} WIB
                </p>
            </div>
        </div>

        @php
            $analysis = $inquiry->ai_analysis ?? [];
            $permits = $analysis['recommended_permits'] ?? [];
            $totalCost = $analysis['total_estimated_cost'] ?? [];
            $riskFactors = $analysis['risk_factors'] ?? [];
            $nextSteps = $analysis['next_steps'] ?? [];
            $limitations = $analysis['limitations'] ?? '';
        @endphp

        <div class="max-w-5xl mx-auto space-y-5">
            
            <!-- Summary Card -->
            <div class="bg-gradient-to-r from-linkedin-500 to-linkedin-600 rounded-xl shadow-lg p-5 sm:p-6 text-white stagger-item">
                <h2 class="text-lg font-bold mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Ringkasan Analisis
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-white/10 backdrop-blur rounded-lg p-3">
                        <div class="text-xs opacity-90 mb-1">Total Estimasi Biaya</div>
                        <div class="text-xl font-bold">
                            @if(isset($totalCost['grand_total']))
                                Rp {{ number_format($totalCost['grand_total']['min'] / 1000000, 1) }}-{{ number_format($totalCost['grand_total']['max'] / 1000000, 1) }} Jt
                            @elseif(isset($totalCost['min']) && isset($totalCost['max']))
                                Rp {{ number_format($totalCost['min'] / 1000000, 0) }}-{{ number_format($totalCost['max'] / 1000000, 0) }} Jt
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-lg p-3">
                        <div class="text-xs opacity-90 mb-1">Timeline Estimasi</div>
                        <div class="text-xl font-bold">{{ $analysis['total_estimated_timeline'] ?? '-' }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-lg p-3">
                        <div class="text-xs opacity-90 mb-1">Tingkat Kompleksitas</div>
                        <div class="text-xl font-bold">{{ $analysis['complexity_score'] ?? '0' }}/10</div>
                    </div>
                </div>
            </div>

            <!-- Recommended Permits -->
            <div class="bg-white rounded-xl shadow-lg border border-linkedin-100 p-5 sm:p-6 stagger-item">
                <h2 class="text-lg font-bold text-linkedin-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-linkedin-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Izin yang Direkomendasikan
                </h2>
                
                @if(count($permits) > 0)
                    <div class="space-y-3">
                        @foreach($permits as $index => $permit)
                            <div class="border-l-4 
                                @if($permit['priority'] === 'critical') border-red-500 bg-red-50
                                @elseif($permit['priority'] === 'high') border-orange-500 bg-orange-50
                                @else border-blue-500 bg-blue-50
                                @endif
                                rounded-lg p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-bold text-gray-900 text-sm">{{ $index + 1 }}. {{ $permit['name'] }}</span>
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                                @if($permit['priority'] === 'critical') bg-red-500 text-white
                                                @elseif($permit['priority'] === 'high') bg-orange-500 text-white
                                                @else bg-blue-500 text-white
                                                @endif">
                                                {{ strtoupper($permit['priority']) }}
                                            </span>
                                        </div>
                                        <p class="text-gray-700 text-sm mb-2 leading-relaxed">{{ $permit['description'] }}</p>
                                        <div class="flex flex-wrap gap-3 text-xs">
                                            <div class="flex items-center gap-1 text-gray-600">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $permit['estimated_timeline'] }}</span>
                                            </div>
                                            <div class="flex items-center gap-1 text-gray-600">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $permit['total_cost_range'] ?? 'Rp 0' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">Tidak ada rekomendasi izin tersedia.</p>
                @endif
            </div>

            <!-- Risk Factors -->
            @if(count($riskFactors) > 0)
            <div class="bg-white rounded-xl shadow-lg border border-linkedin-100 p-5 sm:p-6 stagger-item">
                <h2 class="text-lg font-bold text-linkedin-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Faktor Risiko & Perhatian
                </h2>
                <ul class="space-y-2">
                    @foreach($riskFactors as $risk)
                        <li class="flex items-start gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-bold mt-0.5">!</span>
                            <span class="text-gray-700 text-sm leading-relaxed">{{ $risk }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Next Steps -->
            @if(count($nextSteps) > 0)
            <div class="bg-white rounded-xl shadow-lg border border-linkedin-100 p-5 sm:p-6 stagger-item">
                <h2 class="text-lg font-bold text-linkedin-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-linkedin-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Langkah Selanjutnya
                </h2>
                <ol class="space-y-2">
                    @foreach($nextSteps as $index => $step)
                        <li class="flex items-start gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-linkedin-500 text-white flex items-center justify-center text-xs font-bold mt-0.5">{{ $index + 1 }}</span>
                            <span class="text-gray-700 text-sm leading-relaxed">{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
            @endif

            <!-- Limitations Notice -->
            @if($limitations)
            <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-5 sm:p-6 stagger-item">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-bold text-amber-900 mb-2 text-sm">Catatan Penting</h3>
                        <p class="text-amber-800 text-sm leading-relaxed">{{ $limitations }}</p>
                        
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gold-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-amber-800">Dokumen checklist detail</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gold-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-amber-800">Timeline breakdown</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gold-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-amber-800">Pendampingan konsultan</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gold-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-amber-800">Portal monitoring real-time</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- CTA Section -->
            <div class="bg-gradient-to-r from-linkedin-600 via-linkedin-500 to-linkedin-600 rounded-xl shadow-lg p-6 sm:p-8 text-white text-center stagger-item">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <h2 class="text-2xl font-bold">Siap Mulai Proses Perizinan?</h2>
                </div>
                <p class="text-sm opacity-90 mb-5 max-w-2xl mx-auto leading-relaxed">
                    Daftar sekarang untuk mendapatkan <strong>analisis lengkap</strong>, 
                    pendampingan konsultan bersertifikat, dan akses portal monitoring 24/7.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                    <a href="{{ route('client.register') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white text-linkedin-600 font-bold rounded-lg hover:bg-linkedin-50 transition shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Daftar Portal Lengkap
                    </a>
                    
                    <a href="https://wa.me/6283879602855?text=Halo%2C%20saya%20tertarik%20dengan%20hasil%20analisis%20{{ $inquiry->inquiry_number }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 backdrop-blur border-2 border-white text-white font-semibold rounded-lg hover:bg-white/20 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        Chat via WhatsApp
                    </a>
                </div>
                
                <p class="mt-4 text-xs opacity-75">
                    Email hasil analisis sudah dikirim ke <strong>{{ $inquiry->email }}</strong>
                </p>
            </div>

            <!-- Social Proof -->
            <div class="text-center py-4 stagger-item">
                <p class="text-xs text-gray-600 mb-2">
                    ✨ <strong>{{ App\Models\ServiceInquiry::count() + 137 }}</strong> perusahaan telah menggunakan fitur analisis AI kami
                </p>
                <div class="flex items-center justify-center gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Gratis
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        Data Aman
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                        </svg>
                        Hasil Cepat
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-linkedin-900 text-white py-6 mt-8">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <p class="mb-2 text-sm">© {{ date('Y') }} Bizmark.ID - Platform Perizinan Digital</p>
            <div class="flex items-center justify-center gap-3 text-xs text-linkedin-200">
                <a href="{{ route('privacy.policy') }}" class="hover:text-white transition">Kebijakan Privasi</a>
                <span>·</span>
                <a href="{{ route('terms.conditions') }}" class="hover:text-white transition">Syarat & Ketentuan</a>
                <span>·</span>
                <a href="{{ route('landing') }}" class="hover:text-white transition">Beranda</a>
            </div>
        </div>
    </footer>

</body>
</html>
