@extends('mobile.layouts.app')

@section('title', 'Proyek Baru')

@section('header-actions')
<button onclick="history.back()" class="text-white hover:bg-white/20 w-9 h-9 rounded-full flex items-center justify-center transition-all active:scale-95">
    <i class="fas fa-times text-lg"></i>
</button>
@endsection

@section('content')
<div x-data="projectCreate()" class="pb-20">
    <form @submit.prevent="submitProject" class="p-4 space-y-4">
        
        {{-- Project Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nama Proyek <span class="text-red-500">*</span>
            </label>
            <input 
                x-model="formData.name"
                type="text" 
                required
                placeholder="Misal: Pendirian PT Maju Jaya"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
        </div>

        {{-- Client Selection --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Klien <span class="text-red-500">*</span>
            </label>
            <select 
                x-model="formData.client_id"
                required
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
                <option value="">Pilih Klien...</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Institution Selection --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Instansi/Lembaga <span class="text-red-500">*</span>
            </label>
            <select 
                x-model="formData.institution_id"
                required
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
                <option value="">Pilih Instansi...</option>
                @foreach($institutions as $institution)
                    <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Deadline --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Target Selesai <span class="text-red-500">*</span>
            </label>
            <input 
                x-model="formData.deadline"
                type="date" 
                required
                :min="new Date().toISOString().split('T')[0]"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
        </div>

        {{-- Budget (Optional) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Budget (Opsional)
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                <input 
                    x-model="formData.budget"
                    type="number" 
                    min="0"
                    step="1000"
                    placeholder="0"
                    class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
            </div>
        </div>

        {{-- Description (Optional) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Deskripsi (Opsional)
            </label>
            <textarea 
                x-model="formData.description"
                rows="3"
                maxlength="1000"
                placeholder="Deskripsi singkat tentang proyek ini"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent"></textarea>
            <p class="text-xs text-gray-500 mt-1" x-text="`${formData.description.length}/1000`"></p>
        </div>

        {{-- Info Box --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
            <div class="flex gap-2">
                <i class="fas fa-info-circle text-[#0077b5] mt-0.5"></i>
                <div class="text-sm text-gray-700">
                    <p class="font-medium mb-1">Info Penting:</p>
                    <ul class="text-xs space-y-0.5 list-disc list-inside">
                        <li>Proyek akan dibuat dengan status aktif</li>
                        <li>Progress awal 0%</li>
                        <li>Tanggal mulai otomatis hari ini</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="flex gap-2 pt-2">
            <button 
                type="button"
                @click="history.back()"
                class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all active:scale-95">
                Batal
            </button>
            <button 
                type="submit"
                :disabled="submitting"
                class="flex-1 px-4 py-3 bg-[#0077b5] hover:bg-[#005582] text-white rounded-lg font-medium transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!submitting">
                    <i class="fas fa-folder-plus mr-1"></i> Buat Proyek
                </span>
                <span x-show="submitting">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...
                </span>
            </button>
        </div>
    </form>
</div>

<script>
function projectCreate() {
    return {
        formData: {
            name: '',
            client_id: '',
            institution_id: '',
            deadline: '',
            budget: '',
            description: ''
        },
        submitting: false,

        async submitProject() {
            if (this.submitting) return;

            // Validate required fields
            if (!this.formData.name || !this.formData.client_id || !this.formData.institution_id || !this.formData.deadline) {
                alert('Mohon lengkapi semua field yang required');
                return;
            }

            this.submitting = true;

            try {
                const response = await fetch('{{ route("mobile.projects.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Show success
                    this.showToast('Proyek berhasil dibuat!', 'success');
                    
                    // Redirect to project detail
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    this.showToast(data.message || 'Gagal membuat proyek', 'error');
                }

            } catch (error) {
                console.error('Project create error:', error);
                this.showToast('Terjadi kesalahan jaringan', 'error');
            } finally {
                this.submitting = false;
            }
        },

        showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-20 left-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-gray-800'
            } text-white`;
            toast.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            setTimeout(() => toast.style.transform = 'translateY(-10px)', 10);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }
}
</script>
@endsection
