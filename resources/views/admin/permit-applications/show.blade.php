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
                        @php
                            $formData = is_string($application->form_data) 
                                ? json_decode($application->form_data, true) 
                                : $application->form_data;
                            $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
                        @endphp
                        @if($isPackage && isset($formData['project_name']))
                            <p class="text-gray-600 mt-1">
                                <i class="fas fa-box-open mr-1"></i>
                                <strong>Paket:</strong> {{ $formData['project_name'] }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $formData['permits_by_service']['bizmark'] ?? 0 }} izin BizMark.ID · 
                                {{ $formData['permits_by_service']['owned'] ?? 0 }} sudah ada · 
                                {{ $formData['permits_by_service']['self'] ?? 0 }} dikerjakan sendiri
                            </p>
                        @else
                            <p class="text-gray-600 mt-1">{{ $application->permitType ? $application->permitType->name : ($formData['permit_name'] ?? 'N/A') }}</p>
                        @endif
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

                @php
                    $formData = is_string($application->form_data) 
                        ? json_decode($application->form_data, true) 
                        : $application->form_data;
                    $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
                @endphp

                <!-- Package Revision Action (for packages under review) -->
                @if($isPackage && in_array($application->status, ['under_review', 'submitted', 'document_incomplete', 'quoted']))
                    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-purple-900">
                                    <i class="fas fa-edit mr-2"></i>Perlu Revisi Paket?
                                </h3>
                                <p class="text-sm text-purple-700 mt-1">
                                    Buat revisi untuk menyesuaikan izin, biaya, atau data setelah kajian teknis
                                </p>
                            </div>
                            <a href="{{ route('admin.permit-applications.revise', $application->id) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors whitespace-nowrap">
                                <i class="fas fa-exchange-alt mr-2"></i>Revisi Paket
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Convert to Project Action (for packages with payment_verified status) -->
                @if($isPackage && $application->status === 'payment_verified' && !$application->project_id)
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-green-900">
                                    <i class="fas fa-check-circle mr-2"></i>Pembayaran Terverifikasi - Siap Dikonversi
                                </h3>
                                <p class="text-sm text-green-700 mt-1">
                                    Konversi aplikasi ini menjadi proyek untuk mulai mengelola {{ count($formData['bizmark_permits'] ?? []) }} izin dengan tracking progress dan dependencies
                                </p>
                            </div>
                            <form action="{{ route('admin.permit-applications.convert-to-project', $application->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Konversi aplikasi ini menjadi proyek? Proses ini akan membuat project baru dengan {{ count($formData['bizmark_permits'] ?? []) }} permit yang dapat dikelola secara terpisah.')">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors whitespace-nowrap">
                                    <i class="fas fa-rocket mr-2"></i>Konversi ke Proyek
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Already Converted Notice -->
                @if($application->project_id)
                    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-purple-900">
                                    <i class="fas fa-link mr-2"></i>Sudah Dikonversi ke Proyek
                                </h3>
                                <p class="text-sm text-purple-700 mt-1">
                                    Aplikasi ini sudah dikonversi menjadi proyek pada {{ $application->converted_at ? $application->converted_at->format('d M Y H:i') : 'N/A' }}
                                </p>
                            </div>
                            <a href="{{ route('client.projects.show', $application->project_id) }}" 
                               class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors whitespace-nowrap">
                                <i class="fas fa-external-link-alt mr-2"></i>Lihat Proyek
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Client Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-user mr-2 text-purple-600"></i>Informasi Client
                    </h2>
                    @if($application->client)
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
                    @else
                    <p class="text-gray-500 italic">Data client tidak tersedia</p>
                    @endif
                </div>

                <!-- Application Data -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-file-alt mr-2 text-purple-600"></i>Data Permohonan
                    </h2>

                    <!-- KBLI Information -->
                    @if($application->kbli_code)
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-industry mr-2 text-blue-600"></i>
                                Klasifikasi Bidang Usaha (KBLI)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-gray-600">Kode KBLI</label>
                                    <p class="text-sm font-bold text-blue-900">{{ $application->kbli_code }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Deskripsi</label>
                                    <p class="text-sm text-gray-900">{{ $application->kbli_description }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($application->form_data)
                        @php
                            $formData = is_string($application->form_data) 
                                ? json_decode($application->form_data, true) 
                                : $application->form_data;
                            $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
                        @endphp

                        <!-- Package Information (if multi-permit package) -->
                        @if($isPackage)
                            <div class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-box-open mr-2 text-purple-600"></i>
                                    Paket Izin - {{ $formData['project_name'] ?? 'N/A' }}
                                </h3>
                                
                                <!-- Package Summary Cards -->
                                <div class="grid grid-cols-3 gap-3 mb-4">
                                    @if(isset($formData['permits_by_service']))
                                        <div class="bg-blue-100 border border-blue-300 rounded-lg p-3 text-center">
                                            <i class="fas fa-handshake text-blue-600 text-xl mb-1"></i>
                                            <p class="text-2xl font-bold text-blue-900">{{ $formData['permits_by_service']['bizmark'] ?? 0 }}</p>
                                            <p class="text-xs text-blue-700">BizMark.ID</p>
                                        </div>
                                        <div class="bg-green-100 border border-green-300 rounded-lg p-3 text-center">
                                            <i class="fas fa-check-circle text-green-600 text-xl mb-1"></i>
                                            <p class="text-2xl font-bold text-green-900">{{ $formData['permits_by_service']['owned'] ?? 0 }}</p>
                                            <p class="text-xs text-green-700">Sudah Ada</p>
                                        </div>
                                        <div class="bg-purple-100 border border-purple-300 rounded-lg p-3 text-center">
                                            <i class="fas fa-user-check text-purple-600 text-xl mb-1"></i>
                                            <p class="text-2xl font-bold text-purple-900">{{ $formData['permits_by_service']['self'] ?? 0 }}</p>
                                            <p class="text-xs text-purple-700">Dikerjakan Sendiri</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Project Information -->
                                <div class="bg-white rounded-lg p-4 space-y-3">
                                    <h4 class="font-semibold text-gray-800 text-sm mb-2">Informasi Proyek</h4>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        @if(isset($formData['project_location']))
                                        <div class="col-span-2">
                                            <label class="text-xs text-gray-600"><i class="fas fa-map-marker-alt mr-1"></i>Lokasi</label>
                                            <p class="text-gray-900">{{ $formData['project_location'] }}</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['land_area']))
                                        <div>
                                            <label class="text-xs text-gray-600"><i class="fas fa-ruler-combined mr-1"></i>Luas Tanah</label>
                                            <p class="text-gray-900">{{ number_format($formData['land_area'], 0, ',', '.') }} m²</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['building_area']))
                                        <div>
                                            <label class="text-xs text-gray-600"><i class="fas fa-building mr-1"></i>Luas Bangunan</label>
                                            <p class="text-gray-900">{{ number_format($formData['building_area'], 0, ',', '.') }} m²</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['building_floors']))
                                        <div>
                                            <label class="text-xs text-gray-600"><i class="fas fa-layer-group mr-1"></i>Jumlah Lantai</label>
                                            <p class="text-gray-900">{{ $formData['building_floors'] }} lantai</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['investment_value']))
                                        <div>
                                            <label class="text-xs text-gray-600"><i class="fas fa-money-bill-wave mr-1"></i>Nilai Investasi</label>
                                            <p class="text-gray-900 font-semibold">Rp {{ number_format($formData['investment_value'], 0, ',', '.') }}</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['target_completion_date']))
                                        <div class="col-span-2">
                                            <label class="text-xs text-gray-600"><i class="fas fa-calendar-check mr-1"></i>Target Penyelesaian</label>
                                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($formData['target_completion_date'])->format('d M Y') }}</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['project_description']))
                                        <div class="col-span-2">
                                            <label class="text-xs text-gray-600"><i class="fas fa-align-left mr-1"></i>Deskripsi</label>
                                            <p class="text-gray-700 text-xs">{{ $formData['project_description'] }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Permits List -->
                                @if(isset($formData['selected_permits']) && count($formData['selected_permits']) > 0)
                                <div class="mt-4">
                                    <h4 class="font-semibold text-gray-800 text-sm mb-2">Daftar Izin ({{ count($formData['selected_permits']) }} izin)</h4>
                                    <div class="space-y-2">
                                        @foreach($formData['selected_permits'] as $permit)
                                        <div class="bg-white border rounded-lg p-3 flex items-center justify-between
                                            @if($permit['service_type'] === 'bizmark') border-blue-200
                                            @elseif($permit['service_type'] === 'owned') border-green-200
                                            @else border-purple-200
                                            @endif">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <h5 class="font-semibold text-gray-900 text-sm">{{ $permit['name'] }}</h5>
                                                    @if(isset($permit['type']))
                                                    <span class="text-xs px-2 py-0.5 rounded-full 
                                                        @if($permit['type'] === 'mandatory') bg-red-100 text-red-700
                                                        @else bg-blue-100 text-blue-700
                                                        @endif">
                                                        {{ $permit['type'] === 'mandatory' ? 'Wajib' : 'Opsional' }}
                                                    </span>
                                                    @endif
                                                </div>
                                                @if(isset($permit['category']) || isset($permit['estimated_days']))
                                                <div class="flex gap-3 mt-1 text-xs text-gray-600">
                                                    @if(isset($permit['category']))
                                                    <span><i class="fas fa-folder mr-1"></i>{{ $permit['category'] }}</span>
                                                    @endif
                                                    @if($permit['service_type'] === 'bizmark' && isset($permit['estimated_days']))
                                                    <span><i class="fas fa-clock mr-1"></i>~{{ $permit['estimated_days'] }} hari</span>
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                            <span class="text-xs font-semibold px-3 py-1 rounded-full
                                                @if($permit['service_type'] === 'bizmark') bg-blue-100 text-blue-700
                                                @elseif($permit['service_type'] === 'owned') bg-green-100 text-green-700
                                                @else bg-purple-100 text-purple-700
                                                @endif">
                                                @if($permit['service_type'] === 'bizmark')
                                                    <i class="fas fa-handshake mr-1"></i>BizMark.ID
                                                @elseif($permit['service_type'] === 'owned')
                                                    <i class="fas fa-check-circle mr-1"></i>Sudah Ada
                                                @else
                                                    <i class="fas fa-user-check mr-1"></i>Dikerjakan Sendiri
                                                @endif
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Important Note for Admin -->
                                @if(isset($formData['permits_by_service']['bizmark']) && $formData['permits_by_service']['bizmark'] > 0)
                                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Catatan:</strong> Quotation hanya untuk <strong>{{ $formData['permits_by_service']['bizmark'] }} izin BizMark.ID</strong>. 
                                        Izin "Sudah Ada" dan "Dikerjakan Sendiri" hanya sebagai referensi.
                                    </p>
                                </div>
                                @endif
                            </div>
                        @endif
                    @endif

                    @if($application->form_data)
                        @php
                            $formData = is_string($application->form_data) 
                                ? json_decode($application->form_data, true) 
                                : $application->form_data;
                            $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
                            
                            // Fields already shown in package section
                            $packageFields = [
                                'project_name', 'project_location', 'land_area', 'building_area', 
                                'building_floors', 'investment_value', 'target_completion_date', 
                                'project_description', 'selected_permits', 'permits_by_service',
                                'bizmark_permits', 'owned_permits', 'self_permits', 'package_type', 'source'
                            ];
                        @endphp
                        
                        @if(!$isPackage)
                        <!-- Only show legacy form data for non-package applications -->
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
                        <!-- For package, show any additional custom fields not in packageFields -->
                        @php
                            $additionalFields = array_diff_key($formData, array_flip($packageFields));
                            $hasAdditionalFields = false;
                            foreach($additionalFields as $value) {
                                if(!is_array($value)) {
                                    $hasAdditionalFields = true;
                                    break;
                                }
                            }
                        @endphp
                        @if($hasAdditionalFields)
                        <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <h4 class="font-semibold text-gray-800 text-sm mb-3">Informasi Tambahan</h4>
                            <div class="space-y-3">
                                @foreach($additionalFields as $key => $value)
                                    @if(!is_array($value))
                                        <div>
                                            <label class="text-xs text-gray-600">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                                            <p class="text-sm text-gray-900">{{ $value }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @endif
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
                        @if($application->status === 'under_review' && $application->documents->where('status', 'pending')->count() > 0)
                            <form action="{{ route('admin.applications.documents.approve-all', $application->id) }}" method="POST" onsubmit="return confirm('Approve semua dokumen pending?')">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-check-double mr-2"></i>Approve All Pending ({{ $application->documents->where('status', 'pending')->count() }})
                                </button>
                            </form>
                        @endif
                    </div>

                    @php
                        $requiredDocs = [];
                        if ($application->permitType) {
                            $requiredDocs = is_string($application->permitType->required_documents) 
                                ? json_decode($application->permitType->required_documents, true) 
                                : $application->permitType->required_documents;
                        }
                    @endphp

                    @if($requiredDocs && count($requiredDocs) > 0)
                        <div class="space-y-3">
                            @foreach($requiredDocs as $requiredDoc)
                                @php
                                    $uploadedDoc = $application->documents->firstWhere('document_type', $requiredDoc);
                                    $statusClass = 'bg-gray-50 border-gray-200';
                                    if ($uploadedDoc) {
                                        if ($uploadedDoc->status === 'approved') {
                                            $statusClass = 'bg-green-50 border-green-200';
                                        } elseif ($uploadedDoc->status === 'rejected') {
                                            $statusClass = 'bg-red-50 border-red-200';
                                        } else {
                                            $statusClass = 'bg-yellow-50 border-yellow-200';
                                        }
                                    }
                                @endphp
                                <div class="border rounded-lg p-4 {{ $statusClass }}">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                @if($uploadedDoc)
                                                    @if($uploadedDoc->status === 'approved')
                                                        <i class="fas fa-check-circle text-green-600"></i>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Approved</span>
                                                    @elseif($uploadedDoc->status === 'rejected')
                                                        <i class="fas fa-times-circle text-red-600"></i>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Rejected</span>
                                                    @else
                                                        <i class="fas fa-clock text-yellow-600"></i>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending Review</span>
                                                    @endif
                                                @else
                                                    <i class="fas fa-times-circle text-gray-400"></i>
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Not Uploaded</span>
                                                @endif
                                                <h4 class="font-semibold text-gray-900">{{ $requiredDoc }}</h4>
                                            </div>
                                            
                                            @if($uploadedDoc)
                                                <p class="text-sm text-gray-600 mt-2">
                                                    <i class="fas fa-file mr-1"></i>{{ $uploadedDoc->file_name }}
                                                    <span class="text-gray-400 ml-2">({{ number_format($uploadedDoc->file_size / 1024, 2) }} KB)</span>
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-clock mr-1"></i>Uploaded: {{ $uploadedDoc->created_at->format('d M Y H:i') }}
                                                </p>
                                                
                                                @if($uploadedDoc->status === 'approved' && $uploadedDoc->review_notes)
                                                    <p class="text-sm text-green-700 mt-2 p-2 bg-green-100 rounded">
                                                        <i class="fas fa-comment mr-1"></i>{{ $uploadedDoc->review_notes }}
                                                    </p>
                                                @elseif($uploadedDoc->status === 'rejected' && $uploadedDoc->review_notes)
                                                    <p class="text-sm text-red-700 mt-2 p-2 bg-red-100 rounded">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $uploadedDoc->review_notes }}
                                                    </p>
                                                @endif
                                                
                                                @if($uploadedDoc->reviewed_by && $uploadedDoc->reviewed_at)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Reviewed by {{ $uploadedDoc->reviewer->name ?? 'Admin' }} • {{ $uploadedDoc->reviewed_at->diffForHumans() }}
                                                    </p>
                                                @endif
                                            @else
                                                <p class="text-sm text-gray-500 mt-2">Belum diupload oleh client</p>
                                            @endif
                                        </div>

                                        @if($uploadedDoc && $application->status === 'under_review')
                                            <div class="flex flex-col gap-2">
                                                <a 
                                                    href="{{ Storage::url($uploadedDoc->file_path) }}" 
                                                    target="_blank"
                                                    class="px-3 py-1.5 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-center"
                                                    title="Preview Document"
                                                >
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </a>
                                                
                                                @if($uploadedDoc->status === 'pending')
                                                    <button 
                                                        onclick="approveDocument({{ $uploadedDoc->id }}, '{{ $requiredDoc }}')"
                                                        class="px-3 py-1.5 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200"
                                                        title="Approve Document"
                                                    >
                                                        <i class="fas fa-check mr-1"></i>Approve
                                                    </button>
                                                    <button 
                                                        onclick="rejectDocument({{ $uploadedDoc->id }}, '{{ $requiredDoc }}')"
                                                        class="px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                                                        title="Reject Document"
                                                    >
                                                        <i class="fas fa-times mr-1"></i>Reject
                                                    </button>
                                                @elseif($uploadedDoc->status === 'rejected')
                                                    <span class="text-xs text-gray-500 px-2 py-1">Waiting reupload</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
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

                <!-- Communication / Notes -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-comments mr-2 text-blue-600"></i>Komunikasi
                        </h2>
                        <button onclick="document.getElementById('addNoteModal').classList.remove('hidden')" 
                                class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambah Catatan
                        </button>
                    </div>

                    @php
                        $notes = $application->notes()->orderBy('created_at', 'desc')->get();
                    @endphp

                    @if($notes->count() > 0)
                        <div class="space-y-4 max-h-[500px] overflow-y-auto">
                            @foreach($notes as $note)
                                <div class="flex gap-4 {{ $note->is_internal ? 'bg-yellow-50 border-l-4 border-yellow-400' : 'bg-gray-50' }} p-4 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 {{ $note->author_type === 'admin' ? 'bg-blue-100' : 'bg-green-100' }} rounded-full flex items-center justify-center">
                                            <i class="fas {{ $note->author_type === 'admin' ? 'fa-user-shield text-blue-600' : 'fa-user text-green-600' }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-gray-900">
                                                    {{ $note->author->name ?? 'Unknown' }}
                                                </span>
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $note->author_type === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                                    {{ $note->author_type === 'admin' ? 'Admin' : 'Client' }}
                                                </span>
                                                @if($note->is_internal)
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                                        <i class="fas fa-lock mr-1"></i>Internal
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $note->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $note->note }}</p>
                                        
                                        @if($note->author_id === Auth::id())
                                            <form action="{{ route('admin.applications.notes.destroy', [$application->id, $note->id]) }}" method="POST" class="mt-2" onsubmit="return confirm('Hapus catatan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:text-red-700">
                                                    <i class="fas fa-trash mr-1"></i>Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-comments text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada komunikasi. Tambahkan catatan untuk memulai.</p>
                        </div>
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
                        @if($application->permitType)
                        <div>
                            <label class="text-gray-600">Harga Dasar</label>
                            <p class="text-gray-900">
                                Rp {{ number_format($application->permitType->base_price, 0, ',', '.') }}
                            </p>
                        </div>
                        @endif
                        @if($application->quoted_price)
                            <div>
                                <label class="text-gray-600">Harga Quoted</label>
                                <p class="text-gray-900 font-semibold">
                                    Rp {{ number_format($application->quoted_price, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                        @if($application->permitType)
                        <div>
                            <label class="text-gray-600">Waktu Proses (Est.)</label>
                            <p class="text-gray-900">
                                {{ $application->permitType->avg_processing_days }} hari
                            </p>
                        </div>
                        @endif
                        @if($application->reviewedBy)
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

function approveDocument(documentId, documentName) {
    const notes = prompt(`Notes untuk approval "${documentName}" (optional):`);
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/documents/${documentId}/approve`;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    
    const notesInput = document.createElement('input');
    notesInput.type = 'hidden';
    notesInput.name = 'notes';
    notesInput.value = notes || '';
    
    form.appendChild(csrf);
    form.appendChild(notesInput);
    document.body.appendChild(form);
    form.submit();
}

function rejectDocument(documentId, documentName) {
    const notes = prompt(`Alasan reject "${documentName}" (wajib):`);
    if (notes && notes.trim()) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/documents/${documentId}/reject`;
        
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
    } else {
        alert('Alasan reject wajib diisi!');
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
</script>

<!-- Add Note Modal -->
<div id="addNoteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-comment-medical mr-2 text-blue-600"></i>Tambah Catatan
                </h3>
                <button onclick="document.getElementById('addNoteModal').classList.add('hidden')" 
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.applications.notes.store', $application->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea 
                            name="note" 
                            rows="6" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Tulis catatan atau pesan untuk client..."
                        ></textarea>
                    </div>

                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_internal" 
                            id="is_internal"
                            value="1"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="is_internal" class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-lock mr-1 text-yellow-600"></i>
                            Catatan internal (tidak terlihat oleh client)
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button 
                            type="button"
                            onclick="document.getElementById('addNoteModal').classList.add('hidden')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <i class="fas fa-paper-plane mr-2"></i>Kirim
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
