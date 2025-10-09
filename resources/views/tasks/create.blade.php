@extends('layouts.app')

@section('title', 'Tambah Tugas Baru')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('tasks.index') }}" 
               class="text-apple-blue-dark hover:text-apple-blue">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold" style="color: #FFFFFF;">Tambah Tugas Baru</h1>
        </div>
        <p style="color: rgba(235, 235, 245, 0.6);">Buat tugas baru untuk proyek perizinan</p>
    </div>

    <!-- Form -->
    <div class="card-elevated rounded-apple-lg">
        <form action="{{ route('tasks.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="pb-6" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                <h3 class="text-lg font-medium mb-4" style="color: #FFFFFF;">Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Project -->
                    <div class="md:col-span-2">
                        <label for="project_id" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Proyek <span class="text-apple-red-dark">*</span>
                        </label>
                        <select name="project_id" 
                                id="project_id" 
                                required
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

                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Judul Tugas <span class="text-apple-red-dark">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title"
                               value="{{ old('title') }}"
                               required
                               placeholder="Masukkan judul tugas..."
                               class="input-dark w-full px-3 py-2 rounded-md @error('title') ring-2 ring-apple-red @enderror">
                        @error('title')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Deskripsi</label>
                        <textarea name="description" 
                                  id="description"
                                  rows="4"
                                  placeholder="Deskripsi detail tentang tugas..."
                                  class="input-dark w-full px-3 py-2 rounded-md @error('description') ring-2 ring-apple-red @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SOP Notes -->
                    <div class="md:col-span-2">
                        <label for="sop_notes" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">SOP / Checklist</label>
                        <textarea name="sop_notes" 
                                  id="sop_notes"
                                  rows="3"
                                  placeholder="Standard Operating Procedure atau checklist untuk tugas ini..."
                                  class="input-dark w-full px-3 py-2 rounded-md @error('sop_notes') ring-2 ring-apple-red @enderror">{{ old('sop_notes') }}</textarea>
                        @error('sop_notes')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Assignment & Timeline -->
            <div class="pb-6" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                <h3 class="text-lg font-medium mb-4" style="color: #FFFFFF;">Penugasan & Timeline</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Assigned User -->
                    <div>
                        <label for="assigned_user_id" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Ditugaskan Kepada</label>
                        <select name="assigned_user_id" 
                                id="assigned_user_id"
                                class="input-dark w-full px-3 py-2 rounded-md @error('assigned_user_id') ring-2 ring-apple-red @enderror">
                            <option value="">Belum ditugaskan</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_user_id')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Tanggal Jatuh Tempo</label>
                        <input type="date" 
                               name="due_date" 
                               id="due_date"
                               value="{{ old('due_date') }}"
                               min="{{ date('Y-m-d') }}"
                               class="input-dark w-full px-3 py-2 rounded-md @error('due_date') ring-2 ring-apple-red @enderror">
                        @error('due_date')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estimated Hours -->
                    <div>
                        <label for="estimated_hours" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Estimasi Jam Kerja</label>
                        <input type="number" 
                               name="estimated_hours" 
                               id="estimated_hours"
                               value="{{ old('estimated_hours') }}"
                               min="1"
                               placeholder="Jam"
                               class="input-dark w-full px-3 py-2 rounded-md @error('estimated_hours') ring-2 ring-apple-red @enderror">
                        @error('estimated_hours')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Institution -->
                    <div>
                        <label for="institution_id" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Institusi Terkait</label>
                        <select name="institution_id" 
                                id="institution_id"
                                class="input-dark w-full px-3 py-2 rounded-md @error('institution_id') ring-2 ring-apple-red @enderror">
                            <option value="">Pilih Institusi</option>
                            @foreach($institutions as $institution)
                                <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                    {{ $institution->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('institution_id')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status & Priority -->
            <div>
                <h3 class="text-lg font-medium mb-4" style="color: #FFFFFF;">Status & Prioritas</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Status <span class="text-apple-red-dark">*</span>
                        </label>
                        <select name="status" 
                                id="status" 
                                required
                                class="input-dark w-full px-3 py-2 rounded-md @error('status') ring-2 ring-apple-red @enderror">
                            <option value="todo" {{ old('status', 'todo') == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
                            <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                        </select>
                        @error('status')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Prioritas <span class="text-apple-red-dark">*</span>
                        </label>
                        <select name="priority" 
                                id="priority" 
                                required
                                class="input-dark w-full px-3 py-2 rounded-md @error('priority') ring-2 ring-apple-red @enderror">
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Urutan</label>
                        <input type="number" 
                               name="sort_order" 
                               id="sort_order"
                               value="{{ old('sort_order', 0) }}"
                               min="0"
                               placeholder="0"
                               class="input-dark w-full px-3 py-2 rounded-md @error('sort_order') ring-2 ring-apple-red @enderror">
                        @error('sort_order')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6" style="border-top: 1px solid rgba(84, 84, 88, 0.65);">
                <a href="{{ route('tasks.index') }}" 
                   class="px-6 py-2 rounded-md font-medium transition-colors" style="border: 1px solid rgba(84, 84, 88, 0.65); color: rgba(235, 235, 245, 0.8); background: rgba(58, 58, 60, 0.6);">
                    Batal
                </a>
                <button type="submit" 
                        class="btn-primary px-6 py-2 rounded-md font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Tugas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection