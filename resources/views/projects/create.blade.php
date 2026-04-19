@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Tambah Proyek Baru</h1>
                    <p class="text-gray-400">Buat proyek perizinan baru dengan detail lengkap</p>
                </div>
                <a href="{{ route('projects.index') }}" 
                   class="px-4 py-2 bg-gray-700 text-white rounded-apple hover:bg-gray-600 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="mb-6 card-elevated rounded-apple-lg overflow-hidden border-l-4 border-apple-red">
                <div class="p-4 flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-apple-red text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-2">Terdapat {{ $errors->count() }} kesalahan pada form:</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-300">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Success Alert (if any) -->
        @if (session('success'))
            <div class="mb-6 card-elevated rounded-apple-lg overflow-hidden border-l-4 border-apple-green">
                <div class="p-4 flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-apple-green text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">{{ session('success') }}</h3>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="card-elevated rounded-apple-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-opacity-20" style="border-color: rgba(84, 84, 88, 0.65);">
                    <h3 class="text-lg font-semibold text-white">Informasi Proyek</h3>
                    <p class="text-sm mt-1 text-gray-400">Detail proyek, klien, dan status</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Project Name & Client Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Project Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium mb-2 text-gray-300">
                                Nama Proyek <span class="text-apple-red-dark">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-apple text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-apple-blue focus:border-transparent transition-all"
                                   placeholder="Contoh: Perizinan AMDAL PT. XYZ">
                            @error('name')
                                <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Client -->
                        <div>
                            <label for="client_id" class="block text-sm font-medium mb-2 text-gray-300">
                                Klien <span class="text-apple-red-dark">*</span>
                            </label>
                            <select name="client_id" 
                                    id="client_id" 
                                    required
                                    class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-apple text-white focus:outline-none focus:ring-2 focus:ring-apple-blue focus:border-transparent transition-all">
                                <option value="">Pilih Klien</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->company_name ?? $client->name }}
                                        @if($client->company_name && $client->name != $client->company_name)
                                            ({{ $client->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-gray-400 flex items-center">
                                <i class="fas fa-info-circle mr-1.5"></i>
                                Belum ada di list? 
                                <a href="{{ route('clients.create') }}" target="_blank" class="text-apple-blue hover:text-apple-blue-dark ml-1">
                                    Tambah Klien Baru
                                </a>
                            </p>
                        </div>
                    </div>

                    <!-- Status and Institution -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status_id" class="block text-sm font-medium mb-2 text-gray-300">
                                Status Awal <span class="text-apple-red-dark">*</span>
                            </label>
                            <select name="status_id" 
                                    id="status_id" 
                                    required
                                    class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-apple text-white focus:outline-none focus:ring-2 focus:ring-apple-blue focus:border-transparent transition-all">
                                <option value="">Pilih Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_id')
                                <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="institution_id" class="block text-sm font-medium mb-2 text-gray-300">
                                Institusi Terkait
                            </label>
                            <select name="institution_id" 
                                    id="institution_id" 
                                    class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-apple text-white focus:outline-none focus:ring-2 focus:ring-apple-blue focus:border-transparent transition-all">
                                <option value="">Pilih Institusi</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('institution_id')
                                <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium mb-2 text-gray-300">
                            Deskripsi Proyek
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-apple text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-apple-blue focus:border-transparent transition-all resize-none"
                                  placeholder="Jelaskan detail proyek perizinan ini...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Project Configuration -->
            <div class="card-elevated rounded-apple-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-opacity-20" style="border-color: rgba(84, 84, 88, 0.65);">
                    <h3 class="text-lg font-semibold text-white">Konfigurasi Proyek</h3>
                    <p class="text-sm mt-1 text-gray-400">Timeline, budget, dan pengaturan proyek</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium mb-2 text-gray-300">
                                Tanggal Mulai
                            </label>
                            <input type="date" 
                                   name="start_date" 
                                   id="start_date" 
                                   value="{{ old('start_date') }}"
                                   class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-apple text-white focus:outline-none focus:ring-2 focus:ring-apple-blue focus:border-transparent transition-all">
                            @error('start_date')
                                <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="deadline" class="block text-sm font-medium mb-2 text-gray-300">
                                Target Selesai
                            </label>
                            <input type="date" 
                                   name="deadline" 
                                   id="deadline" 
                                   value="{{ old('deadline') }}"
                                   class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-apple text-white focus:outline-none focus:ring-2 focus:ring-apple-blue focus:border-transparent transition-all">
                            @error('deadline')
                                <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Budget -->
                    <div>
                        <label for="budget" class="block text-sm font-medium mb-2 text-gray-300">
                            Budget (Rp)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">Rp</span>
                            <input type="number" 
                                   name="budget" 
                                   id="budget" 
                                   value="{{ old('budget') }}"
                                   step="1000"
                                   class="w-full pl-12 pr-4 py-2.5 bg-gray-800 border border-gray-700 rounded-apple text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-apple-blue focus:border-transparent transition-all"
                                   placeholder="0">
                        </div>
                        @error('budget')
                            <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium mb-3 text-gray-300">
                            Prioritas Proyek
                        </label>
                        <div class="flex gap-4">
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" 
                                       name="priority" 
                                       value="low" 
                                       {{ old('priority') == 'low' ? 'checked' : '' }}
                                       class="w-4 h-4 text-apple-blue border-gray-700 focus:ring-apple-blue focus:ring-offset-gray-900">
                                <span class="ml-2 text-sm text-gray-300 group-hover:text-white transition-colors">Rendah</span>
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" 
                                       name="priority" 
                                       value="medium" 
                                       {{ old('priority', 'medium') == 'medium' ? 'checked' : '' }}
                                       class="w-4 h-4 text-apple-blue border-gray-700 focus:ring-apple-blue focus:ring-offset-gray-900">
                                <span class="ml-2 text-sm text-gray-300 group-hover:text-white transition-colors">Sedang</span>
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" 
                                       name="priority" 
                                       value="high" 
                                       {{ old('priority') == 'high' ? 'checked' : '' }}
                                       class="w-4 h-4 text-apple-blue border-gray-700 focus:ring-apple-blue focus:ring-offset-gray-900">
                                <span class="ml-2 text-sm text-gray-300 group-hover:text-white transition-colors">Tinggi</span>
                            </label>
                        </div>
                        @error('priority')
                            <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium mb-2 text-gray-300">
                            Catatan Tambahan
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3"
                                  class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-apple text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-apple-blue focus:border-transparent transition-all resize-none"
                                  placeholder="Catatan internal atau informasi penting lainnya...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('projects.index') }}" 
                   class="px-6 py-2.5 bg-gray-700 text-white rounded-apple hover:bg-gray-600 transition-all duration-200 font-medium">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-apple-blue text-white rounded-apple hover:bg-apple-blue-dark transition-all duration-200 font-medium shadow-lg hover:shadow-xl flex items-center gap-2"
                        id="submitBtn">
                    <i class="fas fa-plus"></i>
                    <span>Buat Proyek</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    
    // Form validation on submit
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        let firstInvalidField = null;
        
        // Clear previous error highlights
        requiredFields.forEach(field => {
            field.classList.remove('border-red-500', 'border-2');
        });
        
        // Validate required fields
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500', 'border-2');
                
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            
            // Show error notification
            showNotification('error', 'Mohon lengkapi semua field yang bertanda * (wajib diisi)');
            
            // Scroll to first invalid field
            if (firstInvalidField) {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidField.focus();
            }
            
            return false;
        }
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Menyimpan...</span>';
    });
    
    // Remove error highlight on input
    form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('border-red-500', 'border-2');
        });
    });
    
    // Notification function
    function showNotification(type, message) {
        const colors = {
            error: { bg: 'rgba(255, 59, 48, 0.1)', border: '#FF3B30', icon: 'exclamation-circle' },
            success: { bg: 'rgba(52, 199, 89, 0.1)', border: '#34C759', icon: 'check-circle' },
            warning: { bg: 'rgba(255, 149, 0, 0.1)', border: '#FF9500', icon: 'exclamation-triangle' }
        };
        
        const color = colors[type] || colors.error;
        
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-50 max-w-md animate-slide-in';
        notification.style.cssText = `
            background-color: ${color.bg};
            border-left: 4px solid ${color.border};
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        `;
        notification.innerHTML = `
            <div class="card-elevated rounded-apple-lg p-4 flex items-start gap-3 shadow-2xl">
                <div class="flex-shrink-0">
                    <i class="fas fa-${color.icon}" style="color: ${color.border}; font-size: 1.25rem;"></i>
                </div>
                <div class="flex-1">
                    <p class="text-white font-medium">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
    
    // Auto-hide alerts after page load
    document.querySelectorAll('.alert-auto-hide').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
</script>

<style>
@keyframes slide-in {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

/* Enhanced focus state for required fields */
input[required]:focus,
select[required]:focus,
textarea[required]:focus {
    box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.2) !important;
}

/* Invalid field animation */
.border-red-500 {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}
</style>
@endsection
