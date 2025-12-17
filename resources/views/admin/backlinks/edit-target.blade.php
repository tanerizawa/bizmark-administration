@extends('layouts.app')

@section('title', 'Edit Target Website')
@section('page-title', 'Edit Target Website')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('backlinks.targets') }}" 
           style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--apple-blue); text-decoration: none; font-size: 0.875rem; font-weight: 500; margin-bottom: 1rem;"
           onmouseover="this.style.textDecoration='underline'"
           onmouseout="this.style.textDecoration='none'">
            <i class="fas fa-arrow-left"></i>
            Back to Targets
        </a>
        <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--dark-text-primary); margin: 0;">
            <i class="fas fa-edit" style="color: var(--apple-blue); margin-right: 0.75rem;"></i>
            Edit Target Website
        </h1>
        <p style="font-size: 0.875rem; color: var(--dark-text-secondary); margin: 0.5rem 0 0 0;">
            Update target website information
        </p>
    </div>

    <!-- Form Card -->
    <div class="card-elevated rounded-apple-xl" style="background: var(--dark-bg-elevated); padding: 2rem;">
        <form method="POST" action="{{ route('backlinks.targets.update', $target) }}">
            @csrf
            @method('PUT')

            <!-- Website Name -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                    Website Name <span style="color: var(--apple-red);">*</span>
                </label>
                <input type="text" name="website_name" required value="{{ old('website_name', $target->website_name) }}"
                       placeholder="e.g., TechCrunch, Forbes, Medium"
                       style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                       onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                       onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                @error('website_name')
                    <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Website URL -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                    Website URL <span style="color: var(--apple-red);">*</span>
                </label>
                <input type="url" name="website_url" required value="{{ old('website_url', $target->website_url) }}"
                       placeholder="https://"
                       style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                       onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                       onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                @error('website_url')
                    <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Contact Email -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                        Contact Email
                    </label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $target->contact_email) }}"
                           placeholder="Editor email address"
                           style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                           onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                           onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                    @error('contact_email')
                        <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Name -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                        Contact Name
                    </label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', $target->contact_name) }}"
                           placeholder="Editor or contact person name"
                           style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                           onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                           onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                    @error('contact_name')
                        <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Category -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                    Category <span style="color: var(--apple-red);">*</span>
                </label>
                <input type="text" name="category" required value="{{ old('category', $target->category) }}"
                       placeholder="e.g., Technology, Business, Marketing"
                       style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                       onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                       onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                @error('category')
                    <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Domain Authority -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                        Domain Authority (0-100)
                    </label>
                    <input type="number" name="domain_authority" min="0" max="100" value="{{ old('domain_authority', $target->domain_authority) }}"
                           placeholder="75"
                           style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                           onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                           onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                    @error('domain_authority')
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
                        <option value="pending" {{ old('status', $target->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="contacted" {{ old('status', $target->status) == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="responded" {{ old('status', $target->status) == 'responded' ? 'selected' : '' }}>Responded</option>
                        <option value="accepted" {{ old('status', $target->status) == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ old('status', $target->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="acquired" {{ old('status', $target->status) == 'acquired' ? 'selected' : '' }}>Acquired</option>
                    </select>
                    @error('status')
                        <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Type -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                        Type <span style="color: var(--apple-red);">*</span>
                    </label>
                    <select name="type" required 
                            style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                            onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                            onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                        <option value="guest_post" {{ old('type', $target->type) == 'guest_post' ? 'selected' : '' }}>Guest Post</option>
                        <option value="resource_link" {{ old('type', $target->type) == 'resource_link' ? 'selected' : '' }}>Resource Link</option>
                        <option value="partnership" {{ old('type', $target->type) == 'partnership' ? 'selected' : '' }}>Partnership</option>
                        <option value="directory" {{ old('type', $target->type) == 'directory' ? 'selected' : '' }}>Directory</option>
                        <option value="syndication" {{ old('type', $target->type) == 'syndication' ? 'selected' : '' }}>Syndication</option>
                    </select>
                    @error('type')
                        <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--dark-text-primary); margin-bottom: 0.5rem;">
                        Priority <span style="color: var(--apple-red);">*</span>
                    </label>
                    <select name="priority" required 
                            style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease;"
                            onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                            onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">
                        <option value="high" {{ old('priority', $target->priority) == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ old('priority', $target->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ old('priority', $target->priority) == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                    @error('priority')
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
                          placeholder="Additional information about this target..."
                          style="width: 100%; padding: 0.875rem; background: var(--dark-bg-tertiary); border: 1px solid rgba(84, 84, 88, 0.3); border-radius: 10px; color: var(--dark-text-primary); font-size: 0.875rem; transition: all 0.2s ease; resize: vertical;"
                          onfocus="this.style.borderColor='var(--apple-blue)'; this.style.boxShadow='0 0 0 3px rgba(0, 122, 255, 0.1)'"
                          onblur="this.style.borderColor='rgba(84, 84, 88, 0.3)'; this.style.boxShadow='none'">{{ old('notes', $target->notes) }}</textarea>
                @error('notes')
                    <p style="color: var(--apple-red); font-size: 0.75rem; margin: 0.5rem 0 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid var(--dark-separator);">
                <a href="{{ route('backlinks.targets') }}" 
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
                    Update Target
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
