@extends('layouts.app')

@section('title', 'Buat Template Baru')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('permit-templates.index') }}" 
           class="inline-flex items-center text-sm mb-4 hover:text-apple-blue transition-colors" 
           style="color: rgba(235, 235, 245, 0.6);">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Template
        </a>
        <h1 class="text-3xl font-bold" style="color: #FFFFFF;">Buat Template Izin Baru</h1>
        <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">Buat template siap pakai untuk mempercepat setup proyek</p>
    </div>

    <form action="{{ route('permit-templates.store') }}" method="POST" id="template-form">
        @csrf

        <!-- Basic Info -->
        <div class="card-elevated rounded-apple-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                <i class="fas fa-info-circle mr-2"></i>Informasi Template
            </h3>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Nama Template <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="input-dark w-full px-3 py-2 rounded-md @error('name') border-red-500 @enderror"
                           placeholder="Contoh: UKL-UPL Pabrik/Industri"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Deskripsi
                    </label>
                    <textarea name="description" 
                              rows="3"
                              class="input-dark w-full px-3 py-2 rounded-md @error('description') border-red-500 @enderror"
                              placeholder="Jelaskan use case template ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Estimasi Total Hari
                        </label>
                        <input type="number" 
                               name="estimated_days" 
                               value="{{ old('estimated_days') }}"
                               min="1"
                               class="input-dark w-full px-3 py-2 rounded-md @error('estimated_days') border-red-500 @enderror"
                               placeholder="Contoh: 95">
                        @error('estimated_days')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Estimasi Total Biaya (Rp)
                        </label>
                        <input type="number" 
                               name="estimated_cost" 
                               value="{{ old('estimated_cost') }}"
                               min="0"
                               step="100000"
                               class="input-dark w-full px-3 py-2 rounded-md @error('estimated_cost') border-red-500 @enderror"
                               placeholder="Contoh: 73000000">
                        @error('estimated_cost')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Permit Items -->
        <div class="card-elevated rounded-apple-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold" style="color: #FFFFFF;">
                    <i class="fas fa-list-ol mr-2"></i>Izin dalam Template
                </h3>
                <button type="button" 
                        id="add-permit-item"
                        class="px-4 py-2 rounded-lg transition-colors" 
                        style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);">
                    <i class="fas fa-plus mr-2"></i>Tambah Izin
                </button>
            </div>

            <div id="permit-items-container" class="space-y-4">
                <!-- Items will be added here dynamically -->
            </div>

            <div id="empty-state" class="text-center py-8" style="color: rgba(235, 235, 245, 0.5);">
                <i class="fas fa-certificate text-4xl mb-3"></i>
                <p>Belum ada izin ditambahkan</p>
                <p class="text-sm mt-1">Klik "Tambah Izin" untuk mulai membuat template</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('permit-templates.index') }}" 
               class="px-6 py-3 rounded-lg transition-colors" 
               style="background: rgba(142, 142, 147, 0.3); color: rgba(235, 235, 245, 0.8);">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-3 rounded-lg transition-colors" 
                    style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
                <i class="fas fa-save mr-2"></i>Simpan Template
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('permit-items-container');
    const addButton = document.getElementById('add-permit-item');
    const emptyState = document.getElementById('empty-state');
    let itemCount = 0;

    // Available permit types from database
    const permitTypes = @json(\App\Models\PermitType::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']));

    function updateEmptyState() {
        emptyState.style.display = container.children.length === 0 ? 'block' : 'none';
    }

    function createPermitItem(sequence) {
        const item = document.createElement('div');
        item.className = 'permit-item p-4 rounded-lg border-2 border-transparent hover:border-blue-500 transition-colors';
        item.style.background = 'rgba(58, 58, 60, 0.5)';
        item.dataset.sequence = sequence;

        // Get available dependencies (all items before this one)
        const existingItems = Array.from(container.children);
        const availableDependencies = existingItems.map((el, idx) => {
            const select = el.querySelector('.permit-type-select');
            if (select && select.value) {
                const option = select.options[select.selectedIndex];
                return {
                    sequence: idx + 1,
                    id: select.value,
                    name: option.text
                };
            }
            return null;
        }).filter(Boolean);

        item.innerHTML = `
            <div class="flex items-start gap-4">
                <!-- Sequence Number -->
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg" 
                     style="background: rgba(10, 132, 255, 0.3); color: rgba(10, 132, 255, 1);">
                    ${sequence}
                </div>

                <div class="flex-1 space-y-4">
                    <!-- Permit Type Selection -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Pilih Jenis Izin <span class="text-red-500">*</span>
                        </label>
                        <select name="items[${sequence - 1}][permit_type_id]" 
                                class="permit-type-select input-dark w-full px-3 py-2 rounded-md" 
                                required>
                            <option value="">-- Pilih Izin --</option>
                            ${permitTypes.map(pt => `<option value="${pt.id}">${pt.name} (${pt.code})</option>`).join('')}
                        </select>
                    </div>

                    <!-- Is Goal Permit -->
                    <div>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="items[${sequence - 1}][is_goal_permit]" 
                                   value="1"
                                   class="w-4 h-4 rounded">
                            <span class="ml-2 text-sm" style="color: rgba(235, 235, 245, 0.8);">
                                <i class="fas fa-flag mr-1" style="color: rgba(10, 132, 255, 1);"></i>
                                Ini adalah izin tujuan (goal permit)
                            </span>
                        </label>
                    </div>

                    <!-- Dependencies -->
                    ${availableDependencies.length > 0 ? `
                    <div class="dependencies-section">
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            <i class="fas fa-link mr-1"></i>Prasyarat (Dependencies)
                        </label>
                        <div class="space-y-2">
                            ${availableDependencies.map(dep => `
                                <label class="flex items-center p-2 rounded hover:bg-opacity-50" style="background: rgba(58, 58, 60, 0.3);">
                                    <input type="checkbox" 
                                           name="items[${sequence - 1}][dependencies][]" 
                                           value="${dep.sequence}"
                                           class="w-4 h-4 rounded">
                                    <span class="ml-2 text-sm flex-1" style="color: rgba(235, 235, 245, 0.8);">
                                        ${dep.sequence}. ${dep.name}
                                    </span>
                                    <select name="items[${sequence - 1}][dependency_types][${dep.sequence}]" 
                                            class="text-xs px-2 py-1 rounded-md" 
                                            style="background: rgba(58, 58, 60, 0.8); color: rgba(235, 235, 245, 0.8);">
                                        <option value="MANDATORY">Wajib</option>
                                        <option value="OPTIONAL">Opsional</option>
                                    </select>
                                </label>
                            `).join('')}
                        </div>
                    </div>
                    ` : `
                    <div class="text-sm p-3 rounded-lg" style="background: rgba(255, 149, 0, 0.1); color: rgba(255, 149, 0, 1);">
                        <i class="fas fa-info-circle mr-2"></i>
                        Tidak ada izin sebelumnya untuk dijadikan prasyarat
                    </div>
                    `}
                </div>

                <!-- Remove Button -->
                <button type="button" 
                        class="remove-item flex-shrink-0 w-8 h-8 rounded-lg transition-colors hover:bg-red-600" 
                        style="background: rgba(255, 59, 48, 0.2); color: rgba(255, 59, 48, 1);">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Hidden sequence order -->
            <input type="hidden" name="items[${sequence - 1}][sequence_order]" value="${sequence}">
        `;

        return item;
    }

    // Add new permit item
    addButton.addEventListener('click', function() {
        itemCount++;
        const newItem = createPermitItem(itemCount);
        container.appendChild(newItem);
        updateEmptyState();
    });

    // Remove permit item
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const item = e.target.closest('.permit-item');
            item.remove();
            
            // Renumber remaining items
            itemCount = 0;
            Array.from(container.children).forEach((item, index) => {
                itemCount++;
                const sequenceNum = index + 1;
                item.dataset.sequence = sequenceNum;
                
                // Update sequence number display
                const badge = item.querySelector('.rounded-full');
                if (badge) badge.textContent = sequenceNum;
                
                // Update input names
                item.querySelectorAll('[name^="items["]').forEach(input => {
                    const name = input.getAttribute('name');
                    const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
                    input.setAttribute('name', newName);
                });
                
                // Update hidden sequence order
                const hiddenSeq = item.querySelector('input[name*="[sequence_order]"]');
                if (hiddenSeq) hiddenSeq.value = sequenceNum;
            });
            
            updateEmptyState();
        }
    });

    // Trigger change to update dependencies when permit type is selected
    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('permit-type-select')) {
            // Rebuild subsequent items to include this as dependency option
            const changedItem = e.target.closest('.permit-item');
            const changedSequence = parseInt(changedItem.dataset.sequence);
            
            // Rebuild items after this one
            const allItems = Array.from(container.children);
            allItems.forEach((item, idx) => {
                if (idx >= changedSequence) {
                    const newItem = createPermitItem(idx + 1);
                    const oldSelect = item.querySelector('.permit-type-select');
                    const oldCheckbox = item.querySelector('input[name*="[is_goal_permit]"]');
                    
                    container.replaceChild(newItem, item);
                    
                    // Restore selections
                    if (oldSelect && oldSelect.value) {
                        const newSelect = newItem.querySelector('.permit-type-select');
                        newSelect.value = oldSelect.value;
                    }
                    if (oldCheckbox && oldCheckbox.checked) {
                        const newCheckbox = newItem.querySelector('input[name*="[is_goal_permit]"]');
                        if (newCheckbox) newCheckbox.checked = true;
                    }
                }
            });
        }
    });

    updateEmptyState();
});
</script>
@endpush
@endsection
