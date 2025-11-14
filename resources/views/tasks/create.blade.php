@extends('layouts.app')

@section('title', 'Tambah Tugas Baru')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-4">
                <a href="{{ route('tasks.index') }}" 
                   class="inline-flex items-center justify-center w-10 h-10 rounded-apple bg-dark-bg-tertiary hover:bg-dark-bg-quaternary text-dark-text-secondary hover:text-dark-text-primary transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-dark-text-primary">Tambah Tugas Baru</h1>
                    <p class="text-sm text-dark-text-secondary mt-1">Buat tugas baru untuk proyek perizinan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="card-apple p-4 mb-6 bg-green-500 bg-opacity-10 border border-green-500">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                <div class="flex-grow">
                    <p class="text-green-500 font-medium">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-500 hover:text-green-400">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="card-apple p-4 mb-6 bg-red-500 bg-opacity-10 border border-red-500">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                <div class="flex-grow">
                    <p class="text-red-500 font-medium mb-2">Terdapat kesalahan pada form:</p>
                    <ul class="text-sm text-red-400 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('tasks.store') }}" method="POST" id="taskForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form - 2 columns -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="card-apple p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-dark-separator">
                        <div class="w-10 h-10 rounded-lg bg-apple-blue bg-opacity-10 flex items-center justify-center">
                            <i class="fas fa-info-circle text-apple-blue"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-dark-text-primary">Informasi Dasar</h3>
                            <p class="text-xs text-dark-text-tertiary">Detail utama tugas</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <!-- Project Selection -->
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                Proyek <span class="text-red-500">*</span>
                            </label>
                            <select name="project_id" 
                                    id="project_id" 
                                    required
                                    class="input-apple w-full @error('project_id') ring-2 ring-red-500 @enderror">
                                <option value="">Pilih Proyek</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" 
                                            data-client="{{ $project->client_name }}"
                                            data-status="{{ $project->status->name ?? 'N/A' }}"
                                            {{ old('project_id', $selectedProject?->id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            
                            <!-- Project Info Preview -->
                            <div id="projectInfo" class="hidden mt-3 p-3 rounded-lg bg-dark-bg-tertiary">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-briefcase text-dark-text-tertiary mt-1"></i>
                                    <div class="text-sm">
                                        <p class="text-dark-text-secondary">
                                            <span class="font-medium">Client:</span> <span id="clientName">-</span>
                                        </p>
                                        <p class="text-dark-text-secondary mt-1">
                                            <span class="font-medium">Status:</span> <span id="projectStatus">-</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Task Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                Judul Tugas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   value="{{ old('title') }}"
                                   required
                                   maxlength="255"
                                   placeholder="Misal: Mengurus izin IMB ke Dinas PU"
                                   class="input-apple w-full @error('title') ring-2 ring-red-500 @enderror">
                            <div class="flex items-center justify-between mt-1.5">
                                @error('title')
                                    <p class="text-red-500 text-xs flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </p>
                                @else
                                    <p class="text-xs text-dark-text-tertiary">Gunakan judul yang jelas dan deskriptif</p>
                                @enderror
                                <span class="text-xs text-dark-text-tertiary"><span id="titleCount">0</span>/255</span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                Deskripsi
                                <span class="text-xs text-dark-text-tertiary font-normal ml-2">(Opsional)</span>
                            </label>
                            <textarea name="description" 
                                      id="description"
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="Jelaskan detail tugas, langkah-langkah yang perlu dilakukan, atau informasi penting lainnya..."
                                      class="input-apple w-full @error('description') ring-2 ring-red-500 @enderror">{{ old('description') }}</textarea>
                            <div class="flex items-center justify-between mt-1.5">
                                @error('description')
                                    <p class="text-red-500 text-xs flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </p>
                                @else
                                    <p class="text-xs text-dark-text-tertiary">Tambahkan konteks yang berguna untuk executor</p>
                                @enderror
                                <span class="text-xs text-dark-text-tertiary"><span id="descCount">0</span>/1000</span>
                            </div>
                        </div>

                        <!-- SOP Notes / Checklist -->
                        <div>
                            <label for="sop_notes" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                SOP / Checklist
                                <span class="text-xs text-dark-text-tertiary font-normal ml-2">(Opsional)</span>
                            </label>
                            <textarea name="sop_notes" 
                                      id="sop_notes"
                                      rows="5"
                                      placeholder="Contoh:&#10;☐ Siapkan dokumen persyaratan&#10;☐ Fotokopi KTP dan NPWP&#10;☐ Upload ke sistem online&#10;☐ Tunggu verifikasi (3-5 hari kerja)&#10;☐ Ambil dokumen fisik"
                                      class="input-apple w-full font-mono text-sm @error('sop_notes') ring-2 ring-red-500 @enderror">{{ old('sop_notes') }}</textarea>
                            @error('sop_notes')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @else
                                <div class="mt-2 p-2 rounded bg-dark-bg-tertiary">
                                    <p class="text-xs text-dark-text-tertiary">
                                        <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                                        <strong>Tips:</strong> Gunakan ☐ untuk checkbox, - untuk bullet, atau nomor 1. 2. 3. untuk langkah berurutan
                                    </p>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Assignment & Timeline Card -->
                <div class="card-apple p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-dark-separator">
                        <div class="w-10 h-10 rounded-lg bg-purple-500 bg-opacity-10 flex items-center justify-center">
                            <i class="fas fa-calendar-check text-purple-500"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-dark-text-primary">Penugasan & Timeline</h3>
                            <p class="text-xs text-dark-text-tertiary">Siapa dan kapan tugas dikerjakan</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Assigned User -->
                        <div>
                            <label for="assigned_user_id" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                Ditugaskan Kepada
                            </label>
                            <select name="assigned_user_id" 
                                    id="assigned_user_id"
                                    class="input-apple w-full @error('assigned_user_id') ring-2 ring-red-500 @enderror">
                                <option value="">Belum ditugaskan</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_user_id')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @else
                                <p class="text-xs text-dark-text-tertiary mt-1.5">Bisa diisi nanti setelah task dibuat</p>
                            @enderror
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                Tanggal Jatuh Tempo
                            </label>
                            <input type="date" 
                                   name="due_date" 
                                   id="due_date"
                                   value="{{ old('due_date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="input-apple w-full @error('due_date') ring-2 ring-red-500 @enderror">
                            @error('due_date')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @else
                                <p class="text-xs text-dark-text-tertiary mt-1.5" id="daysUntilDue"></p>
                            @enderror
                        </div>

                        <!-- Estimated Hours -->
                        <div>
                            <label for="estimated_hours" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                Estimasi Jam Kerja
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       name="estimated_hours" 
                                       id="estimated_hours"
                                       value="{{ old('estimated_hours') }}"
                                       min="1"
                                       max="999"
                                       placeholder="8"
                                       class="input-apple w-full pr-16 @error('estimated_hours') ring-2 ring-red-500 @enderror">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-dark-text-tertiary">
                                    jam
                                </span>
                            </div>
                            @error('estimated_hours')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @else
                                <p class="text-xs text-dark-text-tertiary mt-1.5">Perkiraan waktu pengerjaan</p>
                            @enderror
                        </div>

                        <!-- Institution -->
                        <div>
                            <label for="institution_id" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                Institusi Terkait
                            </label>
                            <select name="institution_id" 
                                    id="institution_id"
                                    class="input-apple w-full @error('institution_id') ring-2 ring-red-500 @enderror">
                                <option value="">Tidak ada</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('institution_id')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @else
                                <p class="text-xs text-dark-text-tertiary mt-1.5">Misal: Dinas PU, BPN, dll</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - 1 column -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Status & Priority Card -->
                <div class="card-apple p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-dark-separator">
                        <div class="w-10 h-10 rounded-lg bg-orange-500 bg-opacity-10 flex items-center justify-center">
                            <i class="fas fa-sliders-h text-orange-500"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-dark-text-primary">Status & Prioritas</h3>
                            <p class="text-xs text-dark-text-tertiary">Pengaturan task</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" 
                                    id="status" 
                                    required
                                    class="input-apple w-full @error('status') ring-2 ring-red-500 @enderror">
                                <option value="todo" {{ old('status', 'todo') == 'todo' ? 'selected' : '' }}>
                                    📝 To Do
                                </option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>
                                    ⚙️ In Progress
                                </option>
                                <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>
                                    ✅ Done
                                </option>
                                <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>
                                    🚫 Blocked
                                </option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                Prioritas <span class="text-red-500">*</span>
                            </label>
                            <select name="priority" 
                                    id="priority" 
                                    required
                                    class="input-apple w-full @error('priority') ring-2 ring-red-500 @enderror">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                    🟢 Low
                                </option>
                                <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>
                                    🟡 Normal
                                </option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                    🟠 High
                                </option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                    🔴 Urgent
                                </option>
                            </select>
                            @error('priority')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-dark-text-secondary mb-2">
                                Urutan
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="sort_order"
                                   value="{{ old('sort_order', 0) }}"
                                   min="0"
                                   placeholder="0 (otomatis di akhir)"
                                   class="input-apple w-full @error('sort_order') ring-2 ring-red-500 @enderror">
                            @error('sort_order')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @else
                                <p class="text-xs text-dark-text-tertiary mt-1.5">Kosongkan untuk posisi terakhir</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card-apple p-6 bg-blue-500 bg-opacity-5">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-500 text-xl mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-400 mb-2">Tips Membuat Task</h4>
                            <ul class="text-xs text-blue-300 space-y-2">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                                    <span>Gunakan judul yang spesifik dan actionable</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                                    <span>Pecah task besar menjadi sub-task kecil</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                                    <span>Set prioritas berdasarkan deadline dan impact</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                                    <span>Tambahkan SOP untuk task yang berulang</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions - Fixed Bottom -->
        <div class="sticky bottom-0 mt-6 py-4 bg-dark-bg-primary border-t border-dark-separator">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('tasks.index') }}" 
                   class="btn-apple-secondary inline-flex items-center px-6 py-2.5 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                
                <div class="flex items-center gap-3">
                    <button type="reset" 
                            class="btn-apple-secondary inline-flex items-center px-6 py-2.5 rounded-apple text-sm font-medium transition-apple">
                        <i class="fas fa-undo mr-2"></i>
                        Reset Form
                    </button>
                    
                    <button type="submit" 
                            id="submitBtn"
                            class="btn-apple-blue inline-flex items-center px-6 py-2.5 rounded-apple text-sm font-medium transition-apple">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitText">Simpan Tugas</span>
                        <i class="fas fa-spinner fa-spin ml-2 hidden" id="submitSpinner"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Scripts -->
<script>
// Character counters
const titleInput = document.getElementById('title');
const titleCount = document.getElementById('titleCount');
const descInput = document.getElementById('description');
const descCount = document.getElementById('descCount');

titleInput.addEventListener('input', function() {
    titleCount.textContent = this.value.length;
});

descInput.addEventListener('input', function() {
    descCount.textContent = this.value.length;
});

// Project info preview
const projectSelect = document.getElementById('project_id');
const projectInfo = document.getElementById('projectInfo');
const clientName = document.getElementById('clientName');
const projectStatus = document.getElementById('projectStatus');

projectSelect.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (this.value) {
        clientName.textContent = selected.dataset.client || '-';
        projectStatus.textContent = selected.dataset.status || '-';
        projectInfo.classList.remove('hidden');
    } else {
        projectInfo.classList.add('hidden');
    }
});

// Initialize if project already selected
if (projectSelect.value) {
    projectSelect.dispatchEvent(new Event('change'));
}

// Due date calculator
const dueDateInput = document.getElementById('due_date');
const daysUntilDue = document.getElementById('daysUntilDue');

dueDateInput.addEventListener('change', function() {
    if (this.value) {
        const dueDate = new Date(this.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const diffTime = dueDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 0) {
            daysUntilDue.innerHTML = '<i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>Tanggal sudah lewat!';
            daysUntilDue.classList.add('text-red-500');
        } else if (diffDays === 0) {
            daysUntilDue.innerHTML = '<i class="fas fa-clock text-yellow-500 mr-1"></i>Jatuh tempo hari ini';
            daysUntilDue.classList.add('text-yellow-500');
        } else if (diffDays <= 3) {
            daysUntilDue.innerHTML = `<i class="fas fa-clock text-orange-500 mr-1"></i>${diffDays} hari lagi (urgent!)`;
            daysUntilDue.classList.add('text-orange-500');
        } else {
            daysUntilDue.innerHTML = `<i class="fas fa-clock mr-1"></i>${diffDays} hari lagi`;
            daysUntilDue.classList.remove('text-red-500', 'text-yellow-500', 'text-orange-500');
        }
    } else {
        daysUntilDue.textContent = '';
    }
});

// Form submission with loading state
const form = document.getElementById('taskForm');
const submitBtn = document.getElementById('submitBtn');
const submitText = document.getElementById('submitText');
const submitSpinner = document.getElementById('submitSpinner');

form.addEventListener('submit', function(e) {
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    
    // Show loading state
    submitText.textContent = 'Menyimpan...';
    submitSpinner.classList.remove('hidden');
});

// Initialize character counts on page load
titleCount.textContent = titleInput.value.length;
descCount.textContent = descInput.value.length;
</script>
@endsection
