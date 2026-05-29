<!-- Search Modal -->
<div x-data="{ open: false }"
     x-show="open"
     x-cloak
     @open-search.window="open = true"
     @close-search.window="open = false"
     @keydown.escape.window="open = false"
     class="fixed inset-0 bg-black/90 backdrop-blur-md z-[1002] flex items-start justify-center pt-20 px-4">
    <div class="w-full max-w-3xl"
         @click.outside="open = false">
        <div class="bg-[var(--bg-raised)] rounded-2xl p-6 md:p-8">
            <!-- Search Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold">Cari Artikel</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-700 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <!-- Search Form -->
            <form action="{{ route('blog.index.id') }}" method="GET" class="mb-6">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           placeholder="Cari artikel, perizinan, regulasi..." 
                           class="w-full px-6 py-4 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-amber-500 transition"
                           autofocus>
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-amber-600 hover:text-amber-500 transition">
                        <i class="fas fa-search text-xl"></i>
                    </button>
                </div>
            </form>
            
            <!-- Quick Links / Popular Searches -->
            <div>
                <p class="text-sm text-gray-600 mb-3">Pencarian Populer:</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('blog.index.id', ['search' => 'LB3']) }}"
                       class="px-4 py-2 bg-gray-100 hover:bg-amber-500/10 border border-gray-200 hover:border-amber-500/30 rounded-full text-sm transition text-gray-600 hover:text-gray-900"
                       @click="open = false">
                        Perizinan LB3
                    </a>
                    <a href="{{ route('blog.index.id', ['search' => 'AMDAL']) }}"
                       class="px-4 py-2 bg-gray-100 hover:bg-amber-500/10 border border-gray-200 hover:border-amber-500/30 rounded-full text-sm transition text-gray-600 hover:text-gray-900"
                       @click="open = false">
                        AMDAL
                    </a>
                    <a href="{{ route('blog.index.id', ['search' => 'UKL-UPL']) }}"
                       class="px-4 py-2 bg-gray-100 hover:bg-amber-500/10 border border-gray-200 hover:border-amber-500/30 rounded-full text-sm transition text-gray-600 hover:text-gray-900"
                       @click="open = false">
                        UKL-UPL
                    </a>
                    <a href="{{ route('blog.index.id', ['search' => 'OSS']) }}"
                       class="px-4 py-2 bg-gray-100 hover:bg-amber-500/10 border border-gray-200 hover:border-amber-500/30 rounded-full text-sm transition text-gray-600 hover:text-gray-900"
                       @click="open = false">
                        OSS NIB
                    </a>
                    <a href="{{ route('blog.index.id', ['category' => 'tips']) }}"
                       class="px-4 py-2 bg-gray-100 hover:bg-amber-500/10 border border-gray-200 hover:border-amber-500/30 rounded-full text-sm transition text-gray-600 hover:text-gray-900"
                       @click="open = false">
                        Tips & Panduan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
