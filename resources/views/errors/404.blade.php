<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Bizmark.ID</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'apple-blue': '#007AFF',
                        'apple-blue-dark': '#0051D5',
                        'apple-green': '#34C759',
                        'dark-bg': '#000000',
                        'dark-bg-secondary': '#1C1C1E',
                    }
                }
            }
        }
    </script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #000000;
            color: #ffffff;
        }
        
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #007AFF 0%, #0051D5 100%);
            padding: 0.75rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 122, 255, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.75rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="dark">
    <div class="min-h-screen flex items-center justify-center px-4 relative overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-apple-blue/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-apple-green/10 rounded-full blur-3xl"></div>
        </div>
        
        <!-- Content -->
        <div class="relative z-10 max-w-4xl mx-auto text-center">
            <!-- 404 Icon -->
            <div class="mb-8 floating">
                <div class="inline-flex items-center justify-center w-32 h-32 glass rounded-full">
                    <i class="fas fa-search text-6xl text-apple-blue"></i>
                </div>
            </div>
            
            <!-- 404 Text -->
            <h1 class="text-9xl md:text-[12rem] font-bold mb-4 bg-gradient-to-r from-apple-blue to-apple-green bg-clip-text text-transparent">
                404
            </h1>
            
            <!-- Error Message -->
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Halaman Tidak Ditemukan
            </h2>
            
            <p class="text-xl text-gray-400 mb-8 max-w-2xl mx-auto leading-relaxed">
                Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin halaman tersebut telah dipindahkan atau dihapus.
            </p>
            
            <!-- Search Box -->
            <div class="mb-8 max-w-xl mx-auto">
                <div class="glass rounded-2xl p-2">
                    <form action="/blog" method="GET" class="flex gap-2">
                        <input type="text" 
                               name="search" 
                               placeholder="Cari artikel atau informasi..." 
                               class="flex-1 px-6 py-3 bg-transparent text-white placeholder-gray-500 focus:outline-none">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-search mr-2"></i>Cari
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 justify-center mb-12">
                <a href="/" class="btn-primary">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
                <a href="/blog" class="btn-secondary">
                    <i class="fas fa-newspaper mr-2"></i>
                    Baca Artikel
                </a>
                <a href="https://wa.me/6281382605030" target="_blank" class="btn-secondary">
                    <i class="fab fa-whatsapp mr-2"></i>
                    Hubungi Kami
                </a>
            </div>
            
            <!-- Quick Links -->
            <div class="glass rounded-2xl p-8 max-w-2xl mx-auto">
                <h3 class="text-xl font-bold mb-6">Halaman Populer:</h3>
                <div class="grid md:grid-cols-2 gap-4 text-left">
                    <a href="/#services" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition">
                        <div class="w-10 h-10 bg-apple-blue/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-briefcase text-apple-blue"></i>
                        </div>
                        <div>
                            <div class="font-semibold">Layanan Kami</div>
                            <div class="text-sm text-gray-400">Perizinan LB3, AMDAL, UKL-UPL</div>
                        </div>
                    </a>
                    
                    <a href="/#process" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition">
                        <div class="w-10 h-10 bg-apple-green/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-apple-green"></i>
                        </div>
                        <div>
                            <div class="font-semibold">Proses Perizinan</div>
                            <div class="text-sm text-gray-400">5 langkah mudah & transparan</div>
                        </div>
                    </a>
                    
                    <a href="/#about" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition">
                        <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-info-circle text-purple-400"></i>
                        </div>
                        <div>
                            <div class="font-semibold">Tentang Kami</div>
                            <div class="text-sm text-gray-400">Konsultan terpercaya sejak 2013</div>
                        </div>
                    </a>
                    
                    <a href="/blog" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition">
                        <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-newspaper text-orange-400"></i>
                        </div>
                        <div>
                            <div class="font-semibold">Blog & Artikel</div>
                            <div class="text-sm text-gray-400">Info perizinan & regulasi</div>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Error Code -->
            <div class="mt-8 text-sm text-gray-600">
                Error Code: 404 | Not Found
            </div>
        </div>
    </div>
</body>
</html>
