@extends('layouts.app')

@section('title', 'Edit Target Website')
@section('page-title', 'Edit Target Website')

@section('content')
<div class="container-custom max-w-4xl">
    <div class="page-header-apple">
        <div>
            <h1 class="page-title-apple">
                <i class="fas fa-edit mr-3"></i>Edit Target Website
            </h1>
            <p class="page-subtitle-apple">Update target website information</p>
        </div>
        <a href="{{ route('admin.backlinks.targets') }}" class="btn-secondary-apple">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    
    <div class="card-apple p-6">
        <form method="POST" action="{{ route('admin.backlinks.targets.update', $target) }}">
            @csrf
            @method('PUT')

            <!-- Website Name -->
            <div class="mb-6">
                <label class="label-apple">
                    Website Name <span class="text-apple-red">*</span>
                </label>
                <input type="text" name="website_name" required value="{{ old('website_name', $target->website_name) }}"
                       placeholder="e.g., TechCrunch, Forbes, Medium"
                       class="input-apple"
                       
                       >
                @error('website_name')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Website URL -->
            <div class="mb-6">
                <label class="label-apple">
                    Website URL <span class="text-apple-red">*</span>
                </label>
                <input type="url" name="website_url" required value="{{ old('website_url', $target->website_url) }}"
                       placeholder="https://"
                       class="input-apple"
                       
                       >
                @error('website_url')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <!-- Contact Email -->
                <div>
                    <label class="label-apple">
                        Contact Email
                    </label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $target->contact_email) }}"
                           placeholder="Editor email address"
                           class="input-apple"
                           
                           >
                    @error('contact_email')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Name -->
                <div>
                    <label class="label-apple">
                        Contact Name
                    </label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', $target->contact_name) }}"
                           placeholder="Editor or contact person name"
                           class="input-apple"
                           
                           >
                    @error('contact_name')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Category -->
            <div class="mb-6">
                <label class="label-apple">
                    Category <span class="text-apple-red">*</span>
                </label>
                <input type="text" name="category" required value="{{ old('category', $target->category) }}"
                       placeholder="e.g., Technology, Business, Marketing"
                       class="input-apple"
                       
                       >
                @error('category')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <!-- Domain Authority -->
                <div>
                    <label class="label-apple">
                        Domain Authority (0-100)
                    </label>
                    <input type="number" name="domain_authority" min="0" max="100" value="{{ old('domain_authority', $target->domain_authority) }}"
                           placeholder="75"
                           class="input-apple"
                           
                           >
                    @error('domain_authority')
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
                        <option value="pending" {{ old('status', $target->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="contacted" {{ old('status', $target->status) == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="responded" {{ old('status', $target->status) == 'responded' ? 'selected' : '' }}>Responded</option>
                        <option value="accepted" {{ old('status', $target->status) == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ old('status', $target->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="acquired" {{ old('status', $target->status) == 'acquired' ? 'selected' : '' }}>Acquired</option>
                    </select>
                    @error('status')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <!-- Type -->
                <div>
                    <label class="label-apple">
                        Type <span class="text-apple-red">*</span>
                    </label>
                    <select name="type" required 
                            class="input-apple"
                            
                            >
                        <option value="guest_post" {{ old('type', $target->type) == 'guest_post' ? 'selected' : '' }}>Guest Post</option>
                        <option value="resource_link" {{ old('type', $target->type) == 'resource_link' ? 'selected' : '' }}>Resource Link</option>
                        <option value="partnership" {{ old('type', $target->type) == 'partnership' ? 'selected' : '' }}>Partnership</option>
                        <option value="directory" {{ old('type', $target->type) == 'directory' ? 'selected' : '' }}>Directory</option>
                        <option value="syndication" {{ old('type', $target->type) == 'syndication' ? 'selected' : '' }}>Syndication</option>
                    </select>
                    @error('type')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label class="label-apple">
                        Priority <span class="text-apple-red">*</span>
                    </label>
                    <select name="priority" required 
                            class="input-apple"
                            
                            >
                        <option value="high" {{ old('priority', $target->priority) == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ old('priority', $target->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ old('priority', $target->priority) == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                    @error('priority')
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
                          placeholder="Additional information about this target..."
                          class="input-apple resize-y"
                          
                          >{{ old('notes', $target->notes) }}</textarea>
                @error('notes')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 justify-end pt-6 border-t border-dark-separator">
                <a href="{{ route('admin.backlinks.targets') }}" class="btn-secondary-apple">
                    Cancel
                </a>
                <button type="submit" class="btn-primary-apple">
                    <i class="fas fa-save mr-2"></i>
                    Update Target
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
