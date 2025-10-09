<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bizmark.ID - Solusi Manajemen Perizinan & Konsultan Bisnis Terpercaya</title>
    <meta name="description" content="Bizmark.ID - Solusi Manajemen Perizinan dan Konsultan Bisnis Terpercaya. Layanan perizinan OSS, AMDAL, UKL-UPL, konsultasi, dan digitalisasi administrasi untuk perusahaan modern.">
    <meta name="keywords" content="perizinan, konsultan bisnis, digitalisasi administrasi, OSS, AMDAL, UKL-UPL, legalitas usaha, permit management">
    <link rel="icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --apple-blue: #007AFF;
            --apple-blue-dark: #0051D5;
            --apple-green: #34C759;
            --dark-bg: #000000;
            --dark-bg-secondary: #1C1C1E;
            --dark-bg-tertiary: #2C2C2E;
            --dark-bg-elevated: rgba(28, 28, 30, 0.9);
            --dark-separator: rgba(84, 84, 88, 0.35);
            --dark-text-primary: #FFFFFF;
            --dark-text-secondary: rgba(235, 235, 245, 0.6);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: var(--dark-bg);
            color: var(--dark-text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
        }
        
        html { scroll-behavior: smooth; }
        
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: var(--dark-bg-elevated);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--dark-separator);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); }
        
        .hero-gradient {
            background: linear-gradient(135deg, #000000 0%, #1a1a2e 50%, #16213e 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-gradient::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 122, 255, 0.15) 0%, transparent 70%);
            top: -250px;
            right: -250px;
            animation: float 20s infinite ease-in-out;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(50px, 50px) rotate(180deg); }
        }
        
        .section {
            background: var(--dark-bg-secondary);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            border: 1px solid var(--dark-separator);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .section:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);
            color: #fff;
            border-radius: 0.75rem;
            padding: 1rem 2.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 122, 255, 0.3);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--apple-blue);
            border: 2px solid var(--apple-blue);
            border-radius: 0.75rem;
            padding: 1rem 2.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }
        
        .btn-secondary:hover {
            background: var(--apple-blue);
            color: #fff;
        }
        
        .feature-card {
            background: var(--dark-bg-tertiary);
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid var(--dark-separator);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            border-color: var(--apple-blue);
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 122, 255, 0.2);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);
            color: #fff;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-card { text-align: center; padding: 2rem; }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .form-input, .form-textarea {
            background: var(--dark-bg-tertiary);
            border: 1px solid var(--dark-separator);
            color: var(--dark-text-primary);
            border-radius: 0.5rem;
            padding: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--apple-blue);
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
        }
        
        .form-input::placeholder, .form-textarea::placeholder {
            color: var(--dark-text-secondary);
        }
        
        .mobile-menu {
            display: none;
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: var(--dark-bg-elevated);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--dark-separator);
            padding: 1rem;
        }
        
        .mobile-menu.active { display: block; }
        
        .testimonial-card {
            background: var(--dark-bg-tertiary);
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid var(--dark-separator);
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-center;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 30px rgba(0, 122, 255, 0.3);
            animation: pulse 3s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @media (max-width: 768px) {
            .stat-number { font-size: 2rem; }
            .btn-primary, .btn-secondary { padding: 0.75rem 1.5rem; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold">Bizmark.ID</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="hover:text-blue-400 transition">Beranda</a>
                    <a href="#about" class="hover:text-blue-400 transition">Tentang</a>
                    <a href="#services" class="hover:text-blue-400 transition">Layanan</a>
                    <a href="#why-us" class="hover:text-blue-400 transition">Keunggulan</a>
                    <a href="#contact" class="hover:text-blue-400 transition">Kontak</a>
                </div>
                
                <button class="md:hidden text-2xl" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <div id="mobileMenu" class="mobile-menu">
        <a href="#home" class="block py-2 hover:text-blue-400">Beranda</a>
        <a href="#about" class="block py-2 hover:text-blue-400">Tentang</a>
        <a href="#services" class="block py-2 hover:text-blue-400">Layanan</a>
        <a href="#why-us" class="block py-2 hover:text-blue-400">Keunggulan</a>
        <a href="#contact" class="block py-2 hover:text-blue-400">Kontak</a>
    </div>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient pt-32 pb-20 px-4">
        <div class="container mx-auto text-center max-w-5xl relative z-10">
            <div class="logo-container">
                <i class="fas fa-shield-alt text-white text-4xl"></i>
            </div>
            
            <h1 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight">
                Solusi <span style="background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-green) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Manajemen Perizinan</span><br>& Konsultan Bisnis Terpercaya
            </h1>
            
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto" style="color: var(--dark-text-secondary);">
                Digitalisasi administrasi, layanan perizinan profesional, dan konsultasi bisnis untuk perusahaan modern di Indonesia
            </p>
            
            <div class="flex flex-col md:flex-row justify-center items-center gap-4">
                <a href="#contact" class="btn-primary">
                    <i class="fas fa-phone-alt mr-2"></i>Konsultasi Gratis
                </a>
                <a href="#services" class="btn-secondary">
                    <i class="fas fa-info-circle mr-2"></i>Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 px-4" style="background: var(--dark-bg-secondary);">
        <div class="container mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="stat-card">
                    <div class="stat-number">10+</div>
                    <p class="text-lg" style="color: var(--dark-text-secondary);">Tahun Pengalaman</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number">500+</div>
                    <p class="text-lg" style="color: var(--dark-text-secondary);">Klien Terlayani</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number">1000+</div>
                    <p class="text-lg" style="color: var(--dark-text-secondary);">Perizinan Selesai</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number">98%</div>
                    <p class="text-lg" style="color: var(--dark-text-secondary);">Kepuasan Klien</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Tentang Bizmark.ID</h2>
                <p class="text-xl" style="color: var(--dark-text-secondary);">Partner terpercaya untuk solusi perizinan dan pengembangan bisnis Anda</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="section p-8">
                        <h3 class="text-2xl font-bold mb-4">Visi Kami</h3>
                        <p class="mb-6" style="color: var(--dark-text-secondary);">
                            Menjadi perusahaan konsultan perizinan dan bisnis terdepan di Indonesia yang mengintegrasikan teknologi digital untuk memberikan layanan berkualitas tinggi, efisien, dan transparan.
                        </p>
                        
                        <h3 class="text-2xl font-bold mb-4">Misi Kami</h3>
                        <ul class="space-y-3" style="color: var(--dark-text-secondary);">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                                <span>Memberikan solusi perizinan yang cepat, akurat, dan sesuai regulasi</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                                <span>Mendampingi klien dalam mengembangkan bisnis secara berkelanjutan</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                                <span>Menghadirkan sistem digital untuk transparansi dan efisiensi proses</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                                <span>Membangun kepercayaan melalui profesionalisme dan integritas</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="section p-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-award text-3xl text-blue-500 mr-4"></i>
                            <h4 class="text-xl font-bold">Berpengalaman</h4>
                        </div>
                        <p style="color: var(--dark-text-secondary);">Lebih dari 10 tahun melayani berbagai industri dengan track record sukses</p>
                    </div>
                    
                    <div class="section p-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-users text-3xl text-blue-500 mr-4"></i>
                            <h4 class="text-xl font-bold">Tim Profesional</h4>
                        </div>
                        <p style="color: var(--dark-text-secondary);">Legal expert, konsultan bisnis bersertifikat, dan teknisi berpengalaman</p>
                    </div>
                    
                    <div class="section p-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-network-wired text-3xl text-blue-500 mr-4"></i>
                            <h4 class="text-xl font-bold">Jaringan Luas</h4>
                        </div>
                        <p style="color: var(--dark-text-secondary);">Koneksi kuat dengan instansi pemerintah dan sektor swasta</p>
                    </div>
                    
                    <div class="section p-6">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-laptop-code text-3xl text-blue-500 mr-4"></i>
                            <h4 class="text-xl font-bold">Teknologi Digital</h4>
                        </div>
                        <p style="color: var(--dark-text-secondary);">Platform administrasi online untuk monitoring real-time dan transparansi penuh</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 px-4" style="background: var(--dark-bg-secondary);">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Layanan Kami</h2>
                <p class="text-xl" style="color: var(--dark-text-secondary);">Solusi lengkap untuk kebutuhan perizinan dan pengembangan bisnis Anda</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Manajemen Perizinan</h3>
                    <p style="color: var(--dark-text-secondary);" class="mb-4">
                        Pengurusan lengkap perizinan usaha dengan proses cepat dan transparan
                    </p>
                    <ul class="space-y-2 text-sm" style="color: var(--dark-text-secondary);">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>OSS (Online Single Submission)</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>AMDAL & UKL-UPL</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Izin Lingkungan</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>PBG & SLF</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Andalalin</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Konsultasi Bisnis</h3>
                    <p style="color: var(--dark-text-secondary);" class="mb-4">
                        Pendampingan profesional untuk pengembangan dan pertumbuhan bisnis
                    </p>
                    <ul class="space-y-2 text-sm" style="color: var(--dark-text-secondary);">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Legalitas Perusahaan</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Strategi Pengembangan Bisnis</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Perencanaan Pajak</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Business Process Improvement</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Compliance Management</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Digitalisasi Administrasi</h3>
                    <p style="color: var(--dark-text-secondary);" class="mb-4">
                        Sistem digital untuk efisiensi dan transparansi operasional perusahaan
                    </p>
                    <ul class="space-y-2 text-sm" style="color: var(--dark-text-secondary);">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Document Management System</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Workflow Automation</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Project Tracking</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Real-time Monitoring</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Reporting & Analytics</li>
                    </ul>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Legal & Compliance</h3>
                    <p style="color: var(--dark-text-secondary);">
                        Layanan legal komprehensif untuk memastikan bisnis Anda sesuai regulasi dan perundangan yang berlaku
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Partnership & Networking</h3>
                    <p style="color: var(--dark-text-secondary);">
                        Fasilitasi kerjasama bisnis dan networking dengan instansi pemerintah maupun sektor swasta
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Us Section -->
    <section id="why-us" class="py-20 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Mengapa Memilih Bizmark.ID?</h2>
                <p class="text-xl" style="color: var(--dark-text-secondary);">Keunggulan yang membuat kami menjadi partner terpercaya</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="section p-6">
                    <div class="text-center mb-4">
                        <i class="fas fa-bolt text-5xl text-blue-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center mb-3">Proses Cepat</h3>
                    <p class="text-center" style="color: var(--dark-text-secondary);">
                        Pengurusan perizinan dan konsultasi dengan timeline yang jelas dan efisien
                    </p>
                </div>
                
                <div class="section p-6">
                    <div class="text-center mb-4">
                        <i class="fas fa-eye text-5xl text-blue-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center mb-3">Transparansi Penuh</h3>
                    <p class="text-center" style="color: var(--dark-text-secondary);">
                        Monitoring real-time melalui sistem digital dengan update progress berkala
                    </p>
                </div>
                
                <div class="section p-6">
                    <div class="text-center mb-4">
                        <i class="fas fa-dollar-sign text-5xl text-blue-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center mb-3">Harga Kompetitif</h3>
                    <p class="text-center" style="color: var(--dark-text-secondary);">
                        Biaya yang jelas dan kompetitif dengan nilai tambah maksimal
                    </p>
                </div>
                
                <div class="section p-6">
                    <div class="text-center mb-4">
                        <i class="fas fa-headset text-5xl text-blue-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center mb-3">Support 24/7</h3>
                    <p class="text-center" style="color: var(--dark-text-secondary);">
                        Layanan pelanggan responsif siap membantu kapan pun Anda membutuhkan
                    </p>
                </div>
                
                <div class="section p-6">
                    <div class="text-center mb-4">
                        <i class="fas fa-shield-alt text-5xl text-blue-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center mb-3">Keamanan Data</h3>
                    <p class="text-center" style="color: var(--dark-text-secondary);">
                        Privasi dan keamanan data klien terjamin dengan sistem enkripsi terkini
                    </p>
                </div>
                
                <div class="section p-6">
                    <div class="text-center mb-4">
                        <i class="fas fa-certificate text-5xl text-blue-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center mb-3">Bersertifikat</h3>
                    <p class="text-center" style="color: var(--dark-text-secondary);">
                        Tim profesional bersertifikat dengan pengalaman dan keahlian terbukti
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 px-4" style="background: var(--dark-bg-secondary);">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Apa Kata Klien Kami</h2>
                <p class="text-xl" style="color: var(--dark-text-secondary);">Testimoni dari berbagai perusahaan yang telah mempercayai kami</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="testimonial-card">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">PT Maju Jaya</h4>
                            <p class="text-sm" style="color: var(--dark-text-secondary);">Direktur Utama</p>
                        </div>
                    </div>
                    <p style="color: var(--dark-text-secondary);">
                        "Bizmark.ID sangat membantu kami dalam pengurusan izin lingkungan. Prosesnya cepat dan transparan, highly recommended!"
                    </p>
                    <div class="mt-4 text-yellow-500">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">CV Sukses Makmur</h4>
                            <p class="text-sm" style="color: var(--dark-text-secondary);">Owner</p>
                        </div>
                    </div>
                    <p style="color: var(--dark-text-secondary);">
                        "Platform digitalnya sangat memudahkan monitoring progress perizinan. Tim yang profesional dan responsif."
                    </p>
                    <div class="mt-4 text-yellow-500">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">Startup Tech Indonesia</h4>
                            <p class="text-sm" style="color: var(--dark-text-secondary);">CEO</p>
                        </div>
                    </div>
                    <p style="color: var(--dark-text-secondary);">
                        "Konsultasi bisnis dari Bizmark.ID membantu kami menyusun strategi yang tepat untuk pertumbuhan perusahaan."
                    </p>
                    <div class="mt-4 text-yellow-500">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4">
        <div class="container mx-auto max-w-4xl">
            <div class="section p-12 text-center">
                <h2 class="text-4xl font-bold mb-4">Siap Meningkatkan Bisnis Anda?</h2>
                <p class="text-xl mb-8" style="color: var(--dark-text-secondary);">
                    Hubungi tim kami sekarang untuk konsultasi gratis dan dapatkan solusi terbaik untuk kebutuhan perizinan dan pengembangan bisnis Anda
                </p>
                <a href="#contact" class="btn-primary">
                    <i class="fas fa-phone-alt mr-2"></i>Hubungi Kami Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 px-4" style="background: var(--dark-bg-secondary);">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Hubungi Kami</h2>
                <p class="text-xl" style="color: var(--dark-text-secondary);">Kami siap membantu Anda dengan solusi terbaik</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <div class="section p-8 mb-8">
                        <h3 class="text-2xl font-bold mb-6">Informasi Kontak</h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-2xl text-blue-500 mr-4 mt-1"></i>
                                <div>
                                    <h4 class="font-bold mb-1">Alamat</h4>
                                    <p style="color: var(--dark-text-secondary);">Jl. Sudirman No. 123, Jakarta Selatan<br>DKI Jakarta 12190, Indonesia</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-phone text-2xl text-blue-500 mr-4 mt-1"></i>
                                <div>
                                    <h4 class="font-bold mb-1">Telepon</h4>
                                    <p style="color: var(--dark-text-secondary);">+62 21 1234 5678<br>+62 812 3456 7890</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-envelope text-2xl text-blue-500 mr-4 mt-1"></i>
                                <div>
                                    <h4 class="font-bold mb-1">Email</h4>
                                    <p style="color: var(--dark-text-secondary);">info@bizmark.id<br>support@bizmark.id</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-clock text-2xl text-blue-500 mr-4 mt-1"></i>
                                <div>
                                    <h4 class="font-bold mb-1">Jam Operasional</h4>
                                    <p style="color: var(--dark-text-secondary);">Senin - Jumat: 08:00 - 17:00 WIB<br>Sabtu: 08:00 - 12:00 WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="section p-8">
                        <h3 class="text-2xl font-bold mb-6">Ikuti Kami</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-600 transition">
                                <i class="fab fa-facebook-f text-white text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-500 transition">
                                <i class="fab fa-twitter text-white text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-pink-600 rounded-lg flex items-center justify-center hover:bg-pink-700 transition">
                                <i class="fab fa-instagram text-white text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-blue-700 rounded-lg flex items-center justify-center hover:bg-blue-800 transition">
                                <i class="fab fa-linkedin-in text-white text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center hover:bg-green-600 transition">
                                <i class="fab fa-whatsapp text-white text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="section p-8">
                    <h3 class="text-2xl font-bold mb-6">Kirim Pesan</h3>
                    <form action="#" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block mb-2 font-medium">Nama Lengkap</label>
                            <input type="text" name="name" required class="form-input" placeholder="Masukkan nama Anda">
                        </div>
                        
                        <div>
                            <label class="block mb-2 font-medium">Email</label>
                            <input type="email" name="email" required class="form-input" placeholder="email@example.com">
                        </div>
                        
                        <div>
                            <label class="block mb-2 font-medium">Telepon</label>
                            <input type="tel" name="phone" required class="form-input" placeholder="+62 812 3456 7890">
                        </div>
                        
                        <div>
                            <label class="block mb-2 font-medium">Subjek</label>
                            <input type="text" name="subject" required class="form-input" placeholder="Perihal yang ingin Anda tanyakan">
                        </div>
                        
                        <div>
                            <label class="block mb-2 font-medium">Pesan</label>
                            <textarea name="message" required class="form-textarea" rows="5" placeholder="Tuliskan pesan Anda di sini..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-4" style="background: var(--dark-bg); border-top: 1px solid var(--dark-separator);">
        <div class="container mx-auto max-w-6xl">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-bold">Bizmark.ID</span>
                    </div>
                    <p style="color: var(--dark-text-secondary);">
                        Solusi manajemen perizinan dan konsultan bisnis terpercaya untuk perusahaan modern di Indonesia.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Layanan</h4>
                    <ul class="space-y-2" style="color: var(--dark-text-secondary);">
                        <li><a href="#services" class="hover:text-blue-400 transition">Manajemen Perizinan</a></li>
                        <li><a href="#services" class="hover:text-blue-400 transition">Konsultasi Bisnis</a></li>
                        <li><a href="#services" class="hover:text-blue-400 transition">Digitalisasi Administrasi</a></li>
                        <li><a href="#services" class="hover:text-blue-400 transition">Legal & Compliance</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Perusahaan</h4>
                    <ul class="space-y-2" style="color: var(--dark-text-secondary);">
                        <li><a href="#about" class="hover:text-blue-400 transition">Tentang Kami</a></li>
                        <li><a href="#why-us" class="hover:text-blue-400 transition">Keunggulan</a></li>
                        <li><a href="#contact" class="hover:text-blue-400 transition">Kontak</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Legal</h4>
                    <ul class="space-y-2" style="color: var(--dark-text-secondary);">
                        <li><a href="#" class="hover:text-blue-400 transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition">FAQ</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t pt-8 text-center" style="border-color: var(--dark-separator); color: var(--dark-text-secondary);">
                <p>&copy; 2025 Bizmark.ID. All rights reserved. Made with <i class="fas fa-heart text-red-500"></i> in Indonesia</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }
        
        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth Scroll for Mobile Menu Links
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobileMenu').classList.remove('active');
            });
        });
        
        // Form Submission (you can add AJAX here)
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Terima kasih! Pesan Anda telah dikirim. Tim kami akan segera menghubungi Anda.');
            this.reset();
        });
    </script>
</body>
</html>
