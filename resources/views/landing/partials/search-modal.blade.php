<!-- Search Modal -->
<div id="searchModal" class="fixed inset-0 bg-black/90 backdrop-blur-md z-[1002] hidden flex items-start justify-center pt-20 px-4">
    <div class="w-full max-w-3xl">
        <div class="glass rounded-2xl p-6 md:p-8">
            <!-- Search Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold">Cari Artikel</h3>
                <button onclick="toggleSearch()" class="text-gray-400 hover:text-white transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <!-- Search Form -->
            <form action="{{ route('blog.index') }}" method="GET" class="mb-6">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           placeholder="Cari artikel, perizinan, regulasi..." 
                           class="w-full px-6 py-4 bg-dark-bg-secondary border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-apple-blue transition"
                           autofocus>
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-apple-blue hover:text-apple-blue-dark transition">
                        <i class="fas fa-search text-xl"></i>
                    </button>
                </div>
            </form>
            
            <!-- Quick Links / Popular Searches -->
            <div>
                <p class="text-sm text-gray-400 mb-3">Pencarian Populer:</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('blog.index', ['search' => 'LB3']) }}" 
                       class="px-4 py-2 bg-white/5 hover:bg-apple-blue/20 border border-white/10 hover:border-apple-blue/30 rounded-full text-sm transition"
                       onclick="toggleSearch()">
                        Perizinan LB3
                    </a>
                    <a href="{{ route('blog.index', ['search' => 'AMDAL']) }}" 
                       class="px-4 py-2 bg-white/5 hover:bg-apple-blue/20 border border-white/10 hover:border-apple-blue/30 rounded-full text-sm transition"
                       onclick="toggleSearch()">
                        AMDAL
                    </a>
                    <a href="{{ route('blog.index', ['search' => 'UKL-UPL']) }}" 
                       class="px-4 py-2 bg-white/5 hover:bg-apple-blue/20 border border-white/10 hover:border-apple-blue/30 rounded-full text-sm transition"
                       onclick="toggleSearch()">
                        UKL-UPL
                    </a>
                    <a href="{{ route('blog.index', ['search' => 'OSS']) }}" 
                       class="px-4 py-2 bg-white/5 hover:bg-apple-blue/20 border border-white/10 hover:border-apple-blue/30 rounded-full text-sm transition"
                       onclick="toggleSearch()">
                        OSS NIB
                    </a>
                    <a href="{{ route('blog.index', ['category' => 'tips']) }}" 
                       class="px-4 py-2 bg-white/5 hover:bg-apple-blue/20 border border-white/10 hover:border-apple-blue/30 rounded-full text-sm transition"
                       onclick="toggleSearch()">
                        Tips & Panduan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
