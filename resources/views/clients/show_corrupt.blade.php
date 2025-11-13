@extends('layouts.app')

@section('title', 'Detail Klien - ' . $client->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-semibold text-dark-text-primary mb-2">Detail Klien</h1>
            <div class="flex items-center space-x-4 text-sm text-dark-text-secondary">
                <span class="flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i>Terdaftar: {{ $client->created_at->format('d M Y') }}
                </span>
                <span class="flex items-center">
                    <i class="fas fa-folder mr-2"></i>{{ $client->projects->count() }} Proyek
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('clients.edit', $client) }}" 
               class="px-4 py-2 rounded-apple text-sm font-medium transition-apple inline-flex items-center" 
               style="background-color: rgba(255, 149, 0, 0.15); color: var(--apple-orange); border: 1px solid rgba(255, 149, 0, 0.3);"
               onmouseover="this.style.backgroundColor='rgba(255, 149, 0, 0.25)'" 
               onmouseout="this.style.backgroundColor='rgba(255, 149, 0, 0.15)'">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('clients.index') }}" 
               class="px-4 py-2 rounded-apple text-sm font-medium transition-apple inline-flex items-center" 
               style="background-color: var(--dark-bg-tertiary); color: var(--dark-text-secondary); border: 1px solid var(--dark-separator);"
               onmouseover="this.style.backgroundColor='#3A3A3C'; this.style.color='#FFFFFF'" 
               onmouseout="this.style.backgroundColor='var(--dark-bg-tertiary); this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-apple flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);">
                    <i class="fas fa-briefcase text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-dark-text-secondary">Total Proyek</p>
                    <h3 class="text-2xl font-semibold text-dark-text-primary">{{ $client->projects->count() }}</h3>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-apple flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-green) 0%, #28a745 100%);">
                    <i class="fas fa-tasks text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-dark-text-secondary">Proyek Aktif</p>
                    <h3 class="text-2xl font-semibold text-dark-text-primary">{{ $client->activeProjectsCount() }}</h3>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-apple flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-orange) 0%, #fd7e14 100%);">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-dark-text-secondary">Total Nilai</p>
                    <h3 class="text-base font-semibold text-dark-text-primary">Rp {{ number_format($client->totalProjectValue ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-apple flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-teal) 0%, #17a2b8 100%);">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-dark-text-secondary">Total Dibayar</p>
                    <h3 class="text-base font-semibold text-dark-text-primary">Rp {{ number_format($client->totalPaid ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <!-- Client Information -->
        <div class="card-elevated rounded-apple-lg">
            <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                <h3 class="text-base font-semibold text-white flex items-center">
                    <i class="fas fa-info-circle mr-2 text-apple-blue"></i>Informasi Klien
                </h3>
            </div>
            <div class="p-4 space-y-4">
                <div class="info-group">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Nama Klien</label>
                    <p class="text-sm text-dark-text-primary">{{ $client->name }}</p>
                </div>

                @if($client->company_name)
                <div class="info-group">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Nama Perusahaan</label>
                    <p class="text-sm text-dark-text-primary">{{ $client->company_name }}</p>
                </div>
                @endif

                <div class="info-group">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Tipe Klien</label>
                    <p class="text-sm">
                        @if($client->client_type == 'individual')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(90, 200, 250, 0.15); color: var(--apple-teal);">Individual</span>
                        @elseif($client->client_type == 'company')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(0, 122, 255, 0.15); color: var(--apple-blue);">Perusahaan</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(175, 82, 222, 0.15); color: var(--apple-purple);">Pemerintah</span>
                        @endif
                    </p>
                </div>

                @if($client->industry)
                <div class="info-group">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Industri</label>
                    <p class="text-sm text-dark-text-primary">{{ $client->industry }}</p>
                </div>
                @endif

                <div class="info-group">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Status</label>
                    <p class="text-sm">
                        @if($client->status == 'active')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(52, 199, 89, 0.15); color: var(--apple-green);">Aktif</span>
                        @elseif($client->status == 'inactive')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(255, 59, 48, 0.15); color: var(--apple-red);">Tidak Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(255, 149, 0, 0.15); color: var(--apple-orange);">Potensial</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card-elevated rounded-apple-lg">
            <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                <h3 class="text-base font-semibold text-white flex items-center">
                    <i class="fas fa-address-book mr-2 text-apple-green"></i>Informasi Kontak
                </h3>
            </div>
            <div class="p-4 space-y-4">
                @if($client->contact_person)
                <div class="info-group">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Contact Person</label>
                    <p class="text-sm text-dark-text-primary">{{ $client->contact_person }}</p>
                </div>
                @endif

                @if($client->email)
                <div class="info-group">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Email</label>
                    <p class="text-sm text-dark-text-primary flex items-center">
                        <i class="fas fa-envelope mr-2 text-apple-blue"></i>
                        <a href="mailto:{{ $client->email }}" class="text-apple-blue hover:underline">{{ $client->email }}</a>
                    </p>
                </div>
                @endif

                @if($client->phone)
                <div class="info-group">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Telepon</label>
                    <p class="text-sm text-dark-text-primary flex items-center">
                        <i class="fas fa-phone mr-2 text-apple-blue"></i>
                        <a href="tel:{{ $client->phone }}" class="text-apple-blue hover:underline">{{ $client->phone }}</a>
                    </p>
                </div>
                @endif

                @if($client->mobile)
                <div class="info-group">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Handphone / WhatsApp</label>
                    <p class="text-sm flex items-center space-x-2">
                        <i class="fab fa-whatsapp text-apple-green"></i>
                        <a href="tel:{{ $client->mobile }}" class="text-apple-blue hover:underline">{{ $client->mobile }}</a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $client->mobile) }}" target="_blank" 
                           class="inline-flex items-center px-2 py-1 rounded-apple text-xs font-medium transition-apple" 
                           style="background-color: rgba(52, 199, 89, 0.15); color: var(--apple-green); border: 1px solid rgba(52, 199, 89, 0.3);"
                           onmouseover="this.style.backgroundColor='rgba(52, 199, 89, 0.25)'" 
                           onmouseout="this.style.backgroundColor='rgba(52, 199, 89, 0.15)'">
                            <i class="fab fa-whatsapp mr-1"></i>Chat
                        </a>
                    </p>
                </div>
                @endif

                @if(!$client->contact_person && !$client->email && !$client->phone && !$client->mobile)
                <p class="text-xs text-dark-text-tertiary">Tidak ada informasi kontak tersedia</p>
                @endif
    </div>

    <!-- Projects List -->
    @if($client->projects->count() > 0)
    <div class="card-elevated rounded-apple-lg">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-semibold text-white flex items-center">
                    <i class="fas fa-folder mr-2 text-apple-orange"></i>Daftar Proyek ({{ $client->projects->count() }})
                </h3>
                <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn-primary px-3 py-1.5 text-white rounded-apple text-xs font-medium inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>Tambah Proyek
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Nama Proyek</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Deadline</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Nilai Kontrak</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @foreach($client->projects as $project)
                        <tr class="hover-lift transition-apple">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-sm text-dark-text-primary">{{ $project->name }}</div>
                                @if($project->description)
                                    <div class="text-xs text-dark-text-secondary mt-1">{{ Str::limit($project->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($project->status)
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: {{ $project->status->color }}33; color: {{ $project->status->color }};">
                                        {{ $project->status->name }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple text-dark-text-secondary">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-text-secondary">
                                @if($project->deadline)
                                    {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-text-primary">
                                Rp {{ number_format($project->contract_value ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('projects.show', $project) }}" 
                                   class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple" 
                                   style="background-color: rgba(0, 122, 255, 0.15); color: var(--apple-blue); border: 1px solid rgba(0, 122, 255, 0.3);"
                                   onmouseover="this.style.backgroundColor='rgba(0, 122, 255, 0.25)'" 
                                   onmouseout="this.style.backgroundColor='rgba(0, 122, 255, 0.15)'">
                                                                        <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Notes -->
    @if($client->notes)
    <div class="card-elevated rounded-apple-lg mt-6">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <h3 class="text-base font-semibold text-white flex items-center">
                <i class="fas fa-sticky-note mr-2 text-apple-purple"></i>Catatan
            </h3>
        </div>
        <div class="p-4">
            <p class="text-sm text-dark-text-secondary whitespace-pre-line">{{ $client->notes }}</p>
        </div>
    </div>
    @endif
</div>

<style>
    .info-group {
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(84, 84, 88, 0.35);
    }

    .info-group:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    tbody tr:hover {
        background-color: var(--dark-bg-tertiary) !important;
    }
</style>
@endsection
</div>

<style>
    .info-group {
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(84, 84, 88, 0.35);
    }

    .info-group:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    tbody tr:hover {
        background-color: var(--dark-bg-tertiary) !important;
    }
</style>
@endsection>
                            <thead style="background-color: var(--dark-bg-tertiary);">
                                <tr>
                                    <th style="color: var(--dark-text-primary);">No Proyek</th>
                                    <th style="color: var(--dark-text-primary);">Nama Proyek</th>
                                    <th style="color: var(--dark-text-primary);">Status</th>
                                    <th style="color: var(--dark-text-primary);">Nilai Proyek</th>
                                    <th style="color: var(--dark-text-primary);">Tanggal</th>
                                    <th width="100" style="color: var(--dark-text-primary);">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->projects as $project)
                                <tr style="border-bottom: 1px solid var(--dark-separator);">
                                    <td><strong>{{ $project->project_number }}</strong></td>
                                    <td>{{ $project->project_name }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $project->status->color ?? '#6b7280' }}">
                                            {{ $project->status->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>Rp {{ number_format($project->project_value ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ $project->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm" style="background-color: var(--apple-teal); color: white; border: none;" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="p-4 text-center">
                        <i class="fas fa-folder-open text-dark-text-tertiary mb-3" style="font-size: 3rem;"></i>
                        <p class="text-dark-text-secondary mb-3">Belum ada proyek untuk klien ini</p>
                        <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Proyek Pertama
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items-center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-group:last-child {
        margin-bottom: 0;
    }

    .info-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--dark-text-tertiary);
        margin-bottom: 0.375rem;
    }

    .info-group p {
        font-size: 0.95rem;
        color: var(--dark-text-primary);
        margin-bottom: 0;
    }

    .table tbody tr:hover {
        background-color: var(--dark-bg-tertiary) !important;
    }
</style>
@endsection
