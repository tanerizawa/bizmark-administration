@extends('layouts.app')

@section('title', 'Revisi Paket Aplikasi')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Revisi Paket Aplikasi</h1>
            <p class="text-muted">{{ $application->application_number }} - {{ $application->client->name }}</p>
        </div>
        <a href="{{ route('admin.permit-applications.show', $application->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.permit-applications.revisions.store', $application->id) }}" method="POST" id="revisionForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Section 1: Alasan Revisi -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>1. Alasan Revisi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tipe Revisi <span class="text-danger">*</span></label>
                            <select name="revision_type" class="form-select @error('revision_type') is-invalid @enderror" required>
                                <option value="">Pilih Tipe Revisi</option>
                                <option value="technical_adjustment" {{ old('revision_type') == 'technical_adjustment' ? 'selected' : '' }}>Penyesuaian Teknis</option>
                                <option value="client_request" {{ old('revision_type') == 'client_request' ? 'selected' : '' }}>Permintaan Client</option>
                                <option value="cost_update" {{ old('revision_type') == 'cost_update' ? 'selected' : '' }}>Update Biaya</option>
                                <option value="document_incomplete" {{ old('revision_type') == 'document_incomplete' ? 'selected' : '' }}>Dokumen Tidak Lengkap</option>
                            </select>
                            @error('revision_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Penjelasan Detail <span class="text-danger">*</span></label>
                            <textarea name="revision_reason" rows="4" class="form-control @error('revision_reason') is-invalid @enderror" placeholder="Jelaskan alasan revisi paket ini..." required>{{ old('revision_reason') }}</textarea>
                            @error('revision_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Contoh: "Setelah kajian teknis di lokasi, diperlukan penambahan izin X karena..."</small>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Daftar Izin -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>2. Daftar Izin</h5>
                    </div>
                    <div class="card-body">
                        <div id="permitsContainer">
                            @if(old('permits'))
                                @foreach(old('permits') as $index => $permit)
                                    <div class="permit-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h6 class="mb-0">Izin #<span class="permit-number">{{ $index + 1 }}</span></h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-permit" onclick="removePermit(this)">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Jenis Izin <span class="text-danger">*</span></label>
                                                <select name="permits[{{ $index }}][permit_type_id]" class="form-select permit-type-select" required onchange="updatePermitInfo(this)">
                                                    <option value="">Pilih Jenis Izin</option>
                                                    @foreach($permitTypes as $permitType)
                                                        <option value="{{ $permitType->id }}" 
                                                                data-name="{{ $permitType->name }}"
                                                                data-base-price="{{ $permitType->base_price }}"
                                                                {{ $permit['permit_type_id'] == $permitType->id ? 'selected' : '' }}>
                                                            {{ $permitType->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Jenis Layanan <span class="text-danger">*</span></label>
                                                <select name="permits[{{ $index }}][service_type]" class="form-select" required>
                                                    <option value="bizmark" {{ $permit['service_type'] == 'bizmark' ? 'selected' : '' }}>BizMark</option>
                                                    <option value="owned" {{ $permit['service_type'] == 'owned' ? 'selected' : '' }}>Milik Sendiri</option>
                                                    <option value="self" {{ $permit['service_type'] == 'self' ? 'selected' : '' }}>Urus Sendiri</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Biaya (Rp) <span class="text-danger">*</span></label>
                                                <input type="number" name="permits[{{ $index }}][unit_price]" class="form-control permit-price" value="{{ $permit['unit_price'] }}" min="0" step="1000" required onkeyup="calculateTotal()">
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Estimasi (Hari) <span class="text-danger">*</span></label>
                                                <input type="number" name="permits[{{ $index }}][estimated_days]" class="form-control" value="{{ $permit['estimated_days'] }}" min="1" required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @php $index = 0; @endphp
                                @foreach($currentPackage['permits'] as $permit)
                                    <div class="permit-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h6 class="mb-0">Izin #<span class="permit-number">{{ $index + 1 }}</span></h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-permit" onclick="removePermit(this)">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Jenis Izin <span class="text-danger">*</span></label>
                                                <select name="permits[{{ $index }}][permit_type_id]" class="form-select permit-type-select" required onchange="updatePermitInfo(this)">
                                                    <option value="">Pilih Jenis Izin</option>
                                                    @foreach($permitTypes as $permitType)
                                                        <option value="{{ $permitType->id }}" 
                                                                data-name="{{ $permitType->name }}"
                                                                data-base-price="{{ $permitType->base_price }}"
                                                                {{ isset($permit['permit_type_id']) && $permit['permit_type_id'] == $permitType->id ? 'selected' : '' }}>
                                                            {{ $permitType->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Jenis Layanan <span class="text-danger">*</span></label>
                                                <select name="permits[{{ $index }}][service_type]" class="form-select" required>
                                                    <option value="bizmark" {{ isset($permit['service_type']) && $permit['service_type'] == 'bizmark' ? 'selected' : '' }}>BizMark</option>
                                                    <option value="owned" {{ isset($permit['service_type']) && $permit['service_type'] == 'owned' ? 'selected' : '' }}>Milik Sendiri</option>
                                                    <option value="self" {{ isset($permit['service_type']) && $permit['service_type'] == 'self' ? 'selected' : '' }}>Urus Sendiri</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Biaya (Rp) <span class="text-danger">*</span></label>
                                                <input type="number" name="permits[{{ $index }}][unit_price]" class="form-control permit-price" value="{{ $permit['unit_price'] ?? 0 }}" min="0" step="1000" required onkeyup="calculateTotal()">
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Estimasi (Hari) <span class="text-danger">*</span></label>
                                                <input type="number" name="permits[{{ $index }}][estimated_days]" class="form-control" value="{{ $permit['estimated_days'] ?? 30 }}" min="1" required>
                                            </div>
                                        </div>
                                    </div>
                                    @php $index++; @endphp
                                @endforeach
                            @endif
                        </div>

                        <button type="button" class="btn btn-outline-primary" onclick="addPermit()">
                            <i class="fas fa-plus me-2"></i>Tambah Izin
                        </button>
                    </div>
                </div>

                <!-- Section 3: Data Lokasi -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>3. Data Lokasi Proyek</h5>
                        <small class="text-white-50">Opsional</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi</label>
                                <input type="text" name="location[province]" class="form-control" value="{{ old('location.province', $application->locationDetail->province ?? '') }}" placeholder="Contoh: Jawa Barat">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kabupaten/Kota</label>
                                <input type="text" name="location[city_regency]" class="form-control" value="{{ old('location.city_regency', $application->locationDetail->city_regency ?? '') }}" placeholder="Contoh: Bandung">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" name="location[district]" class="form-control" value="{{ old('location.district', $application->locationDetail->district ?? '') }}" placeholder="Contoh: Cimahi">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelurahan</label>
                                <input type="text" name="location[sub_district]" class="form-control" value="{{ old('location.sub_district', $application->locationDetail->sub_district ?? '') }}" placeholder="Contoh: Cimahi Tengah">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="location[full_address]" rows="3" class="form-control" placeholder="Jl. ...">{{ old('location.full_address', $application->locationDetail->full_address ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" name="location[postal_code]" class="form-control" value="{{ old('location.postal_code', $application->locationDetail->postal_code ?? '') }}" placeholder="40000">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="location[latitude]" class="form-control" value="{{ old('location.latitude', $application->locationDetail->latitude ?? '') }}" placeholder="-6.123456">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="location[longitude]" class="form-control" value="{{ old('location.longitude', $application->locationDetail->longitude ?? '') }}" placeholder="106.123456">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Zona/Kawasan</label>
                                <select name="location[zone_type]" class="form-select">
                                    <option value="">Pilih Zona</option>
                                    <option value="industrial" {{ old('location.zone_type', $application->locationDetail->zone_type ?? '') == 'industrial' ? 'selected' : '' }}>Industri</option>
                                    <option value="commercial" {{ old('location.zone_type', $application->locationDetail->zone_type ?? '') == 'commercial' ? 'selected' : '' }}>Komersial</option>
                                    <option value="residential" {{ old('location.zone_type', $application->locationDetail->zone_type ?? '') == 'residential' ? 'selected' : '' }}>Residensial</option>
                                    <option value="mixed" {{ old('location.zone_type', $application->locationDetail->zone_type ?? '') == 'mixed' ? 'selected' : '' }}>Campuran</option>
                                    <option value="special_economic_zone" {{ old('location.zone_type', $application->locationDetail->zone_type ?? '') == 'special_economic_zone' ? 'selected' : '' }}>KEK</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Lahan</label>
                                <select name="location[land_status]" class="form-select">
                                    <option value="">Pilih Status</option>
                                    <option value="HGB" {{ old('location.land_status', $application->locationDetail->land_status ?? '') == 'HGB' ? 'selected' : '' }}>HGB</option>
                                    <option value="HGU" {{ old('location.land_status', $application->locationDetail->land_status ?? '') == 'HGU' ? 'selected' : '' }}>HGU</option>
                                    <option value="Hak_Milik" {{ old('location.land_status', $application->locationDetail->land_status ?? '') == 'Hak_Milik' ? 'selected' : '' }}>Hak Milik</option>
                                    <option value="Girik" {{ old('location.land_status', $application->locationDetail->land_status ?? '') == 'Girik' ? 'selected' : '' }}>Girik</option>
                                    <option value="Sewa" {{ old('location.land_status', $application->locationDetail->land_status ?? '') == 'Sewa' ? 'selected' : '' }}>Sewa</option>
                                    <option value="Other" {{ old('location.land_status', $application->locationDetail->land_status ?? '') == 'Other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nomor Sertifikat</label>
                                <input type="text" name="location[land_certificate_number]" class="form-control" value="{{ old('location.land_certificate_number', $application->locationDetail->land_certificate_number ?? '') }}" placeholder="No. Sertifikat...">
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Luas Tanah (m²)</label>
                                <input type="number" name="land_area" class="form-control" value="{{ old('land_area', $currentPackage['project_data']['land_area'] ?? '') }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Luas Bangunan (m²)</label>
                                <input type="number" name="building_area" class="form-control" value="{{ old('building_area', $currentPackage['project_data']['building_area'] ?? '') }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nilai Investasi (Rp)</label>
                                <input type="number" name="investment_value" class="form-control" value="{{ old('investment_value', $currentPackage['project_data']['investment_value'] ?? '') }}" min="0" step="1000000">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Checklist Dokumen Legalitas -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>4. Dokumen Legalitas</h5>
                        <small class="text-white-50">Opsional</small>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Tandai dokumen yang tersedia/diperlukan, atau tambahkan dokumen custom
                        </p>

                        <!-- Tabs untuk organize dokumen -->
                        <ul class="nav nav-tabs mb-3" id="legalityTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="preset-tab" data-bs-toggle="tab" data-bs-target="#preset-docs" type="button" role="tab">
                                    <i class="fas fa-list me-1"></i>Dokumen Standar
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="custom-tab" data-bs-toggle="tab" data-bs-target="#custom-docs" type="button" role="tab">
                                    <i class="fas fa-plus-circle me-1"></i>Dokumen Custom
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="legalityTabContent">
                            <!-- Preset Documents Tab -->
                            <div class="tab-pane fade show active" id="preset-docs" role="tabpanel">
                                <div id="presetDocsContainer">
                                    @php
                                        $documentCategories = [
                                            'land_ownership' => [
                                                'label' => 'Sertifikat Tanah',
                                                'icon' => 'fa-file-certificate',
                                                'examples' => 'HGB, HGU, Hak Milik, atau Girik'
                                            ],
                                            'company_legal' => [
                                                'label' => 'Legalitas Perusahaan',
                                                'icon' => 'fa-building',
                                                'examples' => 'Akta Pendirian, NPWP, NIB, TDP'
                                            ],
                                            'existing_permits' => [
                                                'label' => 'Izin Yang Sudah Ada',
                                                'icon' => 'fa-stamp',
                                                'examples' => 'IMB Existing, SIPA, SIUP'
                                            ],
                                            'power_of_attorney' => [
                                                'label' => 'Surat Kuasa',
                                                'icon' => 'fa-file-signature',
                                                'examples' => 'Jika diwakilkan oleh pihak lain'
                                            ],
                                            'technical' => [
                                                'label' => 'Dokumen Teknis',
                                                'icon' => 'fa-drafting-compass',
                                                'examples' => 'Site Plan, Gambar Arsitek, DED'
                                            ],
                                        ];
                                        $existingDocs = $application->legalityDocuments->keyBy('document_category');
                                    @endphp

                                    @foreach($documentCategories as $category => $info)
                                        @php
                                            $existing = $existingDocs->get($category);
                                        @endphp
                                        <div class="document-item border rounded p-3 mb-3 hover-shadow" style="transition: all 0.3s;">
                                            <div class="d-flex align-items-start">
                                                <div class="form-check me-3">
                                                    <input type="checkbox" 
                                                           class="form-check-input preset-doc-checkbox" 
                                                           name="legality_documents[preset_{{ $loop->index }}][is_available]" 
                                                           value="1" 
                                                           id="doc_{{ $category }}"
                                                           {{ $existing && $existing->is_available ? 'checked' : '' }}
                                                           onchange="toggleDocumentFields(this)">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-check-label fw-bold d-flex align-items-center mb-1" for="doc_{{ $category }}" style="cursor: pointer;">
                                                        <i class="fas {{ $info['icon'] }} text-primary me-2"></i>
                                                        {{ $info['label'] }}
                                                    </label>
                                                    <small class="text-muted d-block mb-2">
                                                        <i class="fas fa-arrow-right me-1"></i>{{ $info['examples'] }}
                                                    </small>
                                                    
                                                    <input type="hidden" name="legality_documents[preset_{{ $loop->index }}][category]" value="{{ $category }}">
                                                    <input type="hidden" name="legality_documents[preset_{{ $loop->index }}][name]" value="{{ $info['label'] }}">
                                                    
                                                    <div class="document-details" style="display: {{ $existing && $existing->is_available ? 'block' : 'none' }};">
                                                        <div class="row g-2 mt-1">
                                                            <div class="col-md-5">
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                                    <input type="text" 
                                                                           name="legality_documents[preset_{{ $loop->index }}][number]" 
                                                                           class="form-control" 
                                                                           placeholder="No. Dokumen"
                                                                           value="{{ $existing->document_number ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-7">
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                                                    <input type="text" 
                                                                           name="legality_documents[preset_{{ $loop->index }}][notes]" 
                                                                           class="form-control" 
                                                                           placeholder="Catatan / Keterangan"
                                                                           value="{{ $existing->notes ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="alert alert-info d-flex align-items-center mt-3">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <small>
                                        <strong>Tips:</strong> Centang dokumen yang sudah tersedia atau yang diperlukan untuk aplikasi ini. 
                                        Untuk dokumen yang tidak ada dalam daftar, gunakan tab "Dokumen Custom".
                                    </small>
                                </div>
                            </div>

                            <!-- Custom Documents Tab -->
                            <div class="tab-pane fade" id="custom-docs" role="tabpanel">
                                <div class="mb-3">
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Tambahkan dokumen legalitas lain yang spesifik untuk aplikasi ini
                                    </p>
                                </div>

                                <div id="customDocsContainer">
                                    <!-- Custom documents will be added here dynamically -->
                                    @php
                                        $customDocs = $application->legalityDocuments->where('document_category', 'other');
                                        $customIndex = 0;
                                    @endphp
                                    @foreach($customDocs as $customDoc)
                                        <div class="custom-doc-item border rounded p-3 mb-3" data-index="{{ $customIndex }}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-file-alt text-success me-2"></i>
                                                    Dokumen Custom #<span class="doc-number">{{ $customIndex + 1 }}</span>
                                                </h6>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeCustomDoc(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <input type="hidden" name="legality_documents[custom_{{ $customIndex }}][category]" value="other">
                                            
                                            <div class="row g-2">
                                                <div class="col-md-12 mb-2">
                                                    <label class="form-label form-label-sm">Nama Dokumen <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           name="legality_documents[custom_{{ $customIndex }}][name]" 
                                                           class="form-control form-control-sm" 
                                                           placeholder="Contoh: Surat Persetujuan Tetangga"
                                                           value="{{ $customDoc->document_name }}"
                                                           required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label form-label-sm">Status</label>
                                                    <select name="legality_documents[custom_{{ $customIndex }}][is_available]" class="form-select form-select-sm">
                                                        <option value="1" {{ $customDoc->is_available ? 'selected' : '' }}>Tersedia</option>
                                                        <option value="0" {{ !$customDoc->is_available ? 'selected' : '' }}>Belum Ada</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label form-label-sm">No. Dokumen</label>
                                                    <input type="text" 
                                                           name="legality_documents[custom_{{ $customIndex }}][number]" 
                                                           class="form-control form-control-sm" 
                                                           placeholder="No. / Ref"
                                                           value="{{ $customDoc->document_number }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label form-label-sm">Tanggal Terbit</label>
                                                    <input type="date" 
                                                           name="legality_documents[custom_{{ $customIndex }}][issued_date]" 
                                                           class="form-control form-control-sm"
                                                           value="{{ $customDoc->issued_date?->format('Y-m-d') }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label form-label-sm">Catatan</label>
                                                    <textarea name="legality_documents[custom_{{ $customIndex }}][notes]" 
                                                              class="form-control form-control-sm" 
                                                              rows="2" 
                                                              placeholder="Keterangan tambahan...">{{ $customDoc->notes }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        @php $customIndex++; @endphp
                                    @endforeach
                                </div>

                                <button type="button" class="btn btn-outline-success btn-sm" onclick="addCustomDoc()">
                                    <i class="fas fa-plus me-2"></i>Tambah Dokumen Custom
                                </button>

                                <div class="alert alert-warning d-flex align-items-center mt-3 mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <small>
                                        <strong>Catatan:</strong> Dokumen custom akan masuk kategori "other". 
                                        Pastikan nama dokumen jelas dan spesifik.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar: Summary & Actions -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Ringkasan Biaya</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah Izin:</span>
                            <strong id="totalPermitsCount">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Estimasi Total:</span>
                            <strong id="totalDaysEstimate">0 hari</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-0">
                            <h5>Total Biaya:</h5>
                            <h4 class="text-success mb-0" id="totalCost">Rp 0</h4>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Simpan Revisi
                        </button>
                        <a href="{{ route('admin.permit-applications.show', $application->id) }}" class="btn btn-outline-secondary w-100">
                            Batal
                        </a>
                    </div>
                </div>

                <!-- Previous Revisions -->
                @if($revisions->count() > 0)
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Revisi</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($revisions as $rev)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <strong>Revisi #{{ $rev->revision_number }}</strong>
                                        <span class="badge bg-{{ $rev->status == 'approved' ? 'success' : ($rev->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($rev->status) }}
                                        </span>
                                    </div>
                                    <small class="text-muted">{{ $rev->created_at->format('d M Y H:i') }}</small><br>
                                    <small>{{ Str::limit($rev->revision_reason, 50) }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let permitIndex = {{ $index ?? 0 }};
let customDocIndex = {{ $customIndex ?? 0 }};

// ========== PERMIT FUNCTIONS ==========
function addPermit() {
    permitIndex++;
    const permitItem = `
        <div class="permit-item border rounded p-3 mb-3" data-index="${permitIndex}">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="mb-0">Izin #<span class="permit-number">${permitIndex + 1}</span></h6>
                <button type="button" class="btn btn-sm btn-danger remove-permit" onclick="removePermit(this)">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Jenis Izin <span class="text-danger">*</span></label>
                    <select name="permits[${permitIndex}][permit_type_id]" class="form-select permit-type-select" required onchange="updatePermitInfo(this)">
                        <option value="">Pilih Jenis Izin</option>
                        @foreach($permitTypes as $permitType)
                            <option value="{{ $permitType->id }}" 
                                    data-name="{{ $permitType->name }}"
                                    data-base-price="{{ $permitType->base_price }}">
                                {{ $permitType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Jenis Layanan <span class="text-danger">*</span></label>
                    <select name="permits[${permitIndex}][service_type]" class="form-select" required>
                        <option value="bizmark">BizMark</option>
                        <option value="owned">Milik Sendiri</option>
                        <option value="self">Urus Sendiri</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Biaya (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="permits[${permitIndex}][unit_price]" class="form-control permit-price" value="0" min="0" step="1000" required onkeyup="calculateTotal()">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Estimasi (Hari) <span class="text-danger">*</span></label>
                    <input type="number" name="permits[${permitIndex}][estimated_days]" class="form-control" value="30" min="1" required>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('permitsContainer').insertAdjacentHTML('beforeend', permitItem);
    updatePermitNumbers();
    calculateTotal();
}

function removePermit(button) {
    if (document.querySelectorAll('.permit-item').length <= 1) {
        alert('Minimal harus ada 1 izin');
        return;
    }
    button.closest('.permit-item').remove();
    updatePermitNumbers();
    calculateTotal();
}

function updatePermitNumbers() {
    document.querySelectorAll('.permit-item').forEach((item, index) => {
        item.querySelector('.permit-number').textContent = index + 1;
    });
}

function updatePermitInfo(select) {
    const option = select.options[select.selectedIndex];
    const basePrice = option.getAttribute('data-base-price');
    const permitItem = select.closest('.permit-item');
    const priceInput = permitItem.querySelector('.permit-price');
    
    if (basePrice && priceInput.value == 0) {
        priceInput.value = basePrice;
        calculateTotal();
    }
}

function calculateTotal() {
    let total = 0;
    let totalDays = 0;
    let count = 0;
    
    document.querySelectorAll('.permit-price').forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
        count++;
    });
    
    document.querySelectorAll('input[name$="[estimated_days]"]').forEach(input => {
        const value = parseInt(input.value) || 0;
        totalDays = Math.max(totalDays, value); // Max days, not sum
    });
    
    document.getElementById('totalCost').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('totalPermitsCount').textContent = count;
    document.getElementById('totalDaysEstimate').textContent = totalDays + ' hari';
}

// ========== LEGALITY DOCUMENT FUNCTIONS ==========

// Toggle document detail fields when checkbox is checked/unchecked
function toggleDocumentFields(checkbox) {
    const documentItem = checkbox.closest('.document-item');
    const detailsDiv = documentItem.querySelector('.document-details');
    
    if (checkbox.checked) {
        detailsDiv.style.display = 'block';
        // Add smooth animation
        detailsDiv.style.animation = 'slideDown 0.3s ease-out';
    } else {
        detailsDiv.style.display = 'none';
        // Clear input values when unchecked (optional)
        const inputs = detailsDiv.querySelectorAll('input');
        inputs.forEach(input => {
            if (input.type !== 'hidden') {
                // input.value = ''; // Uncomment if you want to clear values
            }
        });
    }
}

// Add new custom document
function addCustomDoc() {
    customDocIndex++;
    
    const customDocItem = `
        <div class="custom-doc-item border rounded p-3 mb-3 bg-light" data-index="${customDocIndex}" style="animation: slideDown 0.3s ease-out;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">
                    <i class="fas fa-file-alt text-success me-2"></i>
                    Dokumen Custom #<span class="doc-number">${customDocIndex + 1}</span>
                </h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeCustomDoc(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <input type="hidden" name="legality_documents[custom_${customDocIndex}][category]" value="other">
            
            <div class="row g-2">
                <div class="col-md-12 mb-2">
                    <label class="form-label form-label-sm">Nama Dokumen <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="legality_documents[custom_${customDocIndex}][name]" 
                           class="form-control form-control-sm" 
                           placeholder="Contoh: Surat Persetujuan Tetangga, AMDAL, UKL-UPL"
                           required>
                    <small class="text-muted">Berikan nama yang jelas dan spesifik</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-sm">Status Dokumen</label>
                    <select name="legality_documents[custom_${customDocIndex}][is_available]" class="form-select form-select-sm">
                        <option value="1">✓ Tersedia</option>
                        <option value="0" selected>✗ Belum Ada</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-sm">No. Dokumen / Ref</label>
                    <input type="text" 
                           name="legality_documents[custom_${customDocIndex}][number]" 
                           class="form-control form-control-sm" 
                           placeholder="No. / Ref">
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-sm">Tanggal Terbit</label>
                    <input type="date" 
                           name="legality_documents[custom_${customDocIndex}][issued_date]" 
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-12">
                    <label class="form-label form-label-sm">Catatan / Keterangan</label>
                    <textarea name="legality_documents[custom_${customDocIndex}][notes]" 
                              class="form-control form-control-sm" 
                              rows="2" 
                              placeholder="Tambahkan keterangan jika diperlukan, contoh: Diperlukan untuk proses X, masa berlaku, dll"></textarea>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('customDocsContainer').insertAdjacentHTML('beforeend', customDocItem);
    updateCustomDocNumbers();
}

// Remove custom document
function removeCustomDoc(button) {
    if (confirm('Hapus dokumen custom ini?')) {
        button.closest('.custom-doc-item').remove();
        updateCustomDocNumbers();
    }
}

// Update custom document numbering
function updateCustomDocNumbers() {
    document.querySelectorAll('.custom-doc-item').forEach((item, index) => {
        item.querySelector('.doc-number').textContent = index + 1;
    });
}

// Add CSS animation for smooth slide down
const style = document.createElement('style');
style.textContent = `
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .document-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .custom-doc-item {
        border-left: 3px solid #28a745 !important;
    }
`;
document.head.appendChild(style);

// Calculate on load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
    
    // Add hover effect to document items
    document.querySelectorAll('.document-item').forEach(item => {
        item.style.cursor = 'pointer';
    });
});
</script>
@endpush
@endsection
