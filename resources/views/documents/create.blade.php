@extends('layouts.app')

@section('title', 'Upload Dokumen Baru')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('documents.index') }}" 
               class="text-apple-blue-dark hover:text-apple-blue">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold" style="color: #FFFFFF;">Upload Dokumen Baru</h1>
        </div>
        <p style="color: rgba(235, 235, 245, 0.6);">Unggah dokumen perizinan atau berkas proyek</p>
    </div>

    <!-- Form -->
    <div class="card-elevated rounded-apple-lg">
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- File Upload Section -->
            <div class="pb-6" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                <h3 class="text-lg font-medium mb-4" style="color: #FFFFFF;">File Dokumen</h3>
                
                <div>
                    <label for="document_file" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Upload File <span class="text-apple-red-dark">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md hover:border-gray-400 transition-colors" style="border-color: rgba(84, 84, 88, 0.65);">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-cloud-upload-alt text-3xl" style="color: rgba(142, 142, 147, 0.8);"></i>
                            <div class="flex text-sm" style="color: rgba(235, 235, 245, 0.6);">
                                <label for="document_file" class="relative cursor-pointer rounded-md font-semibold focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-apple-blue" style="color: #0A84FF; text-decoration: underline;">
                                    <span>Klik di sini untuk upload file</span>
                                    <input id="document_file" 
                                           name="document_file" 
                                           type="file" 
                                           class="sr-only" 
                                           required
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.rar">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                PDF, DOC, XLS, gambar, ZIP hingga 10MB
                            </p>
                        </div>
                    </div>
                    @error('document_file')
                        <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- File Preview -->
                    <div id="file-preview" class="mt-4 hidden">
                        <div class="flex items-center p-3 rounded-md" style="background: rgba(58, 58, 60, 0.5);">
                            <i class="fas fa-file mr-3" style="color: rgba(142, 142, 147, 0.8);"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.8);" id="file-name"></p>
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);" id="file-size"></p>
                            </div>
                            <button type="button" onclick="removeFile()" class="text-apple-red-dark hover:text-apple-red">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="pb-6" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                <h3 class="text-lg font-medium mb-4" style="color: #FFFFFF;">Informasi Dokumen</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Project -->
                    <div class="md:col-span-2">
                        <label for="project_id" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Proyek <span class="text-apple-red-dark">*</span>
                        </label>
                        <select name="project_id" 
                                id="project_id" 
                                required
                                onchange="loadTasks(this.value)"
                                class="input-dark w-full px-3 py-2 rounded-md @error('project_id') ring-2 ring-apple-red @enderror">
                            <option value="">Pilih Proyek</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" 
                                        {{ old('project_id', $selectedProject?->id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }} - {{ $project->client_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Task (Optional) -->
                    <div class="md:col-span-2">
                        <label for="task_id" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Task Terkait (Opsional)</label>
                        <select name="task_id" 
                                id="task_id"
                                class="input-dark w-full px-3 py-2 rounded-md @error('task_id') ring-2 ring-apple-red @enderror">
                            <option value="">Pilih Task</option>
                            @if($selectedProject && $tasks->count() > 0)
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" {{ old('task_id', $selectedTask?->id) == $task->id ? 'selected' : '' }}>
                                        {{ $task->title }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('task_id')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Judul Dokumen <span class="text-apple-red-dark">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title"
                               value="{{ old('title') }}"
                               required
                               placeholder="Masukkan judul dokumen..."
                               class="input-dark w-full px-3 py-2 rounded-md @error('title') ring-2 ring-apple-red @enderror">
                        @error('title')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Kategori <span class="text-apple-red-dark">*</span>
                        </label>
                        <select name="category" 
                                id="category" 
                                required
                                class="input-dark w-full px-3 py-2 rounded-md @error('category') ring-2 ring-apple-red @enderror">
                            <option value="">Pilih Kategori</option>
                            <option value="proposal" {{ old('category') == 'proposal' ? 'selected' : '' }}>Proposal</option>
                            <option value="kontrak" {{ old('category') == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                            <option value="kajian" {{ old('category') == 'kajian' ? 'selected' : '' }}>Kajian</option>
                            <option value="surat" {{ old('category') == 'surat' ? 'selected' : '' }}>Surat</option>
                            <option value="sk" {{ old('category') == 'sk' ? 'selected' : '' }}>SK/Izin</option>
                            <option value="laporan" {{ old('category') == 'laporan' ? 'selected' : '' }}>Laporan</option>
                            <option value="gambar" {{ old('category') == 'gambar' ? 'selected' : '' }}>Gambar/Desain</option>
                            <option value="lainnya" {{ old('category') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('category')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Status <span class="text-apple-red-dark">*</span>
                        </label>
                        <select name="status" 
                                id="status" 
                                required
                                class="input-dark w-full px-3 py-2 rounded-md @error('status') ring-2 ring-apple-red @enderror">
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="review" {{ old('status') == 'review' ? 'selected' : '' }}>Review</option>
                            <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="final" {{ old('status') == 'final' ? 'selected' : '' }}>Final</option>
                        </select>
                        @error('status')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Document Date -->
                    <div>
                        <label for="document_date" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Tanggal Dokumen</label>
                        <input type="date" 
                               name="document_date" 
                               id="document_date"
                               value="{{ old('document_date') }}"
                               class="input-dark w-full px-3 py-2 rounded-md @error('document_date') ring-2 ring-apple-red @enderror">
                        @error('document_date')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confidential -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_confidential" 
                                   value="1"
                                   {{ old('is_confidential') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-apple-blue shadow-sm focus:border-apple-blue focus:ring focus:ring-apple-blue focus:ring-opacity-50">
                            <span class="ml-2 text-sm" style="color: rgba(235, 235, 245, 0.8);">Dokumen Rahasia</span>
                        </label>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Deskripsi</label>
                        <textarea name="description" 
                                  id="description"
                                  rows="4"
                                  placeholder="Deskripsi dokumen..."
                                  class="input-dark w-full px-3 py-2 rounded-md @error('description') ring-2 ring-apple-red @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Catatan</label>
                        <textarea name="notes" 
                                  id="notes"
                                  rows="3"
                                  placeholder="Catatan tambahan..."
                                  class="input-dark w-full px-3 py-2 rounded-md @error('notes') ring-2 ring-apple-red @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6" style="border-top: 1px solid rgba(84, 84, 88, 0.65);">
                <a href="{{ route('documents.index') }}" 
                   class="px-6 py-2 rounded-md font-medium transition-colors" style="border: 1px solid rgba(84, 84, 88, 0.65); color: rgba(235, 235, 245, 0.8); background: rgba(58, 58, 60, 0.6);">
                    Batal
                </a>
                <button type="submit" 
                        class="btn-primary px-6 py-2 rounded-md font-medium transition-colors">
                    <i class="fas fa-upload mr-2"></i>
                    Upload Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('document_file');
    const dropArea = fileInput.closest('.border-dashed');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    
    // File preview functionality
    fileInput.addEventListener('change', function(e) {
        handleFile(e.target.files[0]);
    });
    
    // Drag and drop functionality
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight(e) {
        dropArea.style.borderColor = '#0A84FF';
        dropArea.style.backgroundColor = 'rgba(10, 132, 255, 0.05)';
    }
    
    function unhighlight(e) {
        dropArea.style.borderColor = 'rgba(84, 84, 88, 0.65)';
        dropArea.style.backgroundColor = 'transparent';
    }
    
    dropArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            fileInput.files = files;
            handleFile(files[0]);
        }
    }
    
    function handleFile(file) {
        if (file) {
            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 10MB.');
                return;
            }
            
            // Validate file type
            const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            
            if (!allowedExtensions.includes(fileExtension)) {
                alert('Tipe file tidak diizinkan! Hanya PDF, DOC, XLS, gambar, dan ZIP yang diperbolehkan.');
                return;
            }
            
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            filePreview.classList.remove('hidden');
            
            // Auto-fill title if empty
            const titleInput = document.getElementById('title');
            if (!titleInput.value) {
                const fileNameWithoutExt = file.name.replace(/\.[^/.]+$/, ""); // Remove extension
                titleInput.value = fileNameWithoutExt;
            }
        }
    }
    
    window.removeFile = function() {
        fileInput.value = '';
        filePreview.classList.add('hidden');
    };
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Load tasks by project
    window.loadTasks = function(projectId) {
        const taskSelect = document.getElementById('task_id');
        taskSelect.innerHTML = '<option value="">Pilih Task</option>';
        
        if (projectId) {
            fetch(`{{ route('api.tasks-by-project') }}?project_id=${projectId}`)
                .then(response => response.json())
                .then(tasks => {
                    tasks.forEach(task => {
                        const option = document.createElement('option');
                        option.value = task.id;
                        option.textContent = task.title;
                        taskSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading tasks:', error));
        }
    };
});
</script>
@endsection