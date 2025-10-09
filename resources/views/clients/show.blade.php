@extends('layouts.app')

@section('title', 'Detail Klien - ' . $client->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-start mb-4">
        <div class="flex items-center">
            <a href="{{ route('clients.index') }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold" style="color: #FFFFFF;">{{ $client->name }}</h1>
                <div class="flex items-center space-x-3 text-xs mt-1" style="color: rgba(235, 235, 245, 0.6);">
                    <span class="flex items-center">
                        <i class="fas fa-calendar-alt mr-1.5"></i>{{ $client->created_at->format('d M Y') }}
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-folder mr-1.5"></i>{{ $client->projects->count() }} Proyek
                    </span>
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('clients.edit', $client) }}" 
               class="px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center" 
               style="background: rgba(255, 149, 0, 0.9); color: #FFFFFF;">
                <i class="fas fa-edit mr-1.5"></i>Edit
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);">
                    <i class="fas fa-briefcase text-white text-base"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Total Proyek</p>
                    <h3 class="text-xl font-semibold" style="color: #FFFFFF;">{{ $client->projects->count() }}</h3>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-green) 0%, #28a745 100%);">
                    <i class="fas fa-tasks text-white text-base"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Proyek Aktif</p>
                    <h3 class="text-xl font-semibold" style="color: #FFFFFF;">{{ $client->activeProjectsCount() }}</h3>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-orange) 0%, #fd7e14 100%);">
                    <i class="fas fa-money-bill-wave text-white text-base"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Total Nilai</p>
                    <h3 class="text-sm font-semibold" style="color: #FFFFFF;">Rp {{ number_format($client->totalProjectValue ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-teal) 0%, #17a2b8 100%);">
                    <i class="fas fa-check-circle text-white text-base"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Total Dibayar</p>
                    <h3 class="text-sm font-semibold" style="color: #FFFFFF;">Rp {{ number_format($client->totalPaid ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
        <!-- Client Information -->
        <div class="card-elevated rounded-apple-lg">
            <div class="px-4 py-2.5" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                <h3 class="text-sm font-semibold flex items-center" style="color: #FFFFFF;">
                    <i class="fas fa-info-circle mr-2 text-apple-blue"></i>Informasi Klien
                </h3>
            </div>
            <div class="p-4 space-y-3">
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
            <div class="px-4 py-2.5" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                <h3 class="text-sm font-semibold flex items-center" style="color: #FFFFFF;">
                    <i class="fas fa-address-book mr-2 text-apple-green"></i>Informasi Kontak
                </h3>
            </div>
            <div class="p-4 space-y-3">
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
        </div>
    </div>

    <!-- Projects List -->
    @if($client->projects->count() > 0)
    <div class="card-elevated rounded-apple-lg">
        <div class="px-4 py-2.5" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-semibold flex items-center" style="color: #FFFFFF;">
                    <i class="fas fa-folder mr-2 text-apple-orange"></i>Daftar Proyek ({{ $client->projects->count() }})
                </h3>
                <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn-primary px-3 py-1.5 text-white rounded-lg text-xs font-medium inline-flex items-center">
                    <i class="fas fa-plus mr-1.5"></i>Tambah Proyek
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="border-color: rgba(84, 84, 88, 0.65);">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase" style="color: rgba(235, 235, 245, 0.6);">Nama Proyek</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase" style="color: rgba(235, 235, 245, 0.6);">Status</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase" style="color: rgba(235, 235, 245, 0.6);">Deadline</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase" style="color: rgba(235, 235, 245, 0.6);">Nilai Kontrak</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-xs font-medium uppercase" style="color: rgba(235, 235, 245, 0.6);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="background-color: var(--dark-bg-secondary); border-color: rgba(84, 84, 88, 0.65);">
                    @foreach($client->projects as $project)
                        <tr class="hover-lift transition-apple">
                            <td class="px-4 py-3">
                                <div class="font-medium text-sm" style="color: #FFFFFF;">{{ $project->name }}</div>
                                @if($project->description)
                                    <div class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.6);">{{ Str::limit($project->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($project->status)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded" style="background-color: {{ $project->status->color }}33; color: {{ $project->status->color }};">
                                        {{ $project->status->name }}
                                    </span>
                                @else
                                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.6);">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                @if($project->deadline)
                                    {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs" style="color: #FFFFFF;">
                                Rp {{ number_format($project->contract_value ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <a href="{{ route('projects.show', $project) }}" 
                                   class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium transition-colors" 
                                   style="background: rgba(0, 122, 255, 0.15); color: var(--apple-blue);">
                                    <i class="fas fa-eye mr-1.5"></i>Detail
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
    <div class="card-elevated rounded-apple-lg mt-4">
        <div class="px-4 py-2.5" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <h3 class="text-sm font-semibold flex items-center" style="color: #FFFFFF;">
                <i class="fas fa-sticky-note mr-2 text-apple-purple"></i>Catatan
            </h3>
        </div>
        <div class="p-4">
            <p class="text-sm whitespace-pre-line" style="color: rgba(235, 235, 245, 0.8);">{{ $client->notes }}</p>
        </div>
    </div>
    @endif
</div>

<style>
    .info-group {
        padding-bottom: 0.75rem;
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
