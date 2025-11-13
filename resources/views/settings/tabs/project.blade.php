{{-- Project Settings Tab --}}
<div>
    <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Project Settings</h3>

    <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h4 class="text-base font-medium" style="color: #FFFFFF;">
                    <i class="fas fa-tasks mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                    Project Statuses
                </h4>
                <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.6);">
                    Kelola status proyek yang digunakan untuk memonitor kemajuan dan pipeline pekerjaan.
                </p>
            </div>
            <span class="px-2 py-1 text-xs rounded-apple" style="background: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 0.9);">
                {{ $statuses->count() }} statuses
            </span>
        </div>

        <form method="POST" action="{{ route('settings.project.statuses.store') }}" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
            @csrf
            <input type="text" name="name" class="input-apple" placeholder="Status name" value="{{ old('name') }}" required>
            <input type="text" name="code" class="input-apple" placeholder="Code (ex: in_progress)" value="{{ old('code') }}" required>
            <input type="text" name="description" class="input-apple md:col-span-2" placeholder="Description" value="{{ old('description') }}">
            <input type="color" name="color" class="input-apple h-10" value="{{ old('color', '#0A84FF') }}">
            <div class="md:col-span-5 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <input type="number" name="sort_order" class="input-apple w-24" placeholder="Order" value="{{ old('sort_order', $statuses->count() + 1) }}">
                    <label class="flex items-center space-x-1">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Active</span>
                    </label>
                    <label class="flex items-center space-x-1">
                        <input type="checkbox" name="is_final" value="1" {{ old('is_final') ? 'checked' : '' }}>
                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Final status</span>
                    </label>
                </div>
                <button type="submit" class="px-3 py-2 rounded-apple text-xs font-medium" style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                    <i class="fas fa-plus mr-1"></i>Add Status
                </button>
            </div>
            @if($errors->has('name') || $errors->has('code'))
                <div class="md:col-span-5 text-xs" style="color: rgba(255, 69, 58, 0.9);">
                    @error('name')<div>{{ $message }}</div>@enderror
                    @error('code')<div>{{ $message }}</div>@enderror
                </div>
            @endif
        </form>

        <div class="divide-y divide-gray-800/40">
            @forelse($statuses as $status)
                <div class="py-3">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full" style="background: {{ $status->color ?? '#0A84FF' }}1f; color: {{ $status->color ?? '#0A84FF' }};">
                                <i class="fas fa-flag"></i>
                            </span>
                            <div>
                                <p class="text-sm font-medium" style="color: #FFFFFF;">{{ $status->name }}</p>
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">{{ $status->code }} &mdash; {{ $status->description ?? 'No description' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 mt-2 md:mt-0">
                            @if($status->is_final)
                                <span class="px-2 py-0.5 text-xs rounded-apple" style="background: rgba(255, 159, 10, 0.15); color: rgba(255, 159, 10, 1);">Final</span>
                            @endif
                            <span class="px-2 py-0.5 text-xs rounded-apple" style="background: {{ $status->is_active ? 'rgba(0, 122, 255, 0.15)' : 'rgba(142, 142, 147, 0.2)' }}; color: {{ $status->is_active ? 'rgba(0, 122, 255, 1)' : 'rgba(142, 142, 147, 0.9)' }};">
                                {{ $status->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <form method="POST" action="{{ route('settings.project.statuses.delete', $status) }}" onsubmit="return confirm('Hapus status {{ $status->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 rounded-apple text-xs" style="background: rgba(255, 59, 48, 0.15); color: rgba(255, 59, 48, 1);" {{ $status->projects()->exists() ? 'disabled' : '' }}>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <details class="mt-3">
                        <summary class="text-xs cursor-pointer" style="color: rgba(235, 235, 245, 0.6);">Edit details</summary>
                        <form method="POST" action="{{ route('settings.project.statuses.update', $status) }}" class="mt-3 grid grid-cols-1 md:grid-cols-5 gap-3">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" class="input-apple" value="{{ old('name', $status->name) }}" required>
                            <input type="text" name="code" class="input-apple" value="{{ old('code', $status->code) }}" required>
                            <input type="text" name="description" class="input-apple md:col-span-2" value="{{ old('description', $status->description) }}">
                            <input type="color" name="color" class="input-apple h-10" value="{{ old('color', $status->color ?? '#0A84FF') }}">
                            <div class="md:col-span-5 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <input type="number" name="sort_order" class="input-apple w-24" value="{{ old('sort_order', $status->sort_order) }}">
                                    <label class="flex items-center space-x-1">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $status->is_active) ? 'checked' : '' }}>
                                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Active</span>
                                    </label>
                                    <label class="flex items-center space-x-1">
                                        <input type="checkbox" name="is_final" value="1" {{ old('is_final', $status->is_final) ? 'checked' : '' }}>
                                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Final status</span>
                                    </label>
                                </div>
                                <button type="submit" class="px-3 py-2 rounded-apple text-xs font-medium" style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                                    <i class="fas fa-save mr-1"></i>Update
                                </button>
                            </div>
                        </form>
                    </details>
                </div>
            @empty
                <div class="text-center py-6" style="color: rgba(235, 235, 245, 0.6);">
                    Belum ada status proyek.
                </div>
            @endforelse
        </div>
    </div>
</div>
