@extends('layouts.app')

@section('meta_title', 'Hasil Estimasi Biaya - Bizmark.ID')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            
            <!-- Success Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 mb-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-3xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            Estimasi Biaya Berhasil!
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            Request ID: <span class="font-mono font-bold">#{{ $consultation->id }}</span> 
                            <span class="mx-2">•</span>
                            {{ $consultation->created_at->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                </div>

                <!-- KBLI Info -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ $consultation->kbli->code }}</span>
                                <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded capitalize">
                                    {{ $consultation->kbli->complexity_level }}
                                </span>
                            </div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $consultation->kbli->description }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $consultation->kbli->category }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost Summary -->
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-8 mb-6 text-white">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                    <i class="fas fa-calculator"></i>
                    Total Estimasi Biaya
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                        <p class="text-sm opacity-80 mb-1">Subtotal</p>
                        <p class="text-2xl font-bold">{{ $consultation->estimate_data['cost_summary']['formatted']['subtotal'] ?? '-' }}</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur rounded-lg p-4 ring-2 ring-white/50">
                        <p class="text-sm opacity-80 mb-1">Total Estimasi</p>
                        <p class="text-4xl font-bold">{{ $consultation->estimate_data['cost_summary']['formatted']['grand_total'] ?? '-' }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                        <p class="text-sm opacity-80 mb-1">Kisaran Biaya</p>
                        <p class="text-xl font-semibold">{{ $consultation->estimate_data['cost_summary']['formatted']['range'] ?? '-' }}</p>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-brain text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold">AI Confidence Score</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-32 h-2 bg-white/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-white rounded-full" style="width: {{ ($consultation->estimate_data['confidence_score'] ?? 0) * 100 }}%"></div>
                                </div>
                                <span class="font-bold">{{ number_format(($consultation->estimate_data['confidence_score'] ?? 0) * 100, 0) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost Breakdown -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <i class="fas fa-file-invoice-dollar text-purple-600"></i>
                    Rincian Biaya
                </h2>

                @php
                    $breakdown = $consultation->estimate_data['cost_breakdown'] ?? [];
                @endphp

                <!-- Biaya Pokok -->
                <div class="mb-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center justify-between">
                        <span>Biaya Pokok</span>
                        <span class="text-blue-600 dark:text-blue-400">Rp {{ number_format($breakdown['biaya_pokok']['total'] ?? 0, 0, ',', '.') }}</span>
                    </h3>
                    <div class="space-y-2 pl-4 border-l-2 border-blue-500">
                        @foreach(($breakdown['biaya_pokok']['breakdown'] ?? []) as $key => $value)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($value, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Biaya Jasa -->
                <div class="mb-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center justify-between">
                        <span>Biaya Jasa</span>
                        <span class="text-purple-600 dark:text-purple-400">Rp {{ number_format($breakdown['biaya_jasa']['total'] ?? 0, 0, ',', '.') }}</span>
                    </h3>
                    <div class="space-y-2 pl-4 border-l-2 border-purple-500">
                        @foreach(($breakdown['biaya_jasa']['breakdown'] ?? []) as $key => $detail)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex-1">
                                    <span class="text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                    @if(isset($detail['hours']))
                                        <span class="text-xs text-gray-500 ml-2">({{ $detail['hours'] }} jam @ Rp {{ number_format($detail['rate'], 0, ',', '.') }})</span>
                                    @endif
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($detail['cost'] ?? $detail, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between text-sm font-medium">
                                <span class="text-gray-700 dark:text-gray-300">Total Jam Kerja</span>
                                <span class="text-gray-900 dark:text-white">{{ $breakdown['biaya_jasa']['total_hours'] ?? 0 }} jam</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overhead -->
                <div class="mb-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center justify-between">
                        <span>Overhead ({{ $breakdown['overhead']['percentage'] ?? 0 }}%)</span>
                        <span class="text-orange-600 dark:text-orange-400">Rp {{ number_format($breakdown['overhead']['amount'] ?? 0, 0, ',', '.') }}</span>
                    </h3>
                </div>
            </div>

            <!-- AI Analysis Results -->
            @if(isset($consultation->estimate_data['ai_analysis']))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <i class="fas fa-robot text-blue-600"></i>
                    AI Analysis
                </h2>

                @php
                    $aiAnalysis = $consultation->estimate_data['ai_analysis'];
                @endphp

                <!-- Permits Count -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Dokumen Perizinan</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $aiAnalysis['permits_count'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-microchip text-xl text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">AI Model</p>
                                <p class="text-sm font-semibold text-purple-600 dark:text-purple-400">{{ $aiAnalysis['model_used'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                @if(isset($aiAnalysis['timeline']))
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-green-600"></i>
                        Estimasi Timeline
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Minimum</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $aiAnalysis['timeline']['minimum_days'] ?? 0 }} hari</p>
                        </div>
                        <div class="text-center bg-green-50 dark:bg-green-900/20 rounded-lg py-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Realistis</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $aiAnalysis['timeline']['realistic_days'] ?? 0 }} hari</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Maximum</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $aiAnalysis['timeline']['maximum_days'] ?? 0 }} hari</p>
                        </div>
                    </div>

                    @if(isset($aiAnalysis['timeline']['critical_path']) && count($aiAnalysis['timeline']['critical_path']) > 0)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Critical Path:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($aiAnalysis['timeline']['critical_path'] as $permit)
                                <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 text-sm font-medium rounded-lg">
                                    {{ $permit }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endif

            <!-- Next Steps -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <i class="fas fa-tasks text-green-600"></i>
                    Langkah Selanjutnya
                </h2>

                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold shrink-0">1</div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Tim kami akan menghubungi Anda</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Dalam 24 jam ke nomor WhatsApp yang Anda berikan untuk diskusi lebih lanjut.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold shrink-0">2</div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Cek email Anda</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Laporan detail estimasi biaya telah dikirim ke email Anda.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold shrink-0">3</div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Daftar ke Client Portal</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Untuk tracking progress dan manajemen project secara real-time.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID%2C%20saya%20sudah%20dapat%20estimasi%20biaya%20(Request%20ID%3A%20%23{{ $consultation->id }})%20dan%20ingin%20konsultasi%20lebih%20lanjut"
                   target="_blank"
                   class="flex-1 flex items-center justify-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all">
                    <i class="fab fa-whatsapp text-2xl"></i>
                    <span>Hubungi via WhatsApp</span>
                </a>

                <a href="/estimasi-biaya"
                   class="flex-1 flex items-center justify-center gap-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-redo"></i>
                    <span>Buat Estimasi Baru</span>
                </a>
            </div>

            <!-- Download Report (Future Feature) -->
            <div class="mt-6 text-center">
                <button 
                    class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition"
                    onclick="window.print()"
                >
                    <i class="fas fa-download"></i>
                    <span>Download Laporan PDF</span>
                </button>
            </div>

        </div>
    </div>
</div>
@endsection
