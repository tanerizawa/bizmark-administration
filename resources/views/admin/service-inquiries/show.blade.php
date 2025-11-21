@extends('layouts.app')

@section('title', 'Detail Inquiry - ' . $serviceInquiry->inquiry_number)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center">
            <a href="{{ route('admin.service-inquiries.index') }}" class="text-apple-blue hover:text-blue-400 mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-dark-text-primary">
                    {{ $serviceInquiry->inquiry_number }}
                </h1>
                <div class="flex items-center space-x-3 text-xs mt-1 text-dark-text-secondary">
                    <span class="flex items-center">
                        <i class="fas fa-calendar-alt mr-1.5"></i>{{ $serviceInquiry->created_at->format('d M Y H:i') }}
                    </span>
                    @if($serviceInquiry->client)
                        <span class="flex items-center text-purple-400">
                            <i class="fas fa-user-check mr-1.5"></i>Sudah jadi klien
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            @if(!$serviceInquiry->client_id)
                <button onclick="document.getElementById('convertModal').classList.remove('hidden')" 
                        class="px-3 py-2 rounded-apple text-sm font-medium transition-colors inline-flex items-center bg-purple-500 hover:bg-purple-600 text-white">
                    <i class="fas fa-user-plus mr-1.5"></i>Konversi ke Klien
                </button>
            @endif
            <button onclick="if(confirm('Yakin ingin menghapus inquiry ini?')) document.getElementById('deleteForm').submit()" 
                    class="px-3 py-2 rounded-apple text-sm font-medium transition-colors inline-flex items-center bg-red-500 hover:bg-red-600 text-white">
                <i class="fas fa-trash mr-1.5"></i>Hapus
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="rounded-apple-lg p-4 mb-4" style="background-color: rgba(52, 199, 89, 0.15); border: 1px solid var(--apple-green);">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3" style="color: var(--apple-green);"></i>
                    <span class="text-sm font-medium" style="color: var(--apple-green);">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-sm" style="color: var(--apple-green); opacity: 0.6;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-apple-lg p-4 mb-4" style="background-color: rgba(255, 59, 48, 0.15); border: 1px solid var(--apple-red);">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3" style="color: var(--apple-red);"></i>
                    <span class="text-sm font-medium" style="color: var(--apple-red);">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-sm" style="color: var(--apple-red); opacity: 0.6;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Left Column -->
        <div class="space-y-4">
            <!-- Contact Information -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-user mr-2 text-apple-blue"></i>Informasi Kontak
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Nama Perusahaan</label>
                        <p class="text-sm text-dark-text-primary">{{ $serviceInquiry->company_name }}</p>
                    </div>
                    @if($serviceInquiry->company_type)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Tipe Perusahaan</label>
                            <p class="text-sm text-dark-text-primary">{{ $serviceInquiry->company_type }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Nama Kontak</label>
                        <p class="text-sm text-dark-text-primary">{{ $serviceInquiry->contact_person }}</p>
                    </div>
                    @if($serviceInquiry->position)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Posisi</label>
                            <p class="text-sm text-dark-text-primary">{{ $serviceInquiry->position }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Email</label>
                        <p class="text-sm text-dark-text-primary">
                            <a href="mailto:{{ $serviceInquiry->email }}" class="text-apple-blue hover:text-blue-400">
                                {{ $serviceInquiry->email }}
                            </a>
                        </p>
                    </div>
                    @if($serviceInquiry->phone)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Telepon</label>
                            <p class="text-sm text-dark-text-primary">
                                <a href="tel:{{ $serviceInquiry->phone }}" class="text-apple-blue hover:text-blue-400">
                                    {{ $serviceInquiry->phone }}
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Business Information -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-briefcase mr-2 text-apple-blue"></i>Informasi Bisnis
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Aktivitas Bisnis</label>
                        <p class="text-sm text-dark-text-primary">{{ $serviceInquiry->business_activity }}</p>
                    </div>
                    @if($serviceInquiry->kbli_code)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Kode KBLI</label>
                            <p class="text-sm text-dark-text-primary font-mono">{{ $serviceInquiry->kbli_code }}</p>
                        </div>
                    @endif
                    @if(isset($serviceInquiry->form_data['business_scale']))
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Skala Bisnis</label>
                            <p class="text-sm text-dark-text-primary">{{ ucfirst($serviceInquiry->form_data['business_scale']) }}</p>
                        </div>
                    @endif
                    @if(isset($serviceInquiry->form_data['location_province']))
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Lokasi</label>
                            <p class="text-sm text-dark-text-primary">
                                {{ $serviceInquiry->form_data['location_city'] ?? '' }}, {{ $serviceInquiry->form_data['location_province'] }}
                            </p>
                        </div>
                    @endif
                    @if(isset($serviceInquiry->form_data['location_category']))
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Kategori Lokasi</label>
                            <p class="text-sm text-dark-text-primary">{{ ucfirst($serviceInquiry->form_data['location_category']) }}</p>
                        </div>
                    @endif
                    @if(isset($serviceInquiry->form_data['estimated_investment']))
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Estimasi Investasi</label>
                            <p class="text-sm text-dark-text-primary">{{ $serviceInquiry->form_data['estimated_investment'] }}</p>
                        </div>
                    @endif
                    @if(isset($serviceInquiry->form_data['timeline']))
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Timeline</label>
                            <p class="text-sm text-dark-text-primary">{{ $serviceInquiry->form_data['timeline'] }}</p>
                        </div>
                    @endif
                    @if(isset($serviceInquiry->form_data['additional_notes']))
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Catatan Tambahan</label>
                            <p class="text-sm text-dark-text-primary whitespace-pre-wrap">{{ $serviceInquiry->form_data['additional_notes'] }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status & Priority Management -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-tasks mr-2 text-apple-blue"></i>Status & Prioritas
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <!-- Update Status -->
                    <form method="POST" action="{{ route('admin.service-inquiries.update-status', $serviceInquiry) }}">
                        @csrf
                        @method('PATCH')
                        <label class="text-xs font-medium text-dark-text-secondary block mb-2">Update Status</label>
                        <div class="flex gap-2">
                            <select name="status" class="flex-1 px-3 py-2 rounded-apple text-sm" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                                <option value="new" {{ $serviceInquiry->status == 'new' ? 'selected' : '' }}>Baru</option>
                                <option value="processing" {{ $serviceInquiry->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                                <option value="analyzed" {{ $serviceInquiry->status == 'analyzed' ? 'selected' : '' }}>Dianalisis</option>
                                <option value="contacted" {{ $serviceInquiry->status == 'contacted' ? 'selected' : '' }}>Dihubungi</option>
                                <option value="qualified" {{ $serviceInquiry->status == 'qualified' ? 'selected' : '' }}>Qualified</option>
                                <option value="converted" {{ $serviceInquiry->status == 'converted' ? 'selected' : '' }}>Konversi</option>
                                <option value="registered" {{ $serviceInquiry->status == 'registered' ? 'selected' : '' }}>Terdaftar</option>
                                <option value="lost" {{ $serviceInquiry->status == 'lost' ? 'selected' : '' }}>Lost</option>
                            </select>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium">
                                Update
                            </button>
                        </div>
                    </form>

                    <!-- Update Priority -->
                    <form method="POST" action="{{ route('admin.service-inquiries.update-priority', $serviceInquiry) }}">
                        @csrf
                        @method('PATCH')
                        <label class="text-xs font-medium text-dark-text-secondary block mb-2">Update Prioritas</label>
                        <div class="flex gap-2">
                            <select name="priority" class="flex-1 px-3 py-2 rounded-apple text-sm" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                                <option value="low" {{ $serviceInquiry->priority == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $serviceInquiry->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $serviceInquiry->priority == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium">
                                Update
                            </button>
                        </div>
                    </form>

                    <!-- Last Contacted -->
                    @if($serviceInquiry->last_contacted_at)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Terakhir Dihubungi</label>
                            <p class="text-sm text-dark-text-primary">
                                {{ $serviceInquiry->last_contacted_at->format('d M Y H:i') }}
                                @if($serviceInquiry->contactedBy)
                                    <span class="text-dark-text-secondary">oleh {{ $serviceInquiry->contactedBy->name }}</span>
                                @endif
                            </p>
                        </div>
                    @endif

                    <!-- Conversion Info -->
                    @if($serviceInquiry->converted_at)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Dikonversi Pada</label>
                            <p class="text-sm text-dark-text-primary">{{ $serviceInquiry->converted_at->format('d M Y H:i') }}</p>
                            @if($serviceInquiry->client)
                                <a href="{{ route('clients.show', $serviceInquiry->client) }}" class="text-apple-blue hover:text-blue-400 text-sm mt-1 inline-block">
                                    Lihat Klien →
                                </a>
                            @endif
                            @if($serviceInquiry->convertedToApplication)
                                <a href="{{ route('projects.show', $serviceInquiry->convertedToApplication->project_id) }}" class="text-apple-blue hover:text-blue-400 text-sm mt-1 inline-block ml-3">
                                    Lihat Proyek →
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-4">
            <!-- AI Analysis Results -->
            @if($serviceInquiry->ai_analysis)
                <div class="card-elevated rounded-apple-lg">
                    <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                        <h3 class="text-base font-semibold text-white flex items-center">
                            <i class="fas fa-robot mr-2 text-apple-blue"></i>Hasil Analisis AI
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <!-- Summary Stats -->
                        <div class="grid grid-cols-3 gap-3">
                            @if(isset($serviceInquiry->ai_analysis['estimated_total_cost']))
                                <div class="text-center">
                                    <div class="text-xs text-dark-text-secondary mb-1">Total Biaya</div>
                                    <div class="text-lg font-bold text-green-400">Rp {{ number_format($serviceInquiry->ai_analysis['estimated_total_cost'] / 1000000, 0) }}M</div>
                                </div>
                            @endif
                            @if(isset($serviceInquiry->ai_analysis['estimated_timeline']))
                                <div class="text-center">
                                    <div class="text-xs text-dark-text-secondary mb-1">Timeline</div>
                                    <div class="text-lg font-bold text-blue-400">{{ $serviceInquiry->ai_analysis['estimated_timeline'] }}</div>
                                </div>
                            @endif
                            @if(isset($serviceInquiry->ai_analysis['complexity_score']))
                                <div class="text-center">
                                    <div class="text-xs text-dark-text-secondary mb-1">Kompleksitas</div>
                                    <div class="text-lg font-bold text-orange-400">{{ $serviceInquiry->ai_analysis['complexity_score'] }}/10</div>
                                </div>
                            @endif
                        </div>

                        <!-- Recommended Permits -->
                        @if(isset($serviceInquiry->ai_analysis['recommended_permits']))
                            <div>
                                <label class="text-xs font-medium text-dark-text-secondary block mb-2">Izin yang Direkomendasikan</label>
                                <div class="space-y-2">
                                    @foreach($serviceInquiry->ai_analysis['recommended_permits'] as $permit)
                                        <div class="p-3 rounded-apple" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                                            <div class="flex items-start justify-between mb-1">
                                                <div class="font-medium text-sm text-dark-text-primary">{{ $permit['name'] ?? 'N/A' }}</div>
                                                @if(isset($permit['priority']))
                                                    @php
                                                        $priorityColors = [
                                                            'critical' => 'bg-red-500/20 text-red-400 border-red-500',
                                                            'high' => 'bg-orange-500/20 text-orange-400 border-orange-500',
                                                            'medium' => 'bg-blue-500/20 text-blue-400 border-blue-500',
                                                        ];
                                                    @endphp
                                                    <span class="text-xs px-2 py-0.5 rounded border {{ $priorityColors[$permit['priority']] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                                                        {{ ucfirst($permit['priority']) }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if(isset($permit['description']))
                                                <p class="text-xs text-dark-text-secondary mb-2">{{ $permit['description'] }}</p>
                                            @endif
                                            <div class="flex justify-between text-xs">
                                                @if(isset($permit['estimated_cost']))
                                                    <span class="text-dark-text-secondary">Biaya: <span class="text-green-400">Rp {{ number_format($permit['estimated_cost'] / 1000, 0) }}K</span></span>
                                                @endif
                                                @if(isset($permit['estimated_duration']))
                                                    <span class="text-dark-text-secondary">Durasi: <span class="text-blue-400">{{ $permit['estimated_duration'] }}</span></span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Risk Factors -->
                        @if(isset($serviceInquiry->ai_analysis['risk_factors']) && count($serviceInquiry->ai_analysis['risk_factors']) > 0)
                            <div>
                                <label class="text-xs font-medium text-dark-text-secondary block mb-2">Faktor Risiko</label>
                                <ul class="space-y-1">
                                    @foreach($serviceInquiry->ai_analysis['risk_factors'] as $risk)
                                        <li class="text-sm text-dark-text-primary flex items-start">
                                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2 mt-1 text-xs"></i>
                                            <span>{{ $risk }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Next Steps -->
                        @if(isset($serviceInquiry->ai_analysis['next_steps']) && count($serviceInquiry->ai_analysis['next_steps']) > 0)
                            <div>
                                <label class="text-xs font-medium text-dark-text-secondary block mb-2">Langkah Selanjutnya</label>
                                <ol class="space-y-1 list-decimal list-inside">
                                    @foreach($serviceInquiry->ai_analysis['next_steps'] as $step)
                                        <li class="text-sm text-dark-text-primary">{{ $step }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card-elevated rounded-apple-lg p-4 text-center">
                    <i class="fas fa-clock text-3xl text-dark-text-secondary opacity-30 mb-2"></i>
                    <p class="text-sm text-dark-text-secondary">Analisis AI belum tersedia</p>
                </div>
            @endif

            <!-- Admin Notes -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-apple-blue"></i>Catatan Admin
                    </h3>
                </div>
                <div class="p-4">
                    <!-- Add Note Form -->
                    <form method="POST" action="{{ route('admin.service-inquiries.add-note', $serviceInquiry) }}" class="mb-4">
                        @csrf
                        <textarea name="note" 
                                  rows="3" 
                                  class="w-full px-3 py-2 rounded-apple text-sm mb-2" 
                                  placeholder="Tambahkan catatan..."
                                  style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);"></textarea>
                        <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium w-full">
                            <i class="fas fa-plus mr-2"></i>Tambah Catatan
                        </button>
                    </form>

                    <!-- Existing Notes -->
                    @if($serviceInquiry->admin_notes)
                        <div class="space-y-2">
                            @foreach(array_filter(explode("\n\n", $serviceInquiry->admin_notes)) as $note)
                                <div class="p-3 rounded-apple text-sm" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                                    <p class="text-dark-text-primary whitespace-pre-wrap">{{ $note }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-dark-text-secondary text-center py-4">Belum ada catatan</p>
                    @endif
                </div>
            </div>

            <!-- Technical Info -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-info-circle mr-2 text-apple-blue"></i>Informasi Teknis
                    </h3>
                </div>
                <div class="p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-dark-text-secondary">IP Address:</span>
                        <span class="text-dark-text-primary font-mono">{{ $serviceInquiry->ip_address ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-dark-text-secondary">User Agent:</span>
                        <span class="text-dark-text-primary text-xs truncate max-w-xs">{{ $serviceInquiry->user_agent ?? '-' }}</span>
                    </div>
                    @if(isset($serviceInquiry->form_data['utm_source']))
                        <div class="flex justify-between">
                            <span class="text-dark-text-secondary">UTM Source:</span>
                            <span class="text-dark-text-primary">{{ $serviceInquiry->form_data['utm_source'] }}</span>
                        </div>
                    @endif
                    @if(isset($serviceInquiry->form_data['utm_medium']))
                        <div class="flex justify-between">
                            <span class="text-dark-text-secondary">UTM Medium:</span>
                            <span class="text-dark-text-primary">{{ $serviceInquiry->form_data['utm_medium'] }}</span>
                        </div>
                    @endif
                    @if(isset($serviceInquiry->form_data['utm_campaign']))
                        <div class="flex justify-between">
                            <span class="text-dark-text-secondary">UTM Campaign:</span>
                            <span class="text-dark-text-primary">{{ $serviceInquiry->form_data['utm_campaign'] }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Convert to Client Modal -->
<div id="convertModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="card-elevated rounded-apple-lg max-w-md w-full p-6">
        <h3 class="text-xl font-semibold text-white mb-4">Konversi ke Klien</h3>
        <form method="POST" action="{{ route('admin.service-inquiries.convert', $serviceInquiry) }}">
            @csrf
            <div class="mb-4">
                <label class="flex items-center text-sm text-dark-text-primary">
                    <input type="checkbox" name="create_client_account" value="1" checked class="mr-2" id="createAccountCheckbox">
                    <span>Buat akun klien baru</span>
                </label>
            </div>
            <div class="mb-4" id="passwordField">
                <label class="text-xs font-medium text-dark-text-secondary block mb-2">Password untuk Akun Klien</label>
                <input type="password" 
                       name="password" 
                       class="w-full px-3 py-2 rounded-apple text-sm" 
                       placeholder="Minimal 8 karakter"
                       style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
            </div>
            <div class="rounded-apple-lg p-3 mb-4" style="background-color: rgba(10, 132, 255, 0.1); border: 1px solid rgba(10, 132, 255, 0.3);">
                <p class="text-xs text-dark-text-secondary">
                    <i class="fas fa-info-circle text-apple-blue mr-1"></i>
                    Akan dibuat klien baru dan aplikasi permit dari data inquiry ini.
                </p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('convertModal').classList.add('hidden')" class="btn-secondary px-4 py-2 rounded-apple text-sm font-medium flex-1">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium flex-1">
                    Konversi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('admin.service-inquiries.destroy', $serviceInquiry) }}" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
// Toggle password field based on checkbox
document.getElementById('createAccountCheckbox').addEventListener('change', function() {
    document.getElementById('passwordField').style.display = this.checked ? 'block' : 'none';
});
</script>
@endsection
