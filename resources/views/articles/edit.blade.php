@extends('layouts.app')

@section('title', 'Edit Artikel')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-dark-text-primary">Edit Artikel</h1>
            <p class="text-sm text-dark-text-secondary mt-1">Edit artikel: {{ $article->title }}</p>
        </div>
        <a href="{{ route('articles.index') }}" class="px-4 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm font-medium hover:bg-dark-bg-secondary transition-apple">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('articles.update', $article) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title -->
                <div class="bg-dark-bg-secondary rounded-apple p-6">
                    <label for="title" class="block text-sm font-medium text-dark-text-primary mb-2">Judul Artikel *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" required class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue @error('title') border-apple-red @enderror">
                    @error('title')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Excerpt -->
                <div class="bg-dark-bg-secondary rounded-apple p-6">
                    <label for="excerpt" class="block text-sm font-medium text-dark-text-primary mb-2">Excerpt</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue @error('excerpt') border-apple-red @enderror">{{ old('excerpt', $article->excerpt) }}</textarea>
                    <p class="mt-1 text-xs text-dark-text-tertiary">Ringkasan singkat artikel (opsional, akan di-generate otomatis jika kosong)</p>
                    @error('excerpt')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div class="bg-dark-bg-secondary rounded-apple p-6">
                    <label for="content" class="block text-sm font-medium text-dark-text-primary mb-2">Konten Artikel *</label>
                    <textarea name="content" id="content" class="w-full @error('content') border-apple-red @enderror">{{ old('content', $article->content) }}</textarea>
                    @error('content')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SEO Section -->
                <div class="bg-dark-bg-secondary rounded-apple p-6">
                    <h3 class="text-lg font-semibold text-dark-text-primary mb-4">SEO Settings</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-dark-text-primary mb-2">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $article->meta_title) }}" maxlength="60" class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue">
                            <p class="mt-1 text-xs text-dark-text-tertiary">Rekomendasi: 50-60 karakter</p>
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-dark-text-primary mb-2">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="2" maxlength="160" class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue">{{ old('meta_description', $article->meta_description) }}</textarea>
                            <p class="mt-1 text-xs text-dark-text-tertiary">Rekomendasi: 150-160 karakter</p>
                        </div>

                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-dark-text-primary mb-2">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $article->meta_keywords) }}" class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue">
                            <p class="mt-1 text-xs text-dark-text-tertiary">Pisahkan dengan koma (contoh: lb3, amdal, lingkungan)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Settings -->
                <div class="bg-dark-bg-secondary rounded-apple p-6">
                    <h3 class="text-lg font-semibold text-dark-text-primary mb-4">Pengaturan Publikasi</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-dark-text-primary mb-2">Status *</label>
                            <select name="status" id="status" required class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue">
                                <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $article->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>

                        <div>
                            <label for="published_at" class="block text-sm font-medium text-dark-text-primary mb-2">Tanggal Publikasi</label>
                            <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue">
                            <p class="mt-1 text-xs text-dark-text-tertiary">Kosongkan untuk publikasi sekarang</p>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article->is_featured) ? 'checked' : '' }} class="rounded border-dark-separator text-apple-blue focus:ring-apple-blue">
                                <span class="ml-2 text-sm text-dark-text-primary">Jadikan artikel unggulan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="bg-dark-bg-secondary rounded-apple p-6">
                    <h3 class="text-lg font-semibold text-dark-text-primary mb-4">Featured Image</h3>
                    
                    @if($article->featured_image)
                    <div id="current-image" class="mb-4">
                        <img src="{{ Storage::url($article->featured_image) }}" alt="Current image" class="w-full rounded-apple mb-2">
                        <p class="text-xs text-dark-text-tertiary">Gambar saat ini</p>
                    </div>
                    @endif

                    <div id="image-preview" class="hidden mb-4">
                        <img src="" alt="Preview" class="w-full rounded-apple">
                        <button type="button" id="remove-image" class="mt-2 text-sm text-apple-red hover:text-apple-red/80">
                            <i class="fas fa-times mr-1"></i>Hapus gambar baru
                        </button>
                    </div>
                    
                    <input type="file" name="featured_image" id="featured_image" accept="image/*" class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue @error('featured_image') border-apple-red @enderror">
                    <p class="mt-2 text-xs text-dark-text-tertiary">Format: JPG, PNG, GIF (max 2MB)</p>
                    @error('featured_image')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category & Tags -->
                <div class="bg-dark-bg-secondary rounded-apple p-6">
                    <h3 class="text-lg font-semibold text-dark-text-primary mb-4">Kategori & Tag</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="category" class="block text-sm font-medium text-dark-text-primary mb-2">Kategori *</label>
                            <select name="category" id="category" required class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue">
                                @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $article->category) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tags-input" class="block text-sm font-medium text-dark-text-primary mb-2">Tags</label>
                            <input type="text" id="tags-input" placeholder="Ketik tag dan tekan Enter" class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue">
                            <div id="tags-container" class="mt-2 flex flex-wrap gap-2"></div>
                            <p class="mt-2 text-xs text-dark-text-tertiary">Tekan Enter untuk menambah tag</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-dark-bg-secondary rounded-apple p-6">
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                            <i class="fas fa-save mr-2"></i>Update Artikel
                        </button>
                        <a href="{{ route('articles.index') }}" class="w-full px-4 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm font-medium hover:bg-dark-bg-tertiary/80 transition-apple flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    // Initialize TinyMCE
    tinymce.init({
        selector: '#content',
        height: 500,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image link | code | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        skin: 'oxide-dark',
        content_css: 'dark',
        images_upload_handler: function (blobInfo, success, failure) {
            const formData = new FormData();
            formData.append('image', blobInfo.blob(), blobInfo.filename());
            
            fetch('{{ route("articles.upload-image") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    success(result.url);
                } else {
                    failure('Upload failed');
                }
            })
            .catch(() => {
                failure('Upload failed');
            });
        }
    });

    // Featured Image Preview
    document.getElementById('featured_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
                document.getElementById('current-image')?.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('remove-image')?.addEventListener('click', function() {
        document.getElementById('featured_image').value = '';
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('current-image')?.classList.remove('hidden');
    });

    // Tags Management
    let tags = @json(old('tags', $article->tags ?? []));
    const tagsInput = document.getElementById('tags-input');
    const tagsContainer = document.getElementById('tags-container');

    // Initial render
    renderTags();

    tagsInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tag = this.value.trim();
            if (tag && !tags.includes(tag)) {
                tags.push(tag);
                renderTags();
                this.value = '';
            }
        }
    });

    function renderTags() {
        tagsContainer.innerHTML = '';
        tags.forEach((tag, index) => {
            const tagEl = document.createElement('span');
            tagEl.className = 'inline-flex items-center px-2 py-1 bg-apple-blue/20 text-apple-blue rounded text-xs';
            tagEl.innerHTML = `
                ${tag}
                <button type="button" onclick="removeTag(${index})" class="ml-1 text-apple-blue hover:text-apple-blue-dark">
                    <i class="fas fa-times"></i>
                </button>
                <input type="hidden" name="tags[]" value="${tag}">
            `;
            tagsContainer.appendChild(tagEl);
        });
    }

    function removeTag(index) {
        tags.splice(index, 1);
        renderTags();
    }
</script>
@endsection
