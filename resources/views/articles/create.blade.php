@extends('layouts.app')

@section('title', 'Buat Artikel Baru')

@section('content')
<div class="container-custom">
    <!-- Page Header -->
    <div class="page-header-apple">
        <div>
            <h1 class="page-title-apple">
                <i class="fas fa-plus-circle mr-3"></i>Buat Artikel Baru
            </h1>
            <p class="page-subtitle-apple">Buat artikel baru untuk landing page</p>
        </div>
        <a href="{{ route('articles.index') }}" class="btn-secondary-apple">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title -->
                <div class="card-apple p-6">
                    <label for="title" class="label-apple">Judul Artikel *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="input-apple @error('title') border-apple-red @enderror">
                    @error('title')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Excerpt -->
                <div class="card-apple p-6">
                    <label for="excerpt" class="label-apple">Excerpt</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="input-apple @error('excerpt') border-apple-red @enderror">{{ old('excerpt') }}</textarea>
                    <p class="mt-1 text-xs text-dark-text-tertiary">Ringkasan singkat artikel (opsional, akan di-generate otomatis jika kosong)</p>
                    @error('excerpt')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div class="card-apple p-6">
                    <label for="content" class="label-apple">Konten Artikel *</label>
                    <div class="ckeditor-wrapper">
                        <textarea name="content" id="content" class="w-full @error('content') border-apple-red @enderror">{{ old('content') }}</textarea>
                    </div>
                    @error('content')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SEO Section -->
                <div class="card-apple p-6">
                    <h3 class="text-lg font-semibold text-dark-text-primary mb-4">
                        <i class="fas fa-search mr-2 text-apple-blue"></i>SEO Settings
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="label-apple">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" maxlength="60" class="input-apple">
                            <p class="mt-1 text-xs text-dark-text-tertiary">Rekomendasi: 50-60 karakter</p>
                        </div>

                        <div>
                            <label for="meta_description" class="label-apple">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="2" maxlength="160" class="input-apple">{{ old('meta_description') }}</textarea>
                            <p class="mt-1 text-xs text-dark-text-tertiary">Rekomendasi: 150-160 karakter</p>
                        </div>

                        <div>
                            <label for="meta_keywords" class="label-apple">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}" class="input-apple">
                            <p class="mt-1 text-xs text-dark-text-tertiary">Pisahkan dengan koma (contoh: lb3, amdal, lingkungan)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Settings -->
                <div class="card-apple p-6">
                    <h3 class="text-lg font-semibold text-dark-text-primary mb-4">
                        <i class="fas fa-cog mr-2 text-apple-green"></i>Pengaturan Publikasi
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="label-apple">Status *</label>
                            <select name="status" id="status" required class="input-apple">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>

                        <div>
                            <label for="published_at" class="label-apple">Tanggal Publikasi</label>
                            <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at') }}" class="input-apple">
                            <p class="mt-1 text-xs text-dark-text-tertiary">Kosongkan untuk publikasi sekarang</p>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-dark-separator text-apple-blue focus:ring-apple-blue">
                                <span class="ml-2 text-sm text-dark-text-primary">Jadikan artikel unggulan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card-apple p-6">
                    <h3 class="text-lg font-semibold text-dark-text-primary mb-4">
                        <i class="fas fa-image mr-2 text-apple-purple"></i>Featured Image
                    </h3>
                    
                    <div id="image-preview" class="hidden mb-4">
                        <img src="" alt="Preview" class="w-full rounded-apple">
                        <button type="button" id="remove-image" class="mt-2 text-sm text-apple-red hover:text-apple-red/80">
                            <i class="fas fa-times mr-1"></i>Hapus gambar
                        </button>
                    </div>
                    
                    <input type="file" name="featured_image" id="featured_image" accept="image/*" class="input-apple @error('featured_image') border-apple-red @enderror">
                    <input type="hidden" name="pexels_image_path" id="pexels_image_path">
                    <p class="mt-2 text-xs text-dark-text-tertiary">Format: JPG, PNG, GIF (max 2MB)</p>
                    
                    <div class="mt-3">
                        <button type="button" id="browse-pexels" class="w-full px-4 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm font-medium hover:bg-dark-bg-tertiary/80 transition-apple flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i>Cari dari Pexels
                        </button>
                    </div>
                    
                    @error('featured_image')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category & Tags -->
                <div class="card-apple p-6">
                    <h3 class="text-lg font-semibold text-dark-text-primary mb-4">
                        <i class="fas fa-tags mr-2 text-apple-orange"></i>Kategori & Tag
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="category" class="label-apple">Kategori *</label>
                            <select name="category" id="category" required class="input-apple">
                                @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tags-input" class="label-apple">Tags</label>
                            <input type="text" id="tags-input" placeholder="Ketik tag dan tekan Enter" class="input-apple">
                            <div id="tags-container" class="mt-2 flex flex-wrap gap-2"></div>
                            <p class="mt-2 text-xs text-dark-text-tertiary">Tekan Enter untuk menambah tag</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card-apple p-6">
                    <div class="space-y-3">
                        <button type="submit" class="w-full btn-primary-apple">
                            <i class="fas fa-save mr-2"></i>Simpan Artikel
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

<!-- Pexels Modal -->
<div id="pexels-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-dark-bg-secondary rounded-apple max-w-5xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Modal Header -->
        <div class="p-6 border-b border-dark-separator">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-dark-text-primary">
                    <i class="fas fa-images mr-2 text-apple-purple"></i>Cari Gambar dari Pexels
                </h2>
                <button type="button" id="close-pexels-modal" class="text-dark-text-tertiary hover:text-dark-text-primary">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <!-- Search Form -->
            <div class="flex gap-3">
                <input type="text" id="pexels-search-input" placeholder="Cari gambar (contoh: nature, business, technology)" class="input-apple flex-1">
                <button type="button" id="pexels-search-btn" class="btn-primary-apple">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
            </div>
            
            <!-- Filters -->
            <div class="mt-3 flex gap-3">
                <select id="pexels-orientation" class="input-apple text-sm">
                    <option value="">Semua Orientasi</option>
                    <option value="landscape">Landscape</option>
                    <option value="portrait">Portrait</option>
                    <option value="square">Square</option>
                </select>
                <select id="pexels-size" class="input-apple text-sm">
                    <option value="">Semua Ukuran</option>
                    <option value="large">Large (24MP)</option>
                    <option value="medium">Medium (12MP)</option>
                    <option value="small">Small (4MP)</option>
                </select>
            </div>
        </div>
        
        <!-- Modal Body -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Loading State -->
            <div id="pexels-loading" class="hidden text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-apple-blue mb-4"></i>
                <p class="text-dark-text-secondary">Mencari gambar...</p>
            </div>
            
            <!-- Empty State -->
            <div id="pexels-empty" class="text-center py-12">
                <i class="fas fa-search text-6xl text-dark-text-tertiary mb-4"></i>
                <p class="text-dark-text-secondary">Ketik kata kunci dan tekan Cari untuk menemukan gambar</p>
                <p class="text-xs text-dark-text-tertiary mt-2">Foto gratis dari Pexels.com</p>
            </div>
            
            <!-- Error State -->
            <div id="pexels-error" class="hidden text-center py-12">
                <i class="fas fa-exclamation-triangle text-6xl text-apple-red mb-4"></i>
                <p class="text-dark-text-secondary" id="pexels-error-message">Terjadi kesalahan</p>
            </div>
            
            <!-- Results Grid -->
            <div id="pexels-results" class="hidden">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="pexels-grid"></div>
                
                <!-- Pagination -->
                <div class="mt-6 flex items-center justify-center gap-2">
                    <button type="button" id="pexels-prev" class="px-4 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm font-medium hover:bg-dark-bg-tertiary/80 transition-apple disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left mr-1"></i>Sebelumnya
                    </button>
                    <span class="text-dark-text-secondary text-sm">
                        Halaman <span id="pexels-current-page">1</span>
                    </span>
                    <button type="button" id="pexels-next" class="px-4 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm font-medium hover:bg-dark-bg-tertiary/80 transition-apple disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Berikutnya<i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="p-4 border-t border-dark-separator bg-dark-bg-tertiary">
            <p class="text-xs text-dark-text-tertiary text-center">
                Foto disediakan oleh <a href="https://www.pexels.com" target="_blank" class="text-apple-blue hover:underline">Pexels</a> • 
                Gratis untuk digunakan sesuai <a href="https://www.pexels.com/license/" target="_blank" class="text-apple-blue hover:underline">Lisensi Pexels</a>
            </p>
        </div>
    </div>
</div>

<!-- CKEditor 5 Custom Build via CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<style>
    /* CKEditor Dark Theme Customization */
    .ckeditor-wrapper .ck-editor__editable {
        min-height: 500px;
        background-color: #1c1c1e !important;
        color: #f5f5f7 !important;
    }
    
    .ck.ck-editor__main > .ck-editor__editable {
        background-color: #1c1c1e !important;
        color: #f5f5f7 !important;
        border-color: #38383a !important;
    }
    
    .ck.ck-toolbar {
        background-color: #2c2c2e !important;
        border-color: #38383a !important;
    }
    
    .ck.ck-button:not(.ck-disabled):hover,
    .ck.ck-button:not(.ck-disabled):active {
        background-color: #3a3a3c !important;
    }
    
    .ck.ck-button.ck-on {
        background-color: #0a84ff !important;
        color: white !important;
    }
    
    .ck.ck-dropdown__panel {
        background-color: #2c2c2e !important;
        border-color: #38383a !important;
    }
    
    .ck.ck-list__item:hover {
        background-color: #3a3a3c !important;
    }
    
    .ck.ck-labeled-field-view > .ck-labeled-field-view__input-wrapper > .ck-input {
        background-color: #1c1c1e !important;
        color: #f5f5f7 !important;
        border-color: #38383a !important;
    }
    
    .ck-content h1, .ck-content h2, .ck-content h3, .ck-content h4, .ck-content h5, .ck-content h6 {
        color: #f5f5f7 !important;
    }
    
    .ck-content a {
        color: #0a84ff !important;
    }
    
    .ck-content blockquote {
        border-left-color: #0a84ff !important;
    }
    
    .ck-content code {
        background-color: #2c2c2e !important;
        color: #ff453a !important;
    }
    
    .ck-content pre {
        background-color: #2c2c2e !important;
        color: #f5f5f7 !important;
        border-color: #38383a !important;
    }
</style>
<script>
    // Custom Upload Adapter for CKEditor
    class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file
                .then(file => new Promise((resolve, reject) => {
                    const data = new FormData();
                    data.append('image', file);

                    fetch('{{ route("articles.upload-image") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: data
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            resolve({
                                default: result.url
                            });
                        } else {
                            reject(result.message || 'Upload failed');
                        }
                    })
                    .catch(error => {
                        reject('Upload failed: ' + error);
                    });
                }));
        }

        abort() {
            // Handle upload abort
        }
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

    // Initialize CKEditor Classic (simple, stable build)
    let editorInstance;
    ClassicEditor
        .create(document.querySelector('#content'), {
            extraPlugins: [MyCustomUploadAdapterPlugin],
            toolbar: [
                'heading', '|',
                'bold', 'italic', '|',
                'link', 'uploadImage', 'blockQuote', 'insertTable', '|',
                'bulletedList', 'numberedList', '|',
                'undo', 'redo'
            ],
            image: {
                toolbar: [
                    'imageTextAlternative', 'linkImage'
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn', 'tableRow', 'mergeTableCells'
                ]
            },
            link: {
                addTargetToExternalLinks: true
            }
        })
        .then(editor => {
            editorInstance = editor;
            console.log('CKEditor initialized successfully');
            
            // Sync editor content back to textarea on form submit
            document.querySelector('form').addEventListener('submit', function(e) {
                document.querySelector('#content').value = editorInstance.getData();
            });
        })
        .catch(error => {
            console.error('CKEditor initialization error:', error);
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
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('remove-image').addEventListener('click', function() {
        document.getElementById('featured_image').value = '';
        document.getElementById('image-preview').classList.add('hidden');
    });

    // Tags Management
    let tags = [];
    const tagsInput = document.getElementById('tags-input');
    const tagsContainer = document.getElementById('tags-container');

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

    // ============================================
    // PEXELS INTEGRATION
    // ============================================
    let currentPexelsPage = 1;
    let currentPexelsQuery = '';
    let pexelsHasMore = false;

    // Modal Elements
    const pexelsModal = document.getElementById('pexels-modal');
    const browsePexelsBtn = document.getElementById('browse-pexels');
    const closePexelsBtn = document.getElementById('close-pexels-modal');
    const pexelsSearchInput = document.getElementById('pexels-search-input');
    const pexelsSearchBtn = document.getElementById('pexels-search-btn');
    const pexelsOrientation = document.getElementById('pexels-orientation');
    const pexelsSize = document.getElementById('pexels-size');
    const pexelsLoading = document.getElementById('pexels-loading');
    const pexelsEmpty = document.getElementById('pexels-empty');
    const pexelsError = document.getElementById('pexels-error');
    const pexelsErrorMessage = document.getElementById('pexels-error-message');
    const pexelsResults = document.getElementById('pexels-results');
    const pexelsGrid = document.getElementById('pexels-grid');
    const pexelsPrevBtn = document.getElementById('pexels-prev');
    const pexelsNextBtn = document.getElementById('pexels-next');
    const pexelsCurrentPageSpan = document.getElementById('pexels-current-page');

    // Open Modal
    browsePexelsBtn.addEventListener('click', function() {
        pexelsModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Load curated photos on first open
        if (!currentPexelsQuery) {
            loadCuratedPhotos();
        }
    });

    // Close Modal
    closePexelsBtn.addEventListener('click', closePexelsModal);
    pexelsModal.addEventListener('click', function(e) {
        if (e.target === pexelsModal) {
            closePexelsModal();
        }
    });

    function closePexelsModal() {
        pexelsModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Search
    pexelsSearchBtn.addEventListener('click', performSearch);
    pexelsSearchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    function performSearch() {
        const query = pexelsSearchInput.value.trim();
        if (query.length < 2) {
            showError('Masukkan minimal 2 karakter untuk pencarian');
            return;
        }
        
        currentPexelsQuery = query;
        currentPexelsPage = 1;
        searchPexels();
    }

    function searchPexels() {
        showLoading();
        
        const params = new URLSearchParams({
            query: currentPexelsQuery,
            page: currentPexelsPage,
            per_page: 20
        });
        
        if (pexelsOrientation.value) {
            params.append('orientation', pexelsOrientation.value);
        }
        if (pexelsSize.value) {
            params.append('size', pexelsSize.value);
        }

        fetch(`{{ route('pexels.search') }}?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResults(data.data);
            } else {
                showError(data.message || 'Gagal mencari foto');
            }
        })
        .catch(error => {
            console.error('Pexels search error:', error);
            showError('Terjadi kesalahan saat mencari foto');
        });
    }

    function loadCuratedPhotos() {
        showLoading();
        
        fetch(`{{ route('pexels.curated') }}?page=${currentPexelsPage}&per_page=20`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResults(data.data);
            } else {
                showError(data.message || 'Gagal memuat foto curated');
            }
        })
        .catch(error => {
            console.error('Pexels curated error:', error);
            showError('Terjadi kesalahan saat memuat foto');
        });
    }

    function displayResults(data) {
        console.log('📦 Pexels API Response:', data);
        
        pexelsLoading.classList.add('hidden');
        pexelsEmpty.classList.add('hidden');
        pexelsError.classList.add('hidden');
        pexelsResults.classList.remove('hidden');
        
        pexelsGrid.innerHTML = '';
        
        if (!data.photos || data.photos.length === 0) {
            console.log('❌ No photos in response');
            showEmpty();
            return;
        }
        
        console.log(`🎨 Rendering ${data.photos.length} photos`);
        
        // Set explicit grid styles
        pexelsGrid.style.cssText = `
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        `;
        
        // Add responsive grid for larger screens
        if (window.innerWidth >= 768) {
            pexelsGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
        }
        if (window.innerWidth >= 1024) {
            pexelsGrid.style.gridTemplateColumns = 'repeat(4, 1fr)';
        }
        
        data.photos.forEach((photo, index) => {
            const photoCard = createPhotoCard(photo);
            pexelsGrid.appendChild(photoCard);
            console.log(`✅ Card ${index} appended`);
        });
        
        console.log(`✅ Total cards rendered: ${pexelsGrid.children.length}`);
        console.log('📐 Grid computed styles:', {
            display: window.getComputedStyle(pexelsGrid).display,
            gridTemplateColumns: window.getComputedStyle(pexelsGrid).gridTemplateColumns,
            gap: window.getComputedStyle(pexelsGrid).gap
        });
        
        // Update pagination
        pexelsCurrentPageSpan.textContent = data.page;
        pexelsPrevBtn.disabled = data.page <= 1;
        pexelsNextBtn.disabled = !data.next_page;
        pexelsHasMore = !!data.next_page;
    }

    function createPhotoCard(photo) {
        const div = document.createElement('div');
        div.className = 'pexels-photo-card group relative cursor-pointer';
        div.style.cssText = `
            aspect-ratio: 1;
            min-height: 200px;
            height: 200px;
            width: 100%;
            background-color: #2c2c2e;
            border-radius: 8px;
            overflow: hidden;
            display: block;
            position: relative;
        `;
        
        const img = document.createElement('img');
        img.src = photo.src.medium;
        img.alt = photo.alt || 'Photo from Pexels';
        img.style.cssText = `
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        `;
        img.loading = 'eager';
        img.onerror = function() {
            console.error('Failed to load image:', photo.src.medium);
            this.style.backgroundColor = '#ff453a';
            div.innerHTML = `<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: white;">Error loading image</div>`;
        };
        img.onload = function() {
            console.log('✅ Image loaded and visible:', photo.src.medium);
        };
        
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: absolute;
            inset: 0;
            background-color: rgba(0, 0, 0, 0);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        `;
        overlay.innerHTML = `
            <button type="button" style="opacity: 0; padding: 0.5rem 1rem; background-color: #0a84ff; color: white; border-radius: 8px; font-size: 0.875rem; transition: opacity 0.3s; border: none; cursor: pointer;">
                <i class="fas fa-check" style="margin-right: 0.25rem;"></i>Pilih
            </button>
        `;
        
        const credit = document.createElement('div');
        credit.style.cssText = `
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0.5rem;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        `;
        credit.innerHTML = `<p style="color: white; font-size: 0.75rem; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">oleh ${photo.photographer}</p>`;
        
        // Hover effects
        div.addEventListener('mouseenter', () => {
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            overlay.querySelector('button').style.opacity = '1';
            credit.style.opacity = '1';
        });
        
        div.addEventListener('mouseleave', () => {
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0)';
            overlay.querySelector('button').style.opacity = '0';
            credit.style.opacity = '0';
        });
        
        div.appendChild(img);
        div.appendChild(overlay);
        div.appendChild(credit);
        
        div.addEventListener('click', () => selectPhoto(photo));
        
        console.log('✅ Card created and styled for photo:', photo.id);
        
        return div;
    }

    function selectPhoto(photo) {
        // Show loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'download-loading';
        loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-75 z-[60] flex items-center justify-center';
        loadingDiv.innerHTML = `
            <div class="text-center">
                <i class="fas fa-spinner fa-spin text-5xl text-apple-blue mb-4"></i>
                <p class="text-white text-lg">Mengunduh foto dari Pexels...</p>
            </div>
        `;
        document.body.appendChild(loadingDiv);
        
        // Download photo
        fetch('{{ route('pexels.download') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                photo_id: photo.id,
                photo_url: photo.src.large2x,
                photographer_name: photo.photographer,
                photographer_url: photo.photographer_url,
                pexels_url: photo.url
            })
        })
        .then(response => response.json())
        .then(data => {
            document.body.removeChild(loadingDiv);
            
            if (data.success) {
                // Set image preview
                const preview = document.getElementById('image-preview');
                preview.querySelector('img').src = data.url;
                preview.classList.remove('hidden');
                
                // Store path for form submission
                document.getElementById('pexels_image_path').value = data.path;
                
                // Clear file input
                document.getElementById('featured_image').value = '';
                
                // Close modal
                closePexelsModal();
                
                // Show success message
                showSuccessNotification('Foto berhasil dipilih dari Pexels!');
            } else {
                showErrorNotification(data.message || 'Gagal mengunduh foto');
            }
        })
        .catch(error => {
            document.body.removeChild(loadingDiv);
            console.error('Download error:', error);
            showErrorNotification('Terjadi kesalahan saat mengunduh foto');
        });
    }

    // Pagination
    pexelsPrevBtn.addEventListener('click', () => {
        if (currentPexelsPage > 1) {
            currentPexelsPage--;
            if (currentPexelsQuery) {
                searchPexels();
            } else {
                loadCuratedPhotos();
            }
        }
    });

    pexelsNextBtn.addEventListener('click', () => {
        if (pexelsHasMore) {
            currentPexelsPage++;
            if (currentPexelsQuery) {
                searchPexels();
            } else {
                loadCuratedPhotos();
            }
        }
    });

    // Helper functions
    function showLoading() {
        pexelsLoading.classList.remove('hidden');
        pexelsEmpty.classList.add('hidden');
        pexelsError.classList.add('hidden');
        pexelsResults.classList.add('hidden');
    }

    function showEmpty() {
        pexelsLoading.classList.add('hidden');
        pexelsEmpty.classList.remove('hidden');
        pexelsError.classList.add('hidden');
        pexelsResults.classList.add('hidden');
    }

    function showError(message) {
        pexelsLoading.classList.add('hidden');
        pexelsEmpty.classList.add('hidden');
        pexelsError.classList.remove('hidden');
        pexelsResults.classList.add('hidden');
        pexelsErrorMessage.textContent = message;
    }

    function showSuccessNotification(message) {
        // Simple notification - you can enhance this with a toast library
        alert(message);
    }

    function showErrorNotification(message) {
        alert(message);
    }
</script>
@endsection
