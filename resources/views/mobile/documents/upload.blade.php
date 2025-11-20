@extends('mobile.layouts.app')

@section('title', 'Upload Dokumen')

@section('header-actions')
<button onclick="history.back()" class="text-white hover:bg-white/20 w-9 h-9 rounded-full flex items-center justify-center transition-all active:scale-95">
    <i class="fas fa-times text-lg"></i>
</button>
@endsection

@section('content')
<div x-data="documentUpload()" class="pb-20">
    <form @submit.prevent="submitUpload" class="p-4 space-y-4">
        
        {{-- Project Selection --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Project <span class="text-red-500">*</span>
            </label>
            <select 
                x-model="formData.project_id"
                required
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
                <option value="">Pilih Project...</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Judul Dokumen <span class="text-red-500">*</span>
            </label>
            <input 
                x-model="formData.title"
                type="text" 
                required
                placeholder="Misal: Proposal Pendirian PT"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
        </div>

        {{-- Category --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Kategori <span class="text-red-500">*</span>
            </label>
            <select 
                x-model="formData.category"
                required
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
                <option value="">Pilih Kategori...</option>
                <option value="permit">Perizinan</option>
                <option value="proposal">Proposal</option>
                <option value="agreement">Perjanjian</option>
                <option value="report">Laporan</option>
                <option value="other">Lainnya</option>
            </select>
        </div>

        {{-- File Upload --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                File <span class="text-red-500">*</span>
            </label>
            
            {{-- Custom File Input --}}
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                <input 
                    type="file" 
                    @change="handleFileChange"
                    accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
                    capture="environment"
                    class="hidden" 
                    id="fileInput"
                    required>
                
                <template x-if="!fileName">
                    <div>
                        <i class="fas fa-camera text-gray-400 text-3xl mb-2"></i>
                        <p class="text-sm text-gray-600 mb-2">Ambil foto atau pilih file</p>
                        <div class="flex gap-2 justify-center">
                            <button 
                                type="button"
                                @click="$('#fileInput').attr('capture', 'environment').click()"
                                class="px-4 py-2 bg-[#0077b5] text-white rounded-lg text-sm font-medium active:scale-95 transition-all">
                                <i class="fas fa-camera mr-1"></i> Kamera
                            </button>
                            <button 
                                type="button"
                                @click="$('#fileInput').removeAttr('capture').click()"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium active:scale-95 transition-all">
                                <i class="fas fa-folder mr-1"></i> File
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Max 10MB (PDF, Word, Excel, Gambar)</p>
                    </div>
                </template>
                
                <template x-if="fileName">
                    <div>
                        <i class="fas fa-file text-[#0077b5] text-3xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-900" x-text="fileName"></p>
                        <p class="text-xs text-gray-500" x-text="fileSize"></p>
                        <button 
                            type="button"
                            @click="clearFile"
                            class="mt-2 text-sm text-red-600 hover:text-red-700">
                            <i class="fas fa-times mr-1"></i> Hapus
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Keterangan (opsional)
            </label>
            <textarea 
                x-model="formData.description"
                rows="3"
                maxlength="500"
                placeholder="Catatan tambahan tentang dokumen ini"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent"></textarea>
            <p class="text-xs text-gray-500 mt-1" x-text="`${formData.description.length}/500`"></p>
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
                :disabled="uploading"
                class="flex-1 px-4 py-3 bg-[#0077b5] hover:bg-[#005582] text-white rounded-lg font-medium transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!uploading">
                    <i class="fas fa-upload mr-1"></i> Upload
                </span>
                <span x-show="uploading">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Uploading...
                </span>
            </button>
        </div>
    </form>
</div>

<script>
function documentUpload() {
    return {
        formData: {
            project_id: '',
            title: '',
            category: '',
            description: '',
            file: null
        },
        fileName: '',
        fileSize: '',
        uploading: false,

        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.formData.file = file;
                this.fileName = file.name;
                this.fileSize = this.formatFileSize(file.size);
            }
        },

        clearFile() {
            this.formData.file = null;
            this.fileName = '';
            this.fileSize = '';
            document.getElementById('fileInput').value = '';
        },

        formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },

        async submitUpload() {
            if (this.uploading) return;

            // Validate
            if (!this.formData.project_id || !this.formData.title || !this.formData.category || !this.formData.file) {
                alert('Mohon lengkapi semua field yang required');
                return;
            }

            this.uploading = true;

            try {
                const formData = new FormData();
                formData.append('project_id', this.formData.project_id);
                formData.append('title', this.formData.title);
                formData.append('category', this.formData.category);
                formData.append('description', this.formData.description);
                formData.append('file', this.formData.file);

                const response = await fetch('{{ route("mobile.documents.store") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Show success
                    this.showToast('Dokumen berhasil diupload!', 'success');
                    
                    // Redirect after delay
                    setTimeout(() => {
                        window.location.href = '{{ route("mobile.documents.index") }}';
                    }, 1000);
                } else {
                    this.showToast(data.message || 'Gagal mengupload dokumen', 'error');
                }

            } catch (error) {
                console.error('Upload error:', error);
                this.showToast('Terjadi kesalahan jaringan', 'error');
            } finally {
                this.uploading = false;
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
