<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Beta Tester - Bizmark.ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard Beta Tester</h1>
                        <p class="text-sm text-gray-600">{{ $betaTester->full_name }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">No. Registrasi</div>
                        <div class="font-bold text-blue-600">{{ $betaTester->registration_number }}</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Status Alert -->
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="text-green-600 text-xl mr-3">✓</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-green-900">Berhasil!</h3>
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if(session('info'))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="text-blue-600 text-xl mr-3">ℹ️</div>
                    <div class="flex-1">
                        <p class="text-blue-800">{{ session('info') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="text-red-600 text-xl mr-3">✕</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-900">Error!</h3>
                        <p class="text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Grid Layout -->
            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Status Pendaftaran</h2>
                        
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Status Saat Ini</div>
                                <div class="flex items-center gap-2">
                                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-{{ $betaTester->status_color }}-100 text-{{ $betaTester->status_color }}-800">
                                        {{ $betaTester->status_label }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600 mb-1">Progress Dokumen</div>
                                <div class="text-3xl font-bold text-blue-600">{{ $betaTester->document_progress }}%</div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-6">
                            <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-blue-600 h-full transition-all duration-500" 
                                     style="width: {{ $betaTester->document_progress }}%"></div>
                            </div>
                        </div>

                        @if($betaTester->status === 'documents_pending')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h3 class="font-bold text-yellow-900 mb-2">⚠️ Aksi Diperlukan</h3>
                            <p class="text-yellow-800 text-sm mb-3">
                                Silakan tanda tangani kedua dokumen di bawah untuk melanjutkan proses pendaftaran.
                            </p>
                        </div>
                        @elseif($betaTester->status === 'documents_signed')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="font-bold text-blue-900 mb-2">✓ Dokumen Lengkap</h3>
                            <p class="text-blue-800 text-sm">
                                Terima kasih! Tim kami sedang melakukan verifikasi. Anda akan menerima notifikasi melalui email.
                            </p>
                        </div>
                        @elseif($betaTester->status === 'active')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h3 class="font-bold text-green-900 mb-2">🎉 Selamat!</h3>
                            <p class="text-green-800 text-sm">
                                Anda telah disetujui sebagai beta tester. Akses GitLab dan sistem akan segera diberikan.
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Documents -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Dokumen</h2>
                        
                        <div class="space-y-4">
                            @foreach($documents as $document)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 mb-1">
                                            {{ $document->document_title }}
                                        </h3>
                                        <div class="flex items-center gap-2 text-sm">
                                            <span class="px-3 py-1 rounded-full bg-{{ $document->signed_status['color'] }}-100 text-{{ $document->signed_status['color'] }}-800">
                                                {{ $document->signed_status['label'] }}
                                            </span>
                                            @if($document->is_signed)
                                                <span class="text-gray-600">
                                                    • Ditandatangani pada {{ $document->signed_at->isoFormat('DD MMM YYYY, HH:mm') }} WIB
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('beta-tester.document.view', ['documentId' => $document->id, 'token' => $betaTester->registration_number]) }}" 
                                       class="flex-1 text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                                        👁️ Lihat Dokumen
                                    </a>
                                    
                                    @if($document->is_signed)
                                        <a href="{{ route('beta-tester.document.download', ['documentId' => $document->id, 'token' => $betaTester->registration_number]) }}" 
                                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                            📥 Download PDF
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Aktivitas Terakhir</h2>
                        
                        @if($recentActivities->isEmpty())
                        <p class="text-gray-600 text-center py-8">Belum ada aktivitas</p>
                        @else
                        <div class="space-y-3">
                            @foreach($recentActivities as $activity)
                            <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0">
                                <div class="w-2 h-2 rounded-full bg-{{ $activity->activity_color }}-600 mt-2"></div>
                                <div class="flex-1">
                                    <p class="text-gray-900 text-sm">{{ $activity->activity_description }}</p>
                                    <p class="text-gray-500 text-xs mt-1">
                                        {{ $activity->time_ago }} • {{ $activity->browser }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Profil Saya</h2>
                        
                        <div class="space-y-3 text-sm">
                            <div>
                                <div class="text-gray-600 mb-1">Nama Lengkap</div>
                                <div class="font-semibold text-gray-900">{{ $betaTester->full_name }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600 mb-1">Email</div>
                                <div class="font-semibold text-gray-900">{{ $betaTester->email }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600 mb-1">Universitas</div>
                                <div class="font-semibold text-gray-900">{{ $betaTester->university }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600 mb-1">Program Studi</div>
                                <div class="font-semibold text-gray-900">{{ $betaTester->major }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600 mb-1">Semester</div>
                                <div class="font-semibold text-gray-900">Semester {{ $betaTester->semester }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <h2 class="text-lg font-bold text-blue-900 mb-3">💬 Butuh Bantuan?</h2>
                        <p class="text-blue-800 text-sm mb-4">
                            Jika ada pertanyaan atau mengalami kesulitan, silakan hubungi tim kami.
                        </p>
                        <a href="mailto:support@bizmark.id" 
                           class="block text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                            📧 Hubungi Support
                        </a>
                    </div>

                    @if($betaTester->status === 'active' || $betaTester->status === 'completed')
                    <!-- Program Info -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Info Program</h2>
                        
                        <div class="space-y-3 text-sm">
                            @if($betaTester->program_start_date)
                            <div>
                                <div class="text-gray-600 mb-1">Mulai Program</div>
                                <div class="font-semibold text-gray-900">
                                    {{ $betaTester->program_start_date->isoFormat('DD MMMM YYYY') }}
                                </div>
                            </div>
                            @endif
                            
                            @if($betaTester->program_end_date)
                            <div>
                                <div class="text-gray-600 mb-1">Akhir Program</div>
                                <div class="font-semibold text-gray-900">
                                    {{ $betaTester->program_end_date->isoFormat('DD MMMM YYYY') }}
                                </div>
                            </div>
                            @endif
                            
                            @if($betaTester->gitlab_username)
                            <div>
                                <div class="text-gray-600 mb-1">GitLab Username</div>
                                <div class="font-semibold text-gray-900">@{{ $betaTester->gitlab_username }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
