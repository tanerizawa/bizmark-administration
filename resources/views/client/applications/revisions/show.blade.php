@extends('layouts.client')

@section('title', 'Review Revisi Paket')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Alert Notification -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Revisi Paket Baru!</strong> Admin telah mengusulkan perubahan pada paket aplikasi Anda. Silakan review perubahan di bawah ini.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Revisi Paket #{{ $revision->revision_number }}</h1>
            <p class="text-muted mb-0">{{ $application->application_number }}</p>
        </div>
        <a href="{{ route('client.applications.show', $application->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Alasan Revisi -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header" style="background-color: #0a66c2; color: white;">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Alasan Revisi</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Tipe:</strong>
                        <span class="badge bg-primary ms-2">
                            @switch($revision->revision_type)
                                @case('technical_adjustment') Penyesuaian Teknis @break
                                @case('client_request') Permintaan Client @break
                                @case('cost_update') Update Biaya @break
                                @case('document_incomplete') Dokumen Tidak Lengkap @break
                                @default {{ $revision->revision_type }}
                            @endswitch
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Penjelasan:</strong>
                        <p class="mb-0 mt-2">{{ $revision->revision_reason }}</p>
                    </div>
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>Direvisi oleh: {{ $revision->revisedBy->name }} pada {{ $revision->created_at->format('d M Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Comparison: Paket Original vs Revised -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header" style="background-color: #0a66c2; color: white;">
                    <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Perbandingan Paket</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="40%">Paket Original</th>
                                    <th width="40%">Paket Revisi (Baru)</th>
                                    <th width="20%" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Permits Comparison -->
                                <tr>
                                    <td colspan="3" class="bg-light"><strong>Daftar Izin</strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            @if(!empty($originalPackage['permits']))
                                                @foreach($originalPackage['permits'] as $permit)
                                                    <li class="mb-2">
                                                        <i class="fas fa-file-alt text-muted me-2"></i>
                                                        @if(isset($permit['permit_name']))
                                                            {{ $permit['permit_name'] }}
                                                        @else
                                                            Izin #{{ $loop->iteration }}
                                                        @endif
                                                        <br>
                                                        <small class="text-muted">
                                                            Biaya: Rp {{ number_format($permit['unit_price'] ?? 0, 0, ',', '.') }}
                                                        </small>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="text-muted">Tidak ada data</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            @foreach($revision->permits_data as $permit)
                                                @php
                                                    $permitType = \App\Models\PermitType::find($permit['permit_type_id']);
                                                @endphp
                                                <li class="mb-2">
                                                    <i class="fas fa-file-alt text-success me-2"></i>
                                                    {{ $permitType->name ?? 'Unknown Permit' }}
                                                    <br>
                                                    <small class="text-muted">
                                                        Biaya: Rp {{ number_format($permit['unit_price'], 0, ',', '.') }} |
                                                        {{ $permit['estimated_days'] }} hari
                                                    </small>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-center">
                                        @if(count($revision->permits_data) > count($originalPackage['permits']))
                                            <span class="badge bg-success">+{{ count($revision->permits_data) - count($originalPackage['permits']) }} Izin</span>
                                        @elseif(count($revision->permits_data) < count($originalPackage['permits']))
                                            <span class="badge bg-warning">-{{ count($originalPackage['permits']) - count($revision->permits_data) }} Izin</span>
                                        @else
                                            <span class="badge bg-info">Sama</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Cost Comparison -->
                                <tr>
                                    <td colspan="3" class="bg-light"><strong>Total Biaya</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <h4>Rp {{ number_format($originalPackage['total_cost'], 0, ',', '.') }}</h4>
                                    </td>
                                    <td class="text-center">
                                        <h4 class="text-success">Rp {{ number_format($revision->total_cost, 0, ',', '.') }}</h4>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $diff = $revision->total_cost - $originalPackage['total_cost'];
                                        @endphp
                                        @if($diff > 0)
                                            <span class="badge bg-danger">+Rp {{ number_format($diff, 0, ',', '.') }}</span>
                                        @elseif($diff < 0)
                                            <span class="badge bg-success">-Rp {{ number_format(abs($diff), 0, ',', '.') }}</span>
                                        @else
                                            <span class="badge bg-secondary">Sama</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header" style="background-color: #0a66c2; color: white;">
                    <h5 class="mb-0"><i class="fas fa-list-ul me-2"></i>Rincian Paket Revisi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Izin</th>
                                    <th>Layanan</th>
                                    <th class="text-end">Biaya</th>
                                    <th class="text-center">Estimasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revision->quotationItems as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $item->item_name }}</strong>
                                            @if($item->description)
                                                <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $item->service_type_label }}</span>
                                        </td>
                                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $item->estimated_days }} hari</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="3" class="text-end"><strong>TOTAL</strong></td>
                                    <td class="text-end"><strong>Rp {{ number_format($revision->total_cost, 0, ',', '.') }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Actions -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Tindakan</h5>
                </div>
                <div class="card-body">
                    @if($revision->status == 'pending_client_approval')
                        <p class="text-muted mb-3">
                            Revisi ini menunggu persetujuan Anda. Silakan review perubahan dan pilih tindakan yang sesuai.
                        </p>

                        <!-- Approve Button -->
                        <form action="{{ route('client.applications.revisions.approve', [$application->id, $revision->id]) }}" method="POST" class="mb-2" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui revisi ini?')">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check-circle me-2"></i>Setuju dengan Revisi
                            </button>
                        </form>

                        <!-- Reject Button with Modal -->
                        <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times-circle me-2"></i>Tolak Revisi
                        </button>

                        <!-- Discuss Button (using existing notes feature) -->
                        <a href="{{ route('client.applications.show', $application->id) }}#communication" class="btn btn-outline-primary w-100">
                            <i class="fas fa-comments me-2"></i>Diskusi dengan Admin
                        </a>
                    @else
                        <div class="alert alert-{{ $revision->status == 'approved' ? 'success' : 'danger' }} mb-0">
                            <i class="fas fa-{{ $revision->status == 'approved' ? 'check' : 'times' }}-circle me-2"></i>
                            <strong>Status:</strong> 
                            @if($revision->status == 'approved')
                                Revisi ini sudah Anda setujui pada {{ $revision->client_approved_at->format('d M Y H:i') }}
                            @else
                                Revisi ini telah ditolak
                            @endif
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Perlu bantuan? <a href="mailto:cs@bizmark.id">Hubungi kami</a>
                    </small>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="card shadow-sm mt-3">
                <div class="card-header" style="background-color: #0a66c2; color: white;">
                    <h6 class="mb-0"><i class="fas fa-info me-2"></i>Informasi Aplikasi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td><strong>Nomor Aplikasi:</strong></td>
                            <td>{{ $application->application_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Revisi:</strong></td>
                            <td>#{{ $revision->revision_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{ $revision->status == 'approved' ? 'success' : ($revision->status == 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($revision->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Diajukan:</strong></td>
                            <td>{{ $revision->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('client.applications.revisions.reject', [$application->id, $revision->id]) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Tolak Revisi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menolak revisi ini? Silakan berikan alasan penolakan:</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Contoh: Biaya terlalu tinggi, saya ingin konsultasi lebih lanjut..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
