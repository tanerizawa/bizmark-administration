@extends('layouts.app')

@section('title', 'Add New Target Website')

@section('content')
<div class="container-custom max-w-4xl">
    {{-- Hero Header --}}
    <section class="card-apple p-5 md:p-6 relative overflow-hidden mb-6">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>

        <div class="relative space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Backlink Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        <i class="fas fa-plus-circle mr-2"></i>Add New Target Website
                    </h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.75);">
                        Add a potential backlink source to your targets list
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.backlinks.targets') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-apple text-sm font-medium transition-apple">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Targets
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    {{-- Form Card --}}
    <div class="card-apple p-6">
        <form method="POST" action="{{ route('admin.backlinks.targets.store') }}">
            @csrf

            <!-- Website Name -->
            <div class="mb-6">
                <label class="label-apple">
                    Website Name <span class="text-apple-red">*</span>
                </label>
                <input type="text" name="website_name" required value="{{ old('website_name') }}"
                       placeholder="e.g., TechCrunch, Forbes, Medium"
                       class="input-apple"
                       autofocus>
                @error('website_name')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                    Enter the name of the target website
                </p>
            </div>

            <!-- Website URL -->
            <div class="mb-6">
                <label class="label-apple">
                    Website URL <span class="text-apple-red">*</span>
                </label>
                <input type="url" name="website_url" required value="{{ old('website_url') }}"
                       placeholder="https://example.com"
                       class="input-apple">
                @error('website_url')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                    Full URL including https://
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Contact Email -->
                <div>
                    <label class="label-apple">
                        Contact Email
                    </label>
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                           placeholder="editor@example.com"
                           class="input-apple">
                    @error('contact_email')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                        Email for outreach
                    </p>
                </div>

                <!-- Contact Name -->
                <div>
                    <label class="label-apple">
                        Contact Name
                    </label>
                    <input type="text" name="contact_name" value="{{ old('contact_name') }}"
                           placeholder="Editor name"
                           class="input-apple">
                    @error('contact_name')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                        Contact person name
                    </p>
                </div>
            </div>

            <!-- Category -->
            <div class="mb-6">
                <label class="label-apple">
                    Category <span class="text-apple-red">*</span>
                </label>
                <input type="text" name="category" required value="{{ old('category') }}"
                       placeholder="e.g., Technology, Business, Marketing"
                       class="input-apple">
                @error('category')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                    Main content category of the website
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Domain Authority -->
                <div>
                    <label class="label-apple">
                        Domain Authority (0-100)
                    </label>
                    <input type="number" name="domain_authority" min="0" max="100" value="{{ old('domain_authority') }}"
                           placeholder="75"
                           class="input-apple">
                    @error('domain_authority')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                        Moz DA score (optional)
                    </p>
                </div>

                <!-- Type -->
                <div>
                    <label class="label-apple">
                        Type <span class="text-apple-red">*</span>
                    </label>
                    <select name="type" required class="input-apple">
                        <option value="">Select Type...</option>
                        <option value="guest_post" {{ old('type') == 'guest_post' ? 'selected' : '' }}>Guest Post</option>
                        <option value="resource_link" {{ old('type') == 'resource_link' ? 'selected' : '' }}>Resource Link</option>
                        <option value="partnership" {{ old('type') == 'partnership' ? 'selected' : '' }}>Partnership</option>
                        <option value="directory" {{ old('type') == 'directory' ? 'selected' : '' }}>Directory</option>
                        <option value="syndication" {{ old('type') == 'syndication' ? 'selected' : '' }}>Syndication</option>
                    </select>
                    @error('type')
                        <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Priority -->
            <div class="mb-6">
                <label class="label-apple">
                    Priority <span class="text-apple-red">*</span>
                </label>
                <select name="priority" required class="input-apple">
                    <option value="">Select Priority...</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Contact ASAP</option>
                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium - Regular outreach</option>
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - When available</option>
                </select>
                @error('priority')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                    High priority targets will be contacted first
                </p>
            </div>

            <!-- Notes -->
            <div class="mb-8">
                <label class="label-apple">
                    Notes
                </label>
                <textarea name="notes" rows="4"
                          placeholder="Additional information about this target (content guidelines, requirements, etc.)"
                          class="input-apple resize-y">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-apple-red text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="mb-6 p-4 rounded-apple" style="background: rgba(10,132,255,0.12); border: 1px solid rgba(10,132,255,0.2);">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 mt-0.5" style="color: rgba(10,132,255,1);" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium" style="color: rgba(10,132,255,1);">After adding this target</p>
                        <p class="text-xs mt-1" style="color: rgba(235,235,245,0.75);">
                            Status will be "pending" until you send an outreach email using:<br>
                            <code class="bg-gray-900 px-2 py-1 rounded mt-2 inline-block">php artisan backlink:outreach --ai --limit=5</code>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 justify-end pt-6 border-t border-dark-separator">
                <a href="{{ route('admin.backlinks.targets') }}" class="btn-secondary-apple">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="btn-primary-apple">
                    <i class="fas fa-plus mr-2"></i>Add Target
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
