@extends('layouts.app')

@section('title', 'Edit Backlink')
@section('page-title', 'Edit Backlink')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('backlinks.list') }}" 
           style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--apple-blue); text-decoration: none; font-size: 0.875rem; font-weight: 500; margin-bottom: 1rem;"
           onmouseover="this.style.textDecoration='underline'"
           onmouseout="this.style.textDecoration='none'">
            <i class="fas fa-arrow-left"></i>
            Back to Backlinks
        </a>
        <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--dark-text-primary); margin: 0;">
            <i class="fas fa-edit" style="color: var(--apple-blue); margin-right: 0.75rem;"></i>
            Edit Backlink
        </h1>
        <p style="font-size: 0.875rem; color: var(--dark-text-secondary); margin: 0.5rem 0 0 0;">
            Update backlink information
        </p>
    </div>

    <!-- Form Card -->
    <div class="card-elevated rounded-apple-xl" style="background: var(--dark-bg-elevated); padding: 2rem;">
        <form method="POST" action="{{ route('backlinks.update', $backlink) }}">
            @csrf
            @method('PUT')

            <!-- Target Website -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                    Target Website <span style="color: var(--apple-red);">*</span>
                </label>
                <select name="backlink_target_id" required 
                        style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                        onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                        onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                    <option value="">Select target website...</option>
                    @foreach($targets as $target)
                        <option value="{{ $target->id }}" {{ old('backlink_target_id', $backlink->backlink_target_id) == $target->id ? 'selected' : '' }}>
                            {{ $target->website_name }} (DA: {{ $target->domain_authority }})
                        </option>
                    @endforeach
                </select>
                @error('backlink_target_id')
                    <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Source URL -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                    Source URL (Where the backlink is) <span style="color: var(--apple-red);">*</span>
                </label>
                <input type="url" name="source_url" required value="{{ old('source_url', $backlink->source_url) }}"
                       placeholder="URL where the backlink is located"
                       style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                       onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                       onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                @error('source_url')
                    <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Target URL -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                    Target URL (Your page being linked to) <span style="color: var(--apple-red);">*</span>
                </label>
                <input type="url" name="target_url" required value="{{ old('target_url', $backlink->target_url) }}"
                       placeholder="https://bizmark.id/your-page"
                       style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                       onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                       onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                @error('target_url')
                    <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Anchor Text -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                    Anchor Text
                </label>
                <input type="text" name="anchor_text" value="{{ old('anchor_text', $backlink->anchor_text) }}"
                       placeholder="Link text (e.g., 'Bizmark Permit Services')"
                       style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                       onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                       onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                @error('anchor_text')
                    <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Type -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                        Link Type <span style="color: var(--apple-red);">*</span>
                    </label>
                    <select name="type" required 
                            style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                            onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                            onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                        <option value="dofollow" {{ old('type', $backlink->type) == 'dofollow' ? 'selected' : '' }}>DoFollow</option>
                        <option value="nofollow" {{ old('type', $backlink->type) == 'nofollow' ? 'selected' : '' }}>NoFollow</option>
                    </select>
                    @error('type')
                        <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                        Status <span style="color: var(--apple-red);">*</span>
                    </label>
                    <select name="status" required 
                            style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                            onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                            onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                        <option value="active" {{ old('status', $backlink->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="indexed" {{ old('status', $backlink->status) == 'indexed' ? 'selected' : '' }}>Indexed</option>
                        <option value="broken" {{ old('status', $backlink->status) == 'broken' ? 'selected' : '' }}>Broken</option>
                        <option value="removed" {{ old('status', $backlink->status) == 'removed' ? 'selected' : '' }}>Removed</option>
                    </select>
                    @error('status')
                        <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Domain Authority -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                        Domain Authority
                    </label>
                    <input type="number" name="domain_authority" min="0" max="100" value="{{ old('domain_authority', $backlink->domain_authority) }}"
                           placeholder="0-100"
                           style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                           onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                           onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                    @error('domain_authority')
                        <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Acquired Date -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                        Acquired Date
                    </label>
                    <input type="date" name="acquired_at" value="{{ old('acquired_at', $backlink->acquired_at ? $backlink->acquired_at->format('Y-m-d') : '') }}"
                           style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                           onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                           onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                    @error('acquired_at')
                        <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                    Notes
                </label>
                <textarea name="notes" rows="4"
                          placeholder="Additional notes about this backlink..."
                          style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease; resize: vertical;"
                          onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                          onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">{{ old('notes', $backlink->notes) }}</textarea>
                @error('notes')
                    <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid var(--dark-separator);">
                <a href="{{ route('backlinks.list') }}" 
                   style="padding: 0.75rem 1.5rem; background: rgba(142, 142, 147, 0.2); color: var(--dark-text-primary); border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;"
                   onmouseover="this.style.background='rgba(142, 142, 147, 0.3)'"
                   onmouseout="this.style.background='rgba(142, 142, 147, 0.2)'">
                    Cancel
                </a>
                <button type="submit" 
                        style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%); color: #FFFFFF; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3); transition: all 0.3s ease;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(0, 122, 255, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0, 122, 255, 0.3)'">
                    <i class="fas fa-save" style="margin-right: 0.5rem;"></i>
                    Update Backlink
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
