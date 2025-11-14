@extends('client.layouts.app')

@section('title', 'Detail Permohonan - ' . $application->application_number)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $application->application_number }}</h1>
                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full"
                      style="background-color: {{ $application->status_color }}20; color: {{ $application->status_color }}">
                    {{ $application->status_label }}
                </span>
                @if($pendingPayment)
                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                        <i class="fas fa-clock mr-1"></i>
                        Menunggu Verifikasi
                    </span>
                @endif
            </div>
            <p class="text-gray-600 dark:text-gray-400">{{ $application->permitType->name }}</p>
        </div>
        <a href="{{ route('client.applications.index') }}" 
           class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Application Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Informasi Permohonan
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nomor Permohonan</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->application_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Jenis Izin</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->permitType->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tanggal Dibuat</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($application->submitted_at)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tanggal Diajukan</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->submitted_at->format('d M Y H:i') }}</p>
                    </div>
                    @endif
                    @if($application->quoted_price)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Harga Quotation</p>
                        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($application->quoted_price, 0, ',', '.') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Company Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-building text-blue-600 mr-2"></i>
                    Data Perusahaan
                </h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nama Perusahaan</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->form_data['company_name'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Alamat</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->form_data['company_address'] ?? '-' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">NPWP</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $application->form_data['company_npwp'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Telepon</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $application->form_data['company_phone'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PIC Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-user text-blue-600 mr-2"></i>
                    Penanggung Jawab
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nama</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->form_data['pic_name'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Jabatan</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->form_data['pic_position'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->form_data['pic_email'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">No. HP</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $application->form_data['pic_phone'] ?? '-' }}</p>
                    </div>
                </div>
            </div>

            @if(!empty($application->form_data['notes']))
            <!-- Notes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-sticky-note text-blue-600 mr-2"></i>
                    Catatan Tambahan
                </h2>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $application->form_data['notes'] }}</p>
            </div>
            @endif

            <!-- Documents -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-paperclip text-blue-600 mr-2"></i>
                        Dokumen Pendukung
                        <span class="ml-2 text-sm font-normal text-gray-600 dark:text-gray-400">
                            ({{ $application->documents->count() }} dokumen)
                        </span>
                    </h2>
                    @if($application->status === 'draft' || $application->status === 'document_incomplete')
                    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">
                        <i class="fas fa-upload mr-2"></i>Upload Dokumen
                    </button>
                    @endif
                </div>

                @if($application->documents->count() > 0)
                    <div class="space-y-3">
                        @foreach($application->documents as $document)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="flex-shrink-0">
                                    @if(str_contains($document->mime_type, 'pdf'))
                                        <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                    @elseif(str_contains($document->mime_type, 'image'))
                                        <i class="fas fa-file-image text-blue-500 text-2xl"></i>
                                    @else
                                        <i class="fas fa-file text-gray-500 text-2xl"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $document->document_type }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $document->file_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ $document->file_size_formatted }} • 
                                        {{ $document->created_at->format('d M Y H:i') }}
                                        @if($document->is_verified)
                                            • <span class="text-green-600"><i class="fas fa-check-circle"></i> Terverifikasi</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(!$document->is_verified && ($application->status === 'draft' || $application->status === 'document_incomplete'))
                                <form method="POST" 
                                      action="{{ route('client.applications.documents.delete', [$application->id, $document->id]) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-file-upload text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Belum ada dokumen yang diupload</p>
                        @if($application->status === 'draft' || $application->status === 'document_incomplete')
                        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            <i class="fas fa-upload mr-2"></i>Upload Dokumen Pertama
                        </button>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Status History -->
            @if($application->statusLogs->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Riwayat Status
                </h2>
                <div class="space-y-4">
                    @foreach($application->statusLogs()->latest()->get() as $log)
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                             style="background-color: {{ $application->status_color }}20">
                            <i class="fas fa-circle text-xs" style="color: {{ $application->status_color }}"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">
                                Status berubah dari <span style="color: {{ $application->status_color }}">{{ ucfirst($log->from_status) }}</span>
                                ke <span style="color: {{ $application->status_color }}">{{ ucfirst($log->to_status) }}</span>
                            </p>
                            @if($log->notes)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $log->notes }}</p>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                {{ $log->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Tindakan</h3>
                <div class="space-y-2">
                    {{-- Link to Project if converted --}}
                    @if($application->status === 'converted_to_project' && $application->project_id)
                    <a href="{{ route('client.projects.show', $application->project_id) }}" 
                       class="block w-full text-center px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg transition shadow-lg">
                        <i class="fas fa-project-diagram mr-2"></i>Lihat Project
                    </a>
                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center mt-2">
                        <i class="fas fa-check-circle text-green-600 mr-1"></i>
                        Permohonan telah dikonversi menjadi project
                    </p>
                    @endif
                    
                    @if($application->quotation && in_array($application->status, ['quoted', 'quotation_accepted', 'payment_pending', 'payment_verified']))
                    <a href="{{ route('client.quotations.show', $application->id) }}" 
                       class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                        <i class="fas fa-file-invoice mr-2"></i>Lihat Quotation
                    </a>
                    @endif

                    @if($application->canBeEdited())
                    <a href="{{ route('client.applications.edit', $application->id) }}" 
                       class="block w-full text-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        <i class="fas fa-edit mr-2"></i>Edit Permohonan
                    </a>
                    @endif

                    @if($application->status === 'draft' && $application->documents->count() > 0)
                    <form method="POST" action="{{ route('client.applications.submit', $application->id) }}"
                          onsubmit="return confirm('Yakin ingin mengajukan permohonan ini?')">
                        @csrf
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                            <i class="fas fa-paper-plane mr-2"></i>Ajukan Permohonan
                        </button>
                    </form>
                    @endif

                    @if($application->canBeCancelled())
                    <form method="POST" action="{{ route('client.applications.cancel', $application->id) }}"
                          onsubmit="return confirm('Yakin ingin membatalkan permohonan ini?')">
                        @csrf
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            <i class="fas fa-times-circle mr-2"></i>Batalkan
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Required Documents Checklist -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Dokumen yang Diperlukan</h3>
                <div class="space-y-2">
                    @foreach($application->permitType->required_documents as $index => $doc)
                        @php
                            $uploaded = $application->documents->where('document_type', $doc)->isNotEmpty();
                        @endphp
                        <div class="flex items-start gap-2">
                            <i class="fas fa-{{ $uploaded ? 'check-circle text-green-500' : 'circle text-gray-300' }} mt-1"></i>
                            <span class="text-sm {{ $uploaded ? 'text-gray-900 dark:text-white font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $doc }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Upload Dokumen</h3>
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')" 
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" 
              action="{{ route('client.applications.documents.upload', $application->id) }}" 
              enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-4">
                <!-- Document Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jenis Dokumen <span class="text-red-500">*</span>
                    </label>
                    <select name="document_type" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih jenis dokumen...</option>
                        @foreach($application->permitType->required_documents as $doc)
                            <option value="{{ $doc }}">{{ $doc }}</option>
                        @endforeach
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        File <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="file" 
                           accept=".pdf,.jpg,.jpeg,.png"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Format: PDF, JPG, PNG. Maksimal 5MB
                    </p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea name="notes" 
                              rows="3"
                              placeholder="Tambahkan catatan jika diperlukan..."
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" 
                        onclick="document.getElementById('uploadModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-upload mr-2"></i>Upload
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
