<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->document_title }} - Bizmark.ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .document-content {
            line-height: 1.8;
        }
        .document-content h1 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        .document-content h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #374151;
        }
        .document-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            color: #4b5563;
        }
        .document-content p {
            margin-bottom: 1rem;
            color: #1f2937;
        }
        .document-content strong {
            font-weight: 600;
            color: #111827;
        }
        .document-content ul, .document-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .document-content li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <a href="{{ route('beta-tester.dashboard', ['token' => $betaTester->registration_number]) }}" 
                   class="text-blue-600 hover:underline mb-4 inline-block">
                    ← Kembali ke Dashboard
                </a>
                
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $document->document_title }}</h1>
                        <p class="text-gray-600">{{ $betaTester->full_name }} • {{ $betaTester->registration_number }}</p>
                    </div>
                    <div>
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-{{ $document->signed_status['color'] }}-100 text-{{ $document->signed_status['color'] }}-800">
                            {{ $document->signed_status['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Document Content -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                <div class="document-content prose max-w-none">
                    {!! nl2br(e($document->filled_content)) !!}
                </div>

                @if($document->is_signed)
                <!-- Signature Info -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="font-bold text-gray-900 mb-4">Informasi Tanda Tangan Digital</h3>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ditandatangani oleh:</span>
                            <span class="font-semibold text-gray-900">{{ $betaTester->full_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-semibold text-gray-900">{{ $betaTester->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Waktu:</span>
                            <span class="font-semibold text-gray-900">
                                {{ $document->signed_at->isoFormat('DD MMMM YYYY, HH:mm') }} WIB
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">IP Address:</span>
                            <span class="font-mono text-gray-900">{{ $document->signature_ip }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Hash (SHA-256):</span>
                            <span class="font-mono text-xs text-gray-900 break-all">{{ $document->signature_hash }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Actions -->
            @if(!$document->is_signed)
            <form action="{{ route('beta-tester.document.sign', ['documentId' => $document->id]) }}" 
                  method="POST"
                  x-data="{ agreed: false }">
                @csrf
                <input type="hidden" name="token" value="{{ $betaTester->registration_number }}">
                
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="font-bold text-gray-900 mb-4">Persetujuan</h3>
                    
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" 
                               name="agreement" 
                               value="1"
                               x-model="agreed"
                               class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                               required>
                        <span class="text-gray-700">
                            Saya telah membaca, memahami, dan menyetujui seluruh isi dokumen 
                            <strong>{{ $document->document_title }}</strong> di atas. 
                            Saya bersedia terikat dengan semua ketentuan yang tercantum dan 
                            bertanggung jawab penuh atas persetujuan ini.
                        </span>
                    </label>

                    @error('agreement')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('beta-tester.dashboard', ['token' => $betaTester->registration_number]) }}" 
                       class="text-gray-600 hover:text-gray-900">
                        ← Kembali
                    </a>
                    <button type="submit" 
                            :disabled="!agreed"
                            :class="agreed ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'"
                            class="text-white px-8 py-3 rounded-lg font-semibold transition">
                        ✍️ Tanda Tangani Dokumen
                    </button>
                </div>
            </form>
            @else
            <!-- Already Signed -->
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
                <h3 class="font-bold text-green-900 mb-2">✓ Dokumen Telah Ditandatangani</h3>
                <p class="text-green-800 text-sm mb-4">
                    Anda telah menandatangani dokumen ini pada {{ $document->signed_at->isoFormat('DD MMMM YYYY, HH:mm') }} WIB.
                </p>
                <div class="flex gap-3">
                    <a href="{{ route('beta-tester.dashboard', ['token' => $betaTester->registration_number]) }}" 
                       class="bg-white text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                        ← Kembali ke Dashboard
                    </a>
                    <a href="{{ route('beta-tester.document.download', ['documentId' => $document->id, 'token' => $betaTester->registration_number]) }}" 
                       class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                        📥 Download PDF
                    </a>
                </div>
            </div>
            @endif

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="font-bold text-blue-900 mb-2">ℹ️ Tanda Tangan Digital</h3>
                <div class="text-sm text-blue-800 space-y-1">
                    <p>• Tanda tangan digital memiliki kekuatan hukum yang sama dengan tanda tangan basah</p>
                    <p>• Sistem akan merekam waktu, IP address, dan browser yang digunakan</p>
                    <p>• Hash SHA-256 digunakan untuk memastikan integritas dokumen</p>
                    <p>• Dokumen yang telah ditandatangani tidak dapat diubah</p>
                    <p>• Anda dapat mengunduh PDF dokumen setelah penandatanganan</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg max-w-md">
        {{ session('error') }}
    </div>
    @endif
</body>
</html>
