@extends('layouts.app')

@section('title', 'Tambah Proyek Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Tambah Proyek Baru</h1>
            <p class="text-gray-600 mt-1">Buat proyek perizinan baru</p>
        </div>
    </div>

    <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>Informasi Dasar
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Proyek <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Contoh: Perizinan IMB Gedung Perkantoran">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Proyek <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Jelaskan detail proyek perizinan...">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Institusi Tujuan <span class="text-red-500">*</span>
                    </label>
                    <select id="institution_id" name="institution_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('institution_id') border-red-500 @enderror">
                        <option value="">Pilih Institusi</option>
                        @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                            {{ $institution->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('institution_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Status Initial <span class="text-red-500">*</span>
                    </label>
                    <select id="status_id" name="status_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status_id') border-red-500 @enderror">
                        <option value="">Pilih Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('status_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user mr-2 text-blue-600"></i>Informasi Klien
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="client_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Klien <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('client_name') border-red-500 @enderror"
                           placeholder="Nama perusahaan atau individu">
                    @error('client_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="client_contact" class="block text-sm font-medium text-gray-700 mb-2">
                        Kontak Klien <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="client_contact" name="client_contact" value="{{ old('client_contact') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('client_contact') border-red-500 @enderror"
                           placeholder="Nomor telepon atau email">
                    @error('client_contact')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="client_address" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Klien
                    </label>
                    <textarea id="client_address" name="client_address" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('client_address') border-red-500 @enderror"
                              placeholder="Alamat lengkap klien">{{ old('client_address') }}</textarea>
                    @error('client_address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Project Schedule -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-calendar mr-2 text-blue-600"></i>Jadwal Proyek
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">
                        Target Selesai
                    </label>
                    <input type="date" id="deadline" name="deadline" value="{{ old('deadline') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('deadline') border-red-500 @enderror">
                    @error('deadline')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                        Progress Initial (%)
                    </label>
                    <input type="number" id="progress_percentage" name="progress_percentage" 
                           value="{{ old('progress_percentage', 0) }}" min="0" max="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('progress_percentage') border-red-500 @enderror">
                    @error('progress_percentage')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Financial Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-dollar-sign mr-2 text-blue-600"></i>Informasi Keuangan
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">
                        Budget Proyek
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                        <input type="number" id="budget" name="budget" value="{{ old('budget') }}" step="0.01"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('budget') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('budget')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="actual_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        Biaya Aktual
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                        <input type="number" id="actual_cost" name="actual_cost" value="{{ old('actual_cost', 0) }}" step="0.01"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('actual_cost') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('actual_cost')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-sticky-note mr-2 text-blue-600"></i>Catatan Tambahan
            </h3>
            
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea id="notes" name="notes" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror"
                          placeholder="Catatan khusus, persyaratan, atau informasi penting lainnya...">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('projects.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Proyek
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Auto-calculate deadline based on start date (optional feature)
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        const deadlineField = document.getElementById('deadline');
        
        if (!deadlineField.value && startDate) {
            // Set default deadline to 30 days from start date
            const deadline = new Date(startDate);
            deadline.setDate(deadline.getDate() + 30);
            deadlineField.value = deadline.toISOString().split('T')[0];
        }
    });

    // Format currency inputs
    function formatCurrency(input) {
        input.addEventListener('input', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = parseInt(value).toLocaleString('id-ID');
            }
        });
    }

    // Apply currency formatting (optional - for display purposes)
    // formatCurrency(document.getElementById('budget'));
    // formatCurrency(document.getElementById('actual_cost'));
</script>
@endpush
@endsection