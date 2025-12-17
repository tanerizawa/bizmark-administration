@extends('layouts.app')

@section('title', 'Edit Backlink')
@section('page-title', 'Edit Backlink')

@section('content')
<div class="container-custom max-w-4xl">
    <div class="page-header-apple">
        <div>
            <h1 class="page-title-apple">
                <i class="fas fa-edit mr-3"></i>Edit Backlink
            </h1>
            <p class="page-subtitle-apple">Update backlink information</p>
        </div>
        <a href="{{ route('admin.backlinks.list') }}" class="btn-secondary-apple">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    
    <div class="card-apple p-6">
        <form method="POST" action="{{ route('admin.backlinks.update', $backlink) }}">
            @csrf
            @method('PUT')

            <!-- Target Website -->
            <div class="mb-6">
                <label class="label-apple">
                    Target Website <span class="text-apple-red">*</span>
                </label>
                <select name="backlink_target_id" required 
                        class="input-apple"
                        
                        >
                    <option value="">Select target website...</option>
                    @foreach($targets as $target)
                        <option value="{{ $target->id }}" {{ old('backlink_target_id', $backlink->backlink_target_id) == $target->id ? 'selected' : '' }}>
                            {{ $target->website_name }} (DA: {{ $target->domain_authority }})
                        </option>
                    @endforeach
                </select>
                @error('backlink_target_id')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Source URL -->
            <div class="mb-6">
                <label class="label-apple">
                    Source URL (Where the backlink is) <span class="text-apple-red">*</span>
                </label>
                <input type="url" name="source_url" required value="{{ old('source_url', $backlink->source_url) }}"
                       placeholder="URL where the backlink is located"
                       class="input-apple"
                       
                       >
                @error('source_url')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Target URL -->
            <div class="mb-6">
                <label class="label-apple">
                    Target URL (Your page being linked to) <span class="text-apple-red">*</span>
                </label>
                <input type="url" name="target_url" required value="{{ old('target_url', $backlink->target_url) }}"
                       placeholder="https://bizmark.id/your-page"
                       class="input-apple"
                       
                       >
                @error('target_url')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Anchor Text -->
            <div class="mb-6">
                <label class="label-apple">
                    Anchor Text
                </label>
                <input type="text" name="anchor_text" value="{{ old('anchor_text', $backlink->anchor_text) }}"
                       placeholder="Link text (e.g., 'Bizmark Permit Services')"
                       class="input-apple"
                       
                       >
                @error('anchor_text')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <!-- Type -->
                <div>
                    <label class="label-apple">
                        Link Type <span class="text-apple-red">*</span>
                    </label>
                    <select name="type" required 
                            class="input-apple"
                            
                            >
                        <option value="dofollow" {{ old('type', $backlink->type) == 'dofollow' ? 'selected' : '' }}>DoFollow</option>
                        <option value="nofollow" {{ old('type', $backlink->type) == 'nofollow' ? 'selected' : '' }}>NoFollow</option>
                    </select>
                    @error('type')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="label-apple">
                        Status <span class="text-apple-red">*</span>
                    </label>
                    <select name="status" required 
                            class="input-apple"
                            
                            >
                        <option value="active" {{ old('status', $backlink->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="indexed" {{ old('status', $backlink->status) == 'indexed' ? 'selected' : '' }}>Indexed</option>
                        <option value="broken" {{ old('status', $backlink->status) == 'broken' ? 'selected' : '' }}>Broken</option>
                        <option value="removed" {{ old('status', $backlink->status) == 'removed' ? 'selected' : '' }}>Removed</option>
                    </select>
                    @error('status')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <!-- Domain Authority -->
                <div>
                    <label class="label-apple">
                        Domain Authority
                    </label>
                    <input type="number" name="domain_authority" min="0" max="100" value="{{ old('domain_authority', $backlink->domain_authority) }}"
                           placeholder="0-100"
                           class="input-apple"
                           
                           >
                    @error('domain_authority')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Acquired Date -->
                <div>
                    <label class="label-apple">
                        Acquired Date
                    </label>
                    <input type="date" name="acquired_at" value="{{ old('acquired_at', $backlink->acquired_at ? $backlink->acquired_at->format('Y-m-d') : '') }}"
                           class="input-apple"
                           
                           >
                    @error('acquired_at')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-8">
                <label class="label-apple">
                    Notes
                </label>
                <textarea name="notes" rows="4"
                          placeholder="Additional notes about this backlink..."
                          class="input-apple"
                          
                          >{{ old('notes', $backlink->notes) }}</textarea>
                @error('notes')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 justify-end pt-6 border-t border-dark-separator">
                <a href="{{ route('admin.backlinks.list') }}" class="btn-secondary-apple">
                    Cancel
                </a>
                <button type="submit" class="btn-primary-apple">
                    <i class="fas fa-save mr-2"></i>
                    Update Backlink
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
