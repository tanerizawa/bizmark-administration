@extends('layouts.app')

@section('title', 'Detail Permohonan - ' . $application->application_number)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $formData = is_string($application->form_data) 
                ? json_decode($application->form_data, true) 
                : $application->form_data;
            $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
        @endphp
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.permit-applications.index') }}" class="transition-colors" style="color: rgba(235,235,245,0.6);" onmouseover="this.style.color='#FFFFFF'" onmouseout="this.style.color='rgba(235,235,245,0.6)'">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-xl font-bold" style="color: #FFFFFF;">{{ $application->application_number }}</h1>
                        @if($isPackage && isset($formData['project_name']))
                            <p class="mt-1" style="color: rgba(235,235,245,0.6);">
                                <i class="fas fa-box-open mr-1"></i>
                                <strong style="color: rgba(235,235,245,0.8);">Paket:</strong> {{ $formData['project_name'] }}
                            </p>
                            <p class="text-sm" style="color: rgba(235,235,245,0.4);">
                                {{ $formData['permits_by_service']['bizmark'] ?? 0 }} izin BizMark.ID · 
                                {{ $formData['permits_by_service']['owned'] ?? 0 }} sudah ada · 
                                {{ $formData['permits_by_service']['self'] ?? 0 }} dikerjakan sendiri
                            </p>
                        @else
                            <p class="mt-1" style="color: rgba(235,235,245,0.6);">{{ $application->permitType ? $application->permitType->name : ($formData['permit_name'] ?? 'N/A') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                @php
                    $statusStyles = [
                        'draft' => 'background:rgba(142,142,147,0.15);color:rgba(235,235,245,0.6);',
                        'submitted' => 'background:rgba(10,132,255,0.2);color:#0A84FF;',
                        'under_review' => 'background:rgba(255,159,10,0.2);color:#FF9F0A;',
                        'document_incomplete' => 'background:rgba(255,159,10,0.2);color:#FF9F0A;',
                        'quoted' => 'background:rgba(88,86,214,0.2);color:#5856D6;',
                        'quotation_accepted' => 'background:rgba(88,86,214,0.2);color:#5856D6;',
                        'quotation_rejected' => 'background:rgba(255,69,58,0.2);color:#FF453A;',
                        'payment_pending' => 'background:rgba(255,214,10,0.2);color:#FFD60A;',
                        'payment_verified' => 'background:rgba(48,209,88,0.2);color:#30D158;',
                        'in_progress' => 'background:rgba(10,132,255,0.2);color:#0A84FF;',
                        'completed' => 'background:rgba(52,199,89,0.2);color:#34C759;',
                        'cancelled' => 'background:rgba(142,142,147,0.2);color:rgba(235,235,245,0.5);',
                    ];
                    $statusStyle = $statusStyles[$application->status] ?? 'background:rgba(142,142,147,0.15);color:rgba(235,235,245,0.6);';
                @endphp
                <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full" style="{{ $statusStyle }}">
                    {{ ucwords(str_replace('_', ' ', $application->status)) }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-apple-lg p-4" style="background:rgba(52,199,89,0.15);border-left:4px solid #34C759;">
                <span style="color:#34C759;"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-apple-lg p-4" style="background:rgba(255,69,58,0.15);border-left:4px solid #FF453A;">
                <span style="color:#FF453A;"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Main Content (Left 2 columns) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Quick Actions -->
                @if($application->status === 'submitted')
                    <div class="rounded-apple-lg p-4" style="background:rgba(10,132,255,0.12);border-left:4px solid #0A84FF;">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold" style="color:#0A84FF;">Aplikasi Baru!</h3>
                                <p class="text-sm" style="color:rgba(10,132,255,0.8);">Klik tombol di bawah untuk mulai review</p>
                            </div>
                            <form action="{{ route('admin.permit-applications.start-review', $application->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-apple text-sm font-medium text-white transition-all hover:opacity-80" style="background:var(--apple-blue);">
                                    <i class="fas fa-play mr-2"></i>Mulai Review
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Package Revision Action -->
                @if($isPackage && in_array($application->status, ['under_review', 'submitted', 'document_incomplete', 'quoted']))
                    <div class="rounded-apple-lg p-4" style="background:rgba(88,86,214,0.12);border-left:4px solid #5856D6;">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold" style="color:#5856D6;">
                                    <i class="fas fa-edit mr-2"></i>Perlu Revisi Paket?
                                </h3>
                                <p class="text-sm mt-1" style="color:rgba(88,86,214,0.8);">
                                    Buat revisi untuk menyesuaikan izin, biaya, atau data setelah kajian teknis
                                </p>
                            </div>
                            <a href="{{ route('admin.permit-applications.revise', $application->id) }}" class="px-4 py-2 rounded-apple text-sm font-medium text-white transition-all hover:opacity-80 whitespace-nowrap" style="background:#5856D6;">
                                <i class="fas fa-exchange-alt mr-2"></i>Revisi Paket
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Convert to Project Action -->
                @if($isPackage && $application->status === 'payment_verified' && !$application->project_id)
                    <div class="rounded-apple-lg p-4" style="background:rgba(52,199,89,0.12);border-left:4px solid #34C759;">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold" style="color:#34C759;">
                                    <i class="fas fa-check-circle mr-2"></i>Pembayaran Terverifikasi - Siap Dikonversi
                                </h3>
                                <p class="text-sm mt-1" style="color:rgba(52,199,89,0.8);">
                                    Konversi aplikasi ini menjadi proyek untuk mulai mengelola {{ count($formData['bizmark_permits'] ?? []) }} izin
                                </p>
                            </div>
                            <form action="{{ route('admin.permit-applications.convert-to-project', $application->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Konversi aplikasi ini menjadi proyek?')">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-apple text-sm font-medium text-white transition-all hover:opacity-80 whitespace-nowrap" style="background:#34C759;">
                                    <i class="fas fa-rocket mr-2"></i>Konversi ke Proyek
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Already Converted Notice -->
                @if($application->project_id)
                    <div class="rounded-apple-lg p-4" style="background:rgba(10,132,255,0.12);border-left:4px solid #0A84FF;">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold" style="color:#0A84FF;">
                                    <i class="fas fa-link mr-2"></i>Sudah Dikonversi ke Proyek
                                </h3>
                                <p class="text-sm mt-1" style="color:rgba(10,132,255,0.8);">
                                    Dikonversi pada {{ $application->converted_at ? $application->converted_at->format('d M Y H:i') : 'N/A' }}
                                </p>
                            </div>
                            <a href="{{ route('client.projects.show', $application->project_id) }}" 
                               class="px-4 py-2 rounded-apple text-sm font-medium text-white transition-all hover:opacity-80 whitespace-nowrap" style="background:var(--apple-blue);">
                                <i class="fas fa-external-link-alt mr-2"></i>Lihat Proyek
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Client Information -->
                <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                    <h2 class="text-lg font-bold mb-4" style="color:#FFFFFF;">
                        <i class="fas fa-user mr-2" style="color:#0A84FF;"></i>Informasi Client
                    </h2>
                    @if($application->client)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">Nama Client</label>
                            <p class="mt-0.5" style="color:#FFFFFF;">{{ $application->client->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">Email</label>
                            <p class="mt-0.5" style="color:#FFFFFF;">{{ $application->client->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">Telepon</label>
                            <p class="mt-0.5" style="color:#FFFFFF;">{{ $application->client->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">Tipe Client</label>
                            <p class="mt-0.5" style="color:#FFFFFF;">{{ ucfirst($application->client->client_type) }}</p>
                        </div>
                    </div>
                    @else
                    <p style="color:rgba(235,235,245,0.4);font-style:italic;">Data client tidak tersedia</p>
                    @endif
                </section>

                <!-- Application Data -->
                <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                    <h2 class="text-lg font-bold mb-4" style="color:#FFFFFF;">
                        <i class="fas fa-file-alt mr-2" style="color:#0A84FF;"></i>Data Permohonan
                    </h2>

                    <!-- KBLI Information -->
                    @if($application->kbli_code)
                        <div class="mb-6 p-4 rounded-apple-lg" style="background:rgba(10,132,255,0.08);border:1px solid rgba(10,132,255,0.2);">
                            <h3 class="text-sm font-semibold mb-2 flex items-center" style="color:rgba(235,235,245,0.8);">
                                <i class="fas fa-industry mr-2" style="color:#0A84FF;"></i>
                                Klasifikasi Bidang Usaha (KBLI)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs" style="color:rgba(235,235,245,0.4);">Kode KBLI</label>
                                    <p class="text-sm font-bold" style="color:#0A84FF;">{{ $application->kbli_code }}</p>
                                </div>
                                <div>
                                    <label class="text-xs" style="color:rgba(235,235,245,0.4);">Deskripsi</label>
                                    <p class="text-sm" style="color:#FFFFFF;">{{ $application->kbli_description }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($application->form_data)
                        <!-- Package Information -->
                        @if($isPackage)
                            <div class="mb-6 p-4 rounded-apple-lg" style="background:rgba(88,86,214,0.08);border:1px solid rgba(88,86,214,0.2);">
                                <h3 class="text-sm font-semibold mb-3 flex items-center" style="color:rgba(235,235,245,0.8);">
                                    <i class="fas fa-box-open mr-2" style="color:#5856D6;"></i>
                                    Paket Izin - {{ $formData['project_name'] ?? 'N/A' }}
                                </h3>
                                
                                <!-- Package Summary Cards -->
                                <div class="grid grid-cols-3 gap-3 mb-4">
                                    @if(isset($formData['permits_by_service']))
                                        <div class="rounded-apple-lg p-3 text-center" style="background:rgba(10,132,255,0.12);">
                                            <i class="fas fa-handshake text-xl mb-1" style="color:#0A84FF;"></i>
                                            <p class="text-2xl font-bold" style="color:#FFFFFF;">{{ $formData['permits_by_service']['bizmark'] ?? 0 }}</p>
                                            <p class="text-xs" style="color:rgba(10,132,255,0.9);">BizMark.ID</p>
                                        </div>
                                        <div class="rounded-apple-lg p-3 text-center" style="background:rgba(52,199,89,0.12);">
                                            <i class="fas fa-check-circle text-xl mb-1" style="color:#34C759;"></i>
                                            <p class="text-2xl font-bold" style="color:#FFFFFF;">{{ $formData['permits_by_service']['owned'] ?? 0 }}</p>
                                            <p class="text-xs" style="color:rgba(52,199,89,0.9);">Sudah Ada</p>
                                        </div>
                                        <div class="rounded-apple-lg p-3 text-center" style="background:rgba(255,159,10,0.12);">
                                            <i class="fas fa-user-check text-xl mb-1" style="color:#FF9F0A;"></i>
                                            <p class="text-2xl font-bold" style="color:#FFFFFF;">{{ $formData['permits_by_service']['self'] ?? 0 }}</p>
                                            <p class="text-xs" style="color:rgba(255,159,10,0.9);">Dikerjakan Sendiri</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Project Information -->
                                <div class="rounded-apple-lg p-4 space-y-3" style="background:rgba(255,255,255,0.04);border:1px solid var(--dark-separator);">
                                    <h4 class="font-semibold text-sm mb-2" style="color:rgba(235,235,245,0.8);">Informasi Proyek</h4>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        @if(isset($formData['project_location']))
                                        <div class="col-span-2">
                                            <label class="text-xs" style="color:rgba(235,235,245,0.4);"><i class="fas fa-map-marker-alt mr-1"></i>Lokasi</label>
                                            <p style="color:#FFFFFF;">{{ $formData['project_location'] }}</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['land_area']))
                                        <div>
                                            <label class="text-xs" style="color:rgba(235,235,245,0.4);"><i class="fas fa-ruler-combined mr-1"></i>Luas Tanah</label>
                                            <p style="color:#FFFFFF;">{{ number_format($formData['land_area'], 0, ',', '.') }} m²</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['building_area']))
                                        <div>
                                            <label class="text-xs" style="color:rgba(235,235,245,0.4);"><i class="fas fa-building mr-1"></i>Luas Bangunan</label>
                                            <p style="color:#FFFFFF;">{{ number_format($formData['building_area'], 0, ',', '.') }} m²</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['building_floors']))
                                        <div>
                                            <label class="text-xs" style="color:rgba(235,235,245,0.4);"><i class="fas fa-layer-group mr-1"></i>Jumlah Lantai</label>
                                            <p style="color:#FFFFFF;">{{ $formData['building_floors'] }} lantai</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['investment_value']))
                                        <div>
                                            <label class="text-xs" style="color:rgba(235,235,245,0.4);"><i class="fas fa-money-bill-wave mr-1"></i>Nilai Investasi</label>
                                            <p class="font-semibold" style="color:#34C759;">Rp {{ number_format($formData['investment_value'], 0, ',', '.') }}</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['target_completion_date']))
                                        <div class="col-span-2">
                                            <label class="text-xs" style="color:rgba(235,235,245,0.4);"><i class="fas fa-calendar-check mr-1"></i>Target Penyelesaian</label>
                                            <p style="color:#FFFFFF;">{{ \Carbon\Carbon::parse($formData['target_completion_date'])->format('d M Y') }}</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($formData['project_description']))
                                        <div class="col-span-2">
                                            <label class="text-xs" style="color:rgba(235,235,245,0.4);"><i class="fas fa-align-left mr-1"></i>Deskripsi</label>
                                            <p class="text-xs" style="color:rgba(235,235,245,0.7);">{{ $formData['project_description'] }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Permits List -->
                                @if(isset($formData['selected_permits']) && count($formData['selected_permits']) > 0)
                                <div class="mt-4">
                                    <h4 class="font-semibold text-sm mb-2" style="color:rgba(235,235,245,0.8);">Daftar Izin ({{ count($formData['selected_permits']) }} izin)</h4>
                                    <div class="space-y-2">
                                        @foreach($formData['selected_permits'] as $permit)
                                        @php
                                            $permitBorderColor = match($permit['service_type'] ?? '') {
                                                'bizmark' => 'border-color:rgba(10,132,255,0.3);',
                                                'owned' => 'border-color:rgba(52,199,89,0.3);',
                                                default => 'border-color:rgba(255,159,10,0.3);',
                                            };
                                        @endphp
                                        <div class="rounded-apple-lg p-3 flex items-center justify-between" style="background:rgba(255,255,255,0.04);border:1px solid transparent;{{ $permitBorderColor }}">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <h5 class="font-semibold text-sm" style="color:#FFFFFF;">{{ $permit['name'] }}</h5>
                                                    @if(isset($permit['type']))
                                                    <span class="text-xs px-2 py-0.5 rounded-full" style="{{ $permit['type'] === 'mandatory' ? 'background:rgba(255,69,58,0.15);color:#FF453A;' : 'background:rgba(10,132,255,0.15);color:#0A84FF;' }}">
                                                        {{ $permit['type'] === 'mandatory' ? 'Wajib' : 'Opsional' }}
                                                    </span>
                                                    @endif
                                                </div>
                                                @if(isset($permit['category']) || isset($permit['estimated_days']))
                                                <div class="flex gap-3 mt-1 text-xs" style="color:rgba(235,235,245,0.5);">
                                                    @if(isset($permit['category']))
                                                    <span><i class="fas fa-folder mr-1"></i>{{ $permit['category'] }}</span>
                                                    @endif
                                                    @if($permit['service_type'] === 'bizmark' && isset($permit['estimated_days']))
                                                    <span><i class="fas fa-clock mr-1"></i>~{{ $permit['estimated_days'] }} hari</span>
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                            @php
                                                $badgeStyle = match($permit['service_type'] ?? '') {
                                                    'bizmark' => 'background:rgba(10,132,255,0.15);color:#0A84FF;',
                                                    'owned' => 'background:rgba(52,199,89,0.15);color:#34C759;',
                                                    default => 'background:rgba(255,159,10,0.15);color:#FF9F0A;',
                                                };
                                            @endphp
                                            <span class="text-xs font-semibold px-3 py-1 rounded-full" style="{{ $badgeStyle }}">
                                                @if($permit['service_type'] === 'bizmark')
                                                    <i class="fas fa-handshake mr-1"></i>BizMark.ID
                                                @elseif($permit['service_type'] === 'owned')
                                                    <i class="fas fa-check-circle mr-1"></i>Sudah Ada
                                                @else
                                                    <i class="fas fa-user-check mr-1"></i>Sendiri
                                                @endif
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Important Note -->
                                @if(isset($formData['permits_by_service']['bizmark']) && $formData['permits_by_service']['bizmark'] > 0)
                                <div class="mt-4 p-3 rounded-apple-lg" style="background:rgba(255,214,10,0.1);border:1px solid rgba(255,214,10,0.2);">
                                    <p class="text-sm" style="color:#FFD60A;">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Catatan:</strong> Quotation hanya untuk <strong>{{ $formData['permits_by_service']['bizmark'] }} izin BizMark.ID</strong>. 
                                        Izin lainnya hanya sebagai referensi.
                                    </p>
                                </div>
                                @endif
                            </div>
                        @endif
                    @endif

                    @if($application->form_data)
                        @php
                            $packageFields = [
                                'project_name', 'project_location', 'land_area', 'building_area', 
                                'building_floors', 'investment_value', 'target_completion_date', 
                                'project_description', 'selected_permits', 'permits_by_service',
                                'bizmark_permits', 'owned_permits', 'self_permits', 'package_type', 'source'
                            ];
                        @endphp
                        
                        @if(!$isPackage)
                        <div class="space-y-4">
                            @foreach($formData as $key => $value)
                                @if(!is_array($value))
                                    <div>
                                        <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                                        <p style="color:#FFFFFF;">{{ $value }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @else
                        @php
                            $additionalFields = array_diff_key($formData, array_flip($packageFields));
                            $hasAdditionalFields = false;
                            foreach($additionalFields as $value) {
                                if(!is_array($value)) { $hasAdditionalFields = true; break; }
                            }
                        @endphp
                        @if($hasAdditionalFields)
                        <div class="mt-4 p-4 rounded-apple-lg" style="background:rgba(255,255,255,0.04);border:1px solid var(--dark-separator);">
                            <h4 class="font-semibold text-sm mb-3" style="color:rgba(235,235,245,0.8);">Informasi Tambahan</h4>
                            <div class="space-y-3">
                                @foreach($additionalFields as $key => $value)
                                    @if(!is_array($value))
                                        <div>
                                            <label class="text-xs" style="color:rgba(235,235,245,0.4);">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                                            <p class="text-sm" style="color:#FFFFFF;">{{ $value }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @endif
                    @else
                        <p style="color:rgba(235,235,245,0.4);">Tidak ada data formulir</p>
                    @endif
                    
                    @if($application->notes)
                        <div class="mt-4 pt-4" style="border-top:1px solid var(--dark-separator);">
                            <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">Catatan Client</label>
                            <p style="color:#FFFFFF;">{{ $application->notes }}</p>
                        </div>
                    @endif
                </section>

                <!-- Documents -->
                <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold" style="color:#FFFFFF;">
                            <i class="fas fa-folder mr-2" style="color:#0A84FF;"></i>Dokumen
                        </h2>
                        @if($application->status === 'under_review' && $application->documents->where('status', 'pending')->count() > 0)
                            <form action="{{ route('admin.applications.documents.approve-all', $application->id) }}" method="POST" onsubmit="return confirm('Approve semua dokumen pending?')">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm rounded-apple font-medium text-white transition-all hover:opacity-80" style="background:#34C759;">
                                    <i class="fas fa-check-double mr-2"></i>Approve All ({{ $application->documents->where('status', 'pending')->count() }})
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
                                    $docBgStyle = 'background:rgba(142,142,147,0.08);border:1px solid var(--dark-separator);';
                                    if ($uploadedDoc) {
                                        if ($uploadedDoc->status === 'approved') {
                                            $docBgStyle = 'background:rgba(52,199,89,0.08);border:1px solid rgba(52,199,89,0.25);';
                                        } elseif ($uploadedDoc->status === 'rejected') {
                                            $docBgStyle = 'background:rgba(255,69,58,0.08);border:1px solid rgba(255,69,58,0.25);';
                                        } else {
                                            $docBgStyle = 'background:rgba(255,159,10,0.08);border:1px solid rgba(255,159,10,0.25);';
                                        }
                                    }
                                @endphp
                                <div class="rounded-apple-lg p-4" style="{{ $docBgStyle }}">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                @if($uploadedDoc)
                                                    @if($uploadedDoc->status === 'approved')
                                                        <i class="fas fa-check-circle" style="color:#34C759;"></i>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background:rgba(52,199,89,0.2);color:#34C759;">Approved</span>
                                                    @elseif($uploadedDoc->status === 'rejected')
                                                        <i class="fas fa-times-circle" style="color:#FF453A;"></i>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background:rgba(255,69,58,0.2);color:#FF453A;">Rejected</span>
                                                    @else
                                                        <i class="fas fa-clock" style="color:#FF9F0A;"></i>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background:rgba(255,159,10,0.2);color:#FF9F0A;">Pending Review</span>
                                                    @endif
                                                @else
                                                    <i class="fas fa-times-circle" style="color:rgba(142,142,147,0.5);"></i>
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background:rgba(142,142,147,0.15);color:rgba(235,235,245,0.5);">Not Uploaded</span>
                                                @endif
                                                <h4 class="font-semibold" style="color:#FFFFFF;">{{ $requiredDoc }}</h4>
                                            </div>
                                            
                                            @if($uploadedDoc)
                                                <p class="text-sm mt-2" style="color:rgba(235,235,245,0.6);">
                                                    <i class="fas fa-file mr-1"></i>{{ $uploadedDoc->file_name }}
                                                    <span class="ml-2" style="color:rgba(235,235,245,0.3);">({{ number_format($uploadedDoc->file_size / 1024, 2) }} KB)</span>
                                                </p>
                                                <p class="text-xs mt-1" style="color:rgba(235,235,245,0.4);">
                                                    <i class="fas fa-clock mr-1"></i>Uploaded: {{ $uploadedDoc->created_at->format('d M Y H:i') }}
                                                </p>
                                                
                                                @if($uploadedDoc->status === 'approved' && $uploadedDoc->review_notes)
                                                    <p class="text-sm mt-2 p-2 rounded-apple" style="background:rgba(52,199,89,0.1);color:#34C759;">
                                                        <i class="fas fa-comment mr-1"></i>{{ $uploadedDoc->review_notes }}
                                                    </p>
                                                @elseif($uploadedDoc->status === 'rejected' && $uploadedDoc->review_notes)
                                                    <p class="text-sm mt-2 p-2 rounded-apple" style="background:rgba(255,69,58,0.1);color:#FF453A;">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $uploadedDoc->review_notes }}
                                                    </p>
                                                @endif
                                                
                                                @if($uploadedDoc->reviewed_by && $uploadedDoc->reviewed_at)
                                                    <p class="text-xs mt-1" style="color:rgba(235,235,245,0.4);">
                                                        Reviewed by {{ optional($uploadedDoc->reviewer)->name ?? 'Admin' }} • {{ $uploadedDoc->reviewed_at ? $uploadedDoc->reviewed_at->diffForHumans() : '-' }}
                                                    </p>
                                                @endif
                                            @else
                                                <p class="text-sm mt-2" style="color:rgba(235,235,245,0.4);">Belum diupload oleh client</p>
                                            @endif
                                        </div>

                                        @if($uploadedDoc && $application->status === 'under_review')
                                            <div class="flex flex-col gap-2">
                                                <a href="{{ Storage::url($uploadedDoc->file_path) }}" target="_blank"
                                                    class="px-3 py-1.5 text-sm rounded-apple text-center transition-all hover:opacity-80" style="background:rgba(10,132,255,0.15);color:#0A84FF;" title="Preview Document">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </a>
                                                
                                                @if($uploadedDoc->status === 'pending')
                                                    <button onclick="approveDocument({{ $uploadedDoc->id }}, '{{ $requiredDoc }}')"
                                                        class="px-3 py-1.5 text-sm rounded-apple transition-all hover:opacity-80" style="background:rgba(52,199,89,0.15);color:#34C759;" title="Approve Document">
                                                        <i class="fas fa-check mr-1"></i>Approve
                                                    </button>
                                                    <button onclick="rejectDocument({{ $uploadedDoc->id }}, '{{ $requiredDoc }}')"
                                                        class="px-3 py-1.5 text-sm rounded-apple transition-all hover:opacity-80" style="background:rgba(255,69,58,0.15);color:#FF453A;" title="Reject Document">
                                                        <i class="fas fa-times mr-1"></i>Reject
                                                    </button>
                                                @elseif($uploadedDoc->status === 'rejected')
                                                    <span class="text-xs px-2 py-1" style="color:rgba(235,235,245,0.4);">Waiting reupload</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="color:rgba(235,235,245,0.4);">Tidak ada dokumen yang diperlukan</p>
                    @endif
                </section>

                <!-- Communication / Notes -->
                <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold" style="color:#FFFFFF;">
                            <i class="fas fa-comments mr-2" style="color:#0A84FF;"></i>Komunikasi
                        </h2>
                        <button onclick="document.getElementById('addNoteModal').classList.remove('hidden')" 
                                class="px-4 py-2 text-sm rounded-apple font-medium text-white transition-all hover:opacity-80" style="background:var(--apple-blue);">
                            <i class="fas fa-plus mr-2"></i>Tambah Catatan
                        </button>
                    </div>

                    @php
                        $notes = $application->notes()->orderBy('created_at', 'desc')->get();
                    @endphp

                    @if($notes->count() > 0)
                        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                            @foreach($notes as $note)
                                @php
                                    $noteBg = $note->is_internal 
                                        ? 'background:rgba(255,214,10,0.08);border-left:3px solid #FFD60A;' 
                                        : 'background:rgba(255,255,255,0.04);';
                                @endphp
                                <div class="flex gap-4 p-4 rounded-apple-lg" style="{{ $noteBg }}">
                                    <div class="flex-shrink-0">
                                        @php
                                            $avatarStyle = $note->author_type === 'admin' 
                                                ? 'background:rgba(10,132,255,0.15);' 
                                                : 'background:rgba(52,199,89,0.15);';
                                            $avatarIconColor = $note->author_type === 'admin' ? '#0A84FF' : '#34C759';
                                        @endphp
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="{{ $avatarStyle }}">
                                            <i class="fas {{ $note->author_type === 'admin' ? 'fa-user-shield' : 'fa-user' }}" style="color:{{ $avatarIconColor }};"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-semibold" style="color:#FFFFFF;">
                                                    {{ optional($note->author)->name ?? 'Unknown' }}
                                                </span>
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="{{ $note->author_type === 'admin' ? 'background:rgba(10,132,255,0.15);color:#0A84FF;' : 'background:rgba(52,199,89,0.15);color:#34C759;' }}">
                                                    {{ $note->author_type === 'admin' ? 'Admin' : 'Client' }}
                                                </span>
                                                @if($note->is_internal)
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background:rgba(255,214,10,0.15);color:#FFD60A;">
                                                        <i class="fas fa-lock mr-1"></i>Internal
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-xs" style="color:rgba(235,235,245,0.4);">{{ $note->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm whitespace-pre-wrap" style="color:rgba(235,235,245,0.75);">{{ $note->note }}</p>
                                        
                                        @if($note->author_id === Auth::id())
                                            <form action="{{ route('admin.applications.notes.destroy', [$application->id, $note->id]) }}" method="POST" class="mt-2" onsubmit="return confirm('Hapus catatan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs transition-colors" style="color:#FF453A;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                                                    <i class="fas fa-trash mr-1"></i>Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-comments text-4xl mb-3" style="color:rgba(142,142,147,0.3);"></i>
                            <p style="color:rgba(235,235,245,0.4);">Belum ada komunikasi. Tambahkan catatan untuk memulai.</p>
                        </div>
                    @endif
                </section>

                <!-- Status History -->
                <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                    <h2 class="text-lg font-bold mb-4" style="color:#FFFFFF;">
                        <i class="fas fa-history mr-2" style="color:#0A84FF;"></i>Riwayat Status
                    </h2>
                    @if($application->statusLogs && $application->statusLogs->count() > 0)
                        <div class="space-y-3">
                            @foreach($application->statusLogs->sortByDesc('created_at') as $log)
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:rgba(10,132,255,0.15);">
                                            <i class="fas fa-arrow-right" style="color:#0A84FF;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-semibold" style="color:#FFFFFF;">
                                                {{ ucwords(str_replace('_', ' ', $log->from_status ?? '-')) }}
                                            </span>
                                            <i class="fas fa-arrow-right" style="color:rgba(142,142,147,0.5);"></i>
                                            <span class="font-semibold" style="color:#FFFFFF;">
                                                {{ ucwords(str_replace('_', ' ', $log->to_status)) }}
                                            </span>
                                        </div>
                                        <p class="text-sm" style="color:rgba(235,235,245,0.5);">
                                            {{ optional($log->changedBy)->name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}
                                        </p>
                                        @if($log->notes)
                                            <p class="text-sm mt-1" style="color:rgba(235,235,245,0.7);">{{ $log->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="color:rgba(235,235,245,0.4);">Belum ada riwayat status</p>
                    @endif
                </section>

            </div>

            <!-- Sidebar (Right 1 column) -->
            <div class="space-y-6">
                
                <!-- Quick Info -->
                <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                    <h3 class="font-bold mb-4" style="color:#FFFFFF;">Informasi Singkat</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">Tanggal Submit</label>
                            <p style="color:#FFFFFF;">
                                {{ $application->submitted_at ? $application->submitted_at->format('d M Y H:i') : '-' }}
                            </p>
                        </div>
                        @if($application->permitType)
                        <div>
                            <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">Harga Dasar</label>
                            <p style="color:#FFFFFF;">
                                Rp {{ number_format($application->permitType->base_price, 0, ',', '.') }}
                            </p>
                        </div>
                        @endif
                        @if($application->quoted_price)
                            <div>
                                <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">Harga Quoted</label>
                                <p class="font-semibold" style="color:#34C759;">
                                    Rp {{ number_format($application->quoted_price, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                        @if($application->permitType)
                        <div>
                            <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">Waktu Proses (Est.)</label>
                            <p style="color:#FFFFFF;">
                                {{ $application->permitType->avg_processing_days }} hari
                            </p>
                        </div>
                        @endif
                        @if($application->reviewedBy)
                            <div>
                                <label class="text-xs uppercase tracking-wider" style="color:rgba(235,235,245,0.4);">Direview oleh</label>
                                <p style="color:#FFFFFF;">{{ $application->reviewedBy->name }}</p>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Admin Actions -->
                @if($application->status !== 'cancelled' && $application->status !== 'completed')
                    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                        <h3 class="font-bold mb-4" style="color:#FFFFFF;">Aksi Admin</h3>
                        <div class="space-y-2">
                            
                            @if($application->status === 'under_review')
                                <a href="{{ route('admin.quotations.create', ['application_id' => $application->id]) }}" 
                                   class="block w-full px-4 py-2.5 text-center rounded-apple font-medium text-white transition-all hover:opacity-80" style="background:#5856D6;">
                                    <i class="fas fa-file-invoice mr-2"></i>Buat Quotation
                                </a>
                                <button onclick="requestDocumentRevision()"
                                    class="block w-full px-4 py-2.5 text-center rounded-apple font-medium text-white transition-all hover:opacity-80" style="background:#FF9F0A;">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Request Revisi Dokumen
                                </button>
                            @endif

                            <button onclick="showUpdateStatusModal()"
                                class="block w-full px-4 py-2.5 text-center rounded-apple font-medium text-white transition-all hover:opacity-80" style="background:var(--apple-blue);">
                                <i class="fas fa-edit mr-2"></i>Update Status
                            </button>

                            <button onclick="showAddNotesModal()"
                                class="block w-full px-4 py-2.5 text-center rounded-apple font-medium text-white transition-all hover:opacity-80" style="background:rgba(142,142,147,0.4);">
                                <i class="fas fa-sticky-note mr-2"></i>Tambah Catatan
                            </button>
                        </div>
                    </section>
                @endif

                <!-- Admin Notes -->
                @if($application->admin_notes)
                    <section class="rounded-apple-xl p-5 md:p-6" style="background:rgba(255,214,10,0.08);border:1px solid rgba(255,214,10,0.2);">
                        <h3 class="font-bold mb-3" style="color:#FFD60A;">Catatan Admin</h3>
                        <div class="text-sm whitespace-pre-line" style="color:rgba(235,235,245,0.7);">{{ $application->admin_notes }}</div>
                    </section>
                @endif

            </div>

        </div>

    </div>
</div>

<!-- Modals & JavaScript -->
<script>
function requestDocumentRevision() {
    document.getElementById('docRevisionModal').classList.remove('hidden');
}

function showUpdateStatusModal() {
    document.getElementById('updateStatusModal').classList.remove('hidden');
}

function approveDocument(documentId, documentName) {
    if (!confirm(`Approve dokumen "${documentName}"?`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/documents/${documentId}/approve`;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
}

function rejectDocument(documentId, documentName) {
    const docNameDisplay = documentName || 'dokumen ini';
    document.getElementById('rejectDocId').value = documentId;
    document.getElementById('rejectDocName').textContent = docNameDisplay;
    document.getElementById('rejectDocModal').classList.remove('hidden');
}

function showAddNotesModal() {
    document.getElementById('addNoteModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.7); backdrop-filter:blur(4px);">
    <div class="max-w-lg w-full rounded-apple-xl" style="background:rgba(28,28,30,0.95); border:1px solid rgba(84,84,88,0.35); box-shadow:0 25px 50px rgba(0,0,0,0.5);">
        <div class="p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold" style="color:#FFFFFF;">
                    <i class="fas fa-edit mr-2" style="color:#0A84FF;"></i>Update Status
                </h3>
                <button onclick="closeModal('updateStatusModal')" style="color:rgba(235,235,245,0.4);" class="hover:opacity-80 transition-opacity">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.permit-applications.update-status', $application->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color:rgba(235,235,245,0.6);">Status Baru</label>
                        <select name="status" required class="w-full px-3 py-2.5 rounded-apple focus:ring-2 focus:ring-blue-500 focus:outline-none" style="background:rgba(44,44,46,0.8); border:1px solid rgba(84,84,88,0.35); color:#FFFFFF;">
                            <option value="">-- Pilih Status --</option>
                            <option value="under_review" {{ $application->status === 'under_review' ? 'disabled' : '' }}>Under Review</option>
                            <option value="document_incomplete" {{ $application->status === 'document_incomplete' ? 'disabled' : '' }}>Document Incomplete</option>
                            <option value="quoted" {{ $application->status === 'quoted' ? 'disabled' : '' }}>Quoted</option>
                            <option value="payment_pending" {{ $application->status === 'payment_pending' ? 'disabled' : '' }}>Payment Pending</option>
                            <option value="payment_verified" {{ $application->status === 'payment_verified' ? 'disabled' : '' }}>Payment Verified</option>
                            <option value="in_progress" {{ $application->status === 'in_progress' ? 'disabled' : '' }}>In Progress</option>
                            <option value="completed" {{ $application->status === 'completed' ? 'disabled' : '' }}>Completed</option>
                            <option value="cancelled" {{ $application->status === 'cancelled' ? 'disabled' : '' }}>Cancelled</option>
                        </select>
                        <p class="mt-1 text-xs" style="color:rgba(235,235,245,0.4);">Status saat ini: <strong style="color:rgba(235,235,245,0.6);">{{ ucwords(str_replace('_', ' ', $application->status)) }}</strong></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color:rgba(235,235,245,0.6);">Catatan <span style="color:rgba(235,235,245,0.3);">(opsional)</span></label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 rounded-apple focus:ring-2 focus:ring-blue-500 focus:outline-none" style="background:rgba(44,44,46,0.8); border:1px solid rgba(84,84,88,0.35); color:#FFFFFF;" placeholder="Alasan atau keterangan perubahan status..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4" style="border-top:1px solid rgba(84,84,88,0.35);">
                        <button type="button" onclick="closeModal('updateStatusModal')" class="px-4 py-2 rounded-apple transition-colors" style="background:rgba(44,44,46,0.8); color:rgba(235,235,245,0.6); border:1px solid rgba(84,84,88,0.35);">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-apple text-white transition-colors" style="background:#0A84FF;">
                            <i class="fas fa-check mr-2"></i>Update Status
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Revision Modal -->
<div id="docRevisionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.7); backdrop-filter:blur(4px);">
    <div class="max-w-lg w-full rounded-apple-xl" style="background:rgba(28,28,30,0.95); border:1px solid rgba(84,84,88,0.35); box-shadow:0 25px 50px rgba(0,0,0,0.5);">
        <div class="p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold" style="color:#FFFFFF;">
                    <i class="fas fa-exclamation-triangle mr-2" style="color:#FF9F0A;"></i>Request Revisi Dokumen
                </h3>
                <button onclick="closeModal('docRevisionModal')" style="color:rgba(235,235,245,0.4);" class="hover:opacity-80 transition-opacity">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.permit-applications.request-document-revision', $application->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color:rgba(235,235,245,0.6);">Alasan Revisi <span style="color:#FF453A;">*</span></label>
                        <textarea name="notes" rows="4" required class="w-full px-3 py-2 rounded-apple focus:ring-2 focus:outline-none" style="background:rgba(44,44,46,0.8); border:1px solid rgba(84,84,88,0.35); color:#FFFFFF; --tw-ring-color:#FF9F0A;" placeholder="Jelaskan dokumen apa yang perlu diperbaiki dan alasannya..."></textarea>
                    </div>
                    <div class="p-3 rounded-apple text-sm" style="background:rgba(255,159,10,0.12); color:#FF9F0A; border:1px solid rgba(255,159,10,0.2);">
                        <i class="fas fa-info-circle mr-1"></i>
                        Status aplikasi akan berubah menjadi <strong>Document Incomplete</strong> dan client akan mendapat notifikasi.
                    </div>
                    <div class="flex justify-end gap-3 pt-4" style="border-top:1px solid rgba(84,84,88,0.35);">
                        <button type="button" onclick="closeModal('docRevisionModal')" class="px-4 py-2 rounded-apple transition-colors" style="background:rgba(44,44,46,0.8); color:rgba(235,235,245,0.6); border:1px solid rgba(84,84,88,0.35);">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-apple text-white transition-colors" style="background:#FF9F0A;">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Permintaan Revisi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Document Modal -->
<div id="rejectDocModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.7); backdrop-filter:blur(4px);">
    <div class="max-w-lg w-full rounded-apple-xl" style="background:rgba(28,28,30,0.95); border:1px solid rgba(84,84,88,0.35); box-shadow:0 25px 50px rgba(0,0,0,0.5);">
        <div class="p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold" style="color:#FFFFFF;">
                    <i class="fas fa-times-circle mr-2" style="color:#FF453A;"></i>Tolak Dokumen
                </h3>
                <button onclick="closeModal('rejectDocModal')" style="color:rgba(235,235,245,0.4);" class="hover:opacity-80 transition-opacity">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="rejectDocForm" method="POST" action="">
                @csrf
                <input type="hidden" id="rejectDocId" value="">
                <div class="space-y-4">
                    <p class="text-sm" style="color:rgba(235,235,245,0.6);">Dokumen: <strong id="rejectDocName" style="color:#FFFFFF;"></strong></p>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color:rgba(235,235,245,0.6);">Alasan Penolakan <span style="color:#FF453A;">*</span></label>
                        <textarea name="notes" rows="4" required class="w-full px-3 py-2 rounded-apple focus:ring-2 focus:outline-none" style="background:rgba(44,44,46,0.8); border:1px solid rgba(84,84,88,0.35); color:#FFFFFF; --tw-ring-color:#FF453A;" placeholder="Jelaskan alasan penolakan dokumen ini..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4" style="border-top:1px solid rgba(84,84,88,0.35);">
                        <button type="button" onclick="closeModal('rejectDocModal')" class="px-4 py-2 rounded-apple transition-colors" style="background:rgba(44,44,46,0.8); color:rgba(235,235,245,0.6); border:1px solid rgba(84,84,88,0.35);">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-apple text-white transition-colors" style="background:#FF453A;">
                            <i class="fas fa-times mr-2"></i>Tolak Dokumen
                        </button>
                    </div>
                </div>
            </form>
            <script>
                document.getElementById('rejectDocForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const docId = document.getElementById('rejectDocId').value;
                    this.action = `/admin/documents/${docId}/reject`;
                    this.submit();
                });
            </script>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div id="addNoteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.7); backdrop-filter:blur(4px);">
    <div class="max-w-2xl w-full max-h-[90vh] overflow-y-auto rounded-apple-xl" style="background:rgba(28,28,30,0.95); border:1px solid rgba(84,84,88,0.35); box-shadow:0 25px 50px rgba(0,0,0,0.5);">
        <div class="p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold" style="color:#FFFFFF;">
                    <i class="fas fa-comment-medical mr-2" style="color:#0A84FF;"></i>Tambah Catatan
                </h3>
                <button onclick="closeModal('addNoteModal')" style="color:rgba(235,235,245,0.4);" class="hover:opacity-80 transition-opacity">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.applications.notes.store', $application->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color:rgba(235,235,245,0.6);">Catatan</label>
                        <textarea 
                            name="note" 
                            rows="6" 
                            required
                            class="w-full px-3 py-2 rounded-apple focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            style="background:rgba(44,44,46,0.8); border:1px solid rgba(84,84,88,0.35); color:#FFFFFF;"
                            placeholder="Tulis catatan atau pesan untuk client..."
                        ></textarea>
                    </div>

                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_internal" 
                            id="is_internal"
                            value="1"
                            class="w-4 h-4 rounded focus:ring-blue-500"
                            style="background:rgba(44,44,46,0.8); border-color:rgba(84,84,88,0.5);"
                        >
                        <label for="is_internal" class="ml-2 text-sm" style="color:rgba(235,235,245,0.6);">
                            <i class="fas fa-lock mr-1" style="color:#FF9F0A;"></i>
                            Catatan internal (tidak terlihat oleh client)
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4" style="border-top:1px solid rgba(84,84,88,0.35);">
                        <button 
                            type="button"
                            onclick="closeModal('addNoteModal')"
                            class="px-4 py-2 rounded-apple transition-colors"
                            style="background:rgba(44,44,46,0.8); color:rgba(235,235,245,0.6); border:1px solid rgba(84,84,88,0.35);"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 rounded-apple text-white transition-colors"
                            style="background:#0A84FF;"
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
