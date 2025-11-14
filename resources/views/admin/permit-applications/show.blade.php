@extends('layouts.app')

@section('title', 'Detail Permohonan - ' . $application->application_number)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.permit-applications.index') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $application->application_number }}</h1>
                        <p class="text-gray-600 mt-1">{{ $application->permitType->name }}</p>
                    </div>
                </div>
            </div>
            <div>
                @php
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-800',
                        'submitted' => 'bg-blue-100 text-blue-800',
                        'under_review' => 'bg-yellow-100 text-yellow-800',
                        'document_incomplete' => 'bg-orange-100 text-orange-800',
                        'quoted' => 'bg-purple-100 text-purple-800',
                        'quotation_accepted' => 'bg-indigo-100 text-indigo-800',
                        'quotation_rejected' => 'bg-red-100 text-red-800',
                        'payment_pending' => 'bg-yellow-100 text-yellow-800',
                        'payment_verified' => 'bg-teal-100 text-teal-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-gray-100 text-gray-800',
                    ];
                    $colorClass = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full {{ $colorClass }}">
                    {{ ucwords(str_replace('_', ' ', $application->status)) }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Main Content (Left 2 columns) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Quick Actions -->
                @if($application->status === 'submitted')
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-blue-900">Aplikasi Baru!</h3>
                                <p class="text-sm text-blue-700">Klik tombol di bawah untuk mulai review</p>
                            </div>
                            <form action="{{ route('admin.permit-applications.start-review', $application->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-play mr-2"></i>Mulai Review
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Client Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-user mr-2 text-purple-600"></i>Informasi Client
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Nama Client</label>
                            <p class="text-gray-900">{{ $application->client->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Email</label>
                            <p class="text-gray-900">{{ $application->client->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Telepon</label>
                            <p class="text-gray-900">{{ $application->client->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tipe Client</label>
                            <p class="text-gray-900">{{ ucfirst($application->client->client_type) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Application Data -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-file-alt mr-2 text-purple-600"></i>Data Permohonan
                    </h2>
                    @if($application->form_data)
                        @php
                            $formData = is_string($application->form_data) 
                                ? json_decode($application->form_data, true) 
                                : $application->form_data;
                        @endphp
                        <div class="space-y-4">
                            @foreach($formData as $key => $value)
                                @if(!is_array($value))
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                                        <p class="text-gray-900">{{ $value }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Tidak ada data formulir</p>
                    @endif
                    
                    @if($application->notes)
                        <div class="mt-4 pt-4 border-t">
                            <label class="text-sm font-medium text-gray-600">Catatan Client</label>
                            <p class="text-gray-900">{{ $application->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Documents -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-folder mr-2 text-purple-600"></i>Dokumen
                        </h2>
                        @if($application->status === 'under_review' && $application->documents->where('is_verified', false)->count() > 0)
                            <form action="{{ route('admin.permit-applications.verify-all-documents', $application->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                    <i class="fas fa-check-double mr-1"></i>Verifikasi Semua
                                </button>
                            </form>
                        @endif
                    </div>

                    @php
                        $requiredDocs = is_string($application->permitType->required_documents) 
                            ? json_decode($application->permitType->required_documents, true) 
                            : $application->permitType->required_documents;
                    @endphp

                    @if($requiredDocs && count($requiredDocs) > 0)
                        <div class="space-y-3">
                            @foreach($requiredDocs as $requiredDoc)
                                @php
                                    $uploadedDoc = $application->documents->firstWhere('document_type', $requiredDoc);
                                @endphp
                                <div class="border rounded-lg p-4 {{ $uploadedDoc ? ($uploadedDoc->is_verified ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200') : 'bg-gray-50 border-gray-200' }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                @if($uploadedDoc)
                                                    @if($uploadedDoc->is_verified)
                                                        <i class="fas fa-check-circle text-green-600"></i>
                                                    @else
                                                        <i class="fas fa-clock text-yellow-600"></i>
                                                    @endif
                                                @else
                                                    <i class="fas fa-times-circle text-gray-400"></i>
                                                @endif
                                                <h4 class="font-semibold text-gray-900">{{ $requiredDoc }}</h4>
                                            </div>
                                            
                                            @if($uploadedDoc)
                                                <p class="text-sm text-gray-600 mt-1">
                                                    <i class="fas fa-file mr-1"></i>{{ $uploadedDoc->file_name }}
                                                    ({{ number_format($uploadedDoc->file_size / 1024, 2) }} KB)
                                                </p>
                                                @if($uploadedDoc->notes)
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        <i class="fas fa-comment mr-1"></i>{{ $uploadedDoc->notes }}
                                                    </p>
                                                @endif
                                                @if($uploadedDoc->is_verified && $uploadedDoc->verification_notes)
                                                    <p class="text-sm text-green-700 mt-1">
                                                        <i class="fas fa-check mr-1"></i>{{ $uploadedDoc->verification_notes }}
                                                    </p>
                                                @endif
                                            @else
                                                <p class="text-sm text-gray-500 mt-1">Belum diupload</p>
                                            @endif
                                        </div>

                                        @if($uploadedDoc && $application->status === 'under_review')
                                            <div class="flex gap-2">
                                                <a 
                                                    href="{{ Storage::url($uploadedDoc->file_path) }}" 
                                                    target="_blank"
                                                    class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if(!$uploadedDoc->is_verified)
                                                    <form action="{{ route('admin.permit-applications.documents.verify', [$application->id, $uploadedDoc->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approve">
                                                        <button 
                                                            type="submit" 
                                                            class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200"
                                                            title="Verifikasi"
                                                        >
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <button 
                                                        onclick="rejectDocument({{ $uploadedDoc->id }})"
                                                        class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                                                        title="Tolak"
                                                    >
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Tidak ada dokumen yang diperlukan</p>
                    @endif
                </div>

                <!-- Status History -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-history mr-2 text-purple-600"></i>Riwayat Status
                    </h2>
                    @if($application->statusLogs && $application->statusLogs->count() > 0)
                        <div class="space-y-3">
                            @foreach($application->statusLogs->sortByDesc('created_at') as $log)
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-arrow-right text-purple-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-900">
                                                {{ ucwords(str_replace('_', ' ', $log->from_status ?? '-')) }}
                                            </span>
                                            <i class="fas fa-arrow-right text-gray-400"></i>
                                            <span class="font-semibold text-gray-900">
                                                {{ ucwords(str_replace('_', ' ', $log->to_status)) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            {{ $log->changedBy->name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}
                                        </p>
                                        @if($log->notes)
                                            <p class="text-sm text-gray-700 mt-1">{{ $log->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Belum ada riwayat status</p>
                    @endif
                </div>

            </div>

            <!-- Sidebar (Right 1 column) -->
            <div class="space-y-6">
                
                <!-- Quick Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Informasi Singkat</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="text-gray-600">Tanggal Submit</label>
                            <p class="text-gray-900">
                                {{ $application->submitted_at ? $application->submitted_at->format('d M Y H:i') : '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-gray-600">Harga Dasar</label>
                            <p class="text-gray-900">
                                Rp {{ number_format($application->permitType->base_price, 0, ',', '.') }}
                            </p>
                        </div>
                        @if($application->quoted_price)
                            <div>
                                <label class="text-gray-600">Harga Quoted</label>
                                <p class="text-gray-900 font-semibold">
                                    Rp {{ number_format($application->quoted_price, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                        <div>
                            <label class="text-gray-600">Waktu Proses (Est.)</label>
                            <p class="text-gray-900">
                                {{ $application->permitType->avg_processing_days }} hari
                            </p>
                        </div>
                        @if($application->reviewed_by)
                            <div>
                                <label class="text-gray-600">Direview oleh</label>
                                <p class="text-gray-900">{{ $application->reviewedBy->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Admin Actions -->
                @if($application->status !== 'cancelled' && $application->status !== 'completed')
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Aksi Admin</h3>
                        <div class="space-y-2">
                            
                            @if($application->status === 'under_review')
                                <!-- Create Quotation -->
                                <a 
                                    href="{{ route('admin.quotations.create', ['application_id' => $application->id]) }}" 
                                    class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded-lg hover:bg-purple-700"
                                >
                                    <i class="fas fa-file-invoice mr-2"></i>Buat Quotation
                                </a>
                                
                                <!-- Request Document Revision -->
                                <button 
                                    onclick="requestDocumentRevision()"
                                    class="block w-full px-4 py-2 bg-orange-600 text-white text-center rounded-lg hover:bg-orange-700"
                                >
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Request Revisi Dokumen
                                </button>
                            @endif

                            <!-- Update Status -->
                            <button 
                                onclick="showUpdateStatusModal()"
                                class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700"
                            >
                                <i class="fas fa-edit mr-2"></i>Update Status
                            </button>

                            <!-- Add Notes -->
                            <button 
                                onclick="showAddNotesModal()"
                                class="block w-full px-4 py-2 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700"
                            >
                                <i class="fas fa-sticky-note mr-2"></i>Tambah Catatan
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Admin Notes -->
                @if($application->admin_notes)
                    <div class="bg-yellow-50 rounded-lg border border-yellow-200 p-6">
                        <h3 class="font-bold text-gray-900 mb-3">Catatan Admin</h3>
                        <div class="text-sm text-gray-700 whitespace-pre-line">{{ $application->admin_notes }}</div>
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>

<!-- Modals will be added here with JavaScript -->
<script>
function requestDocumentRevision() {
    const notes = prompt('Masukkan alasan permintaan revisi dokumen:');
    if (notes && notes.trim()) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.permit-applications.request-document-revision', $application->id) }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'notes';
        notesInput.value = notes;
        
        form.appendChild(csrf);
        form.appendChild(notesInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function showUpdateStatusModal() {
    // Simple prompt for now, can be enhanced with modal
    const status = prompt('Update status ke: (under_review, document_incomplete, quoted, payment_pending, in_progress, completed, cancelled)');
    if (status) {
        const notes = prompt('Catatan (optional):');
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.permit-applications.update-status', $application->id) }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        
        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'notes';
        notesInput.value = notes || '';
        
        form.appendChild(csrf);
        form.appendChild(statusInput);
        form.appendChild(notesInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function showAddNotesModal() {
    const notes = prompt('Tambahkan catatan:');
    if (notes && notes.trim()) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.permit-applications.add-notes', $application->id) }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'notes';
        notesInput.value = notes;
        
        form.appendChild(csrf);
        form.appendChild(notesInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectDocument(documentId) {
    const notes = prompt('Alasan penolakan dokumen:');
    if (notes && notes.trim()) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/applications/{{ $application->id }}/documents/' + documentId + '/verify';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'reject';
        
        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'notes';
        notesInput.value = notes;
        
        form.appendChild(csrf);
        form.appendChild(actionInput);
        form.appendChild(notesInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
