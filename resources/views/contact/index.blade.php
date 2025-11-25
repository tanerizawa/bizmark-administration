@extends('landing.layout')

@section('title', 'Hubungi Kami - Bizmark.ID')
@section('meta_title', 'Hubungi Kami | Konsultan Perizinan Terpercaya - Bizmark.ID')
@section('meta_description', 'Hubungi Bizmark.ID untuk konsultasi perizinan OSS, AMDAL, UKL-UPL, PBG, SLF. Tim ahli kami siap membantu Anda. Konsultasi gratis via telepon, email, atau WhatsApp.')
@section('meta_keywords', 'contact bizmark, hubungi konsultan perizinan, konsultasi gratis, nomor telepon bizmark, email bizmark, alamat kantor bizmark jakarta')

@section('content')
<style>
    :root {
        --apple-blue: #007AFF;
        --apple-blue-dark: #0051D5;
        --dark-bg: #000000;
        --dark-bg-secondary: #1C1C1E;
        --dark-bg-tertiary: #2C2C2E;
        --dark-separator: rgba(84, 84, 88, 0.35);
        --dark-text-primary: #FFFFFF;
        --dark-text-secondary: rgba(235, 235, 245, 0.6);
    }
    
    .contact-hero {
        background: linear-gradient(135deg, #000000 0%, #1a1a2e 50%, #16213e 100%);
        min-height: 50vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        margin-top: 80px;
    }
    
    .contact-hero::before {
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
    
    .info-card {
        background: var(--dark-bg-secondary);
        border-radius: 1.5rem;
        padding: 2rem;
        border: 1px solid var(--dark-separator);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    }
    
    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.25);
    }
    
    .contact-form {
        background: var(--dark-bg-secondary);
        border-radius: 1.5rem;
        padding: 3rem;
        border: 1px solid var(--dark-separator);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    }
    
    .form-input {
        width: 100%;
        padding: 1rem 1.5rem;
        background: var(--dark-bg-tertiary);
        border: 1px solid var(--dark-separator);
        border-radius: 0.75rem;
        font-size: 16px;
        color: var(--dark-text-primary);
        transition: all 0.3s ease;
    }
    
    .form-input::placeholder {
        color: var(--dark-text-secondary);
    }
    
    .form-input:focus {
        outline: none;
        border-color: var(--apple-blue);
        box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
    }
    
    .form-textarea {
        min-height: 150px;
        resize: vertical;
    }
    
    .submit-btn {
        background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);
        color: white;
        padding: 1rem 2.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 16px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        min-height: 48px;
    }
    
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 122, 255, 0.3);
    }
    
    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .icon-box {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        margin-bottom: 1.5rem;
    }
    
    .alert {
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .alert-success {
        background: rgba(52, 199, 89, 0.1);
        border: 1px solid rgba(52, 199, 89, 0.3);
        color: #34C759;
    }
    
    .alert-error {
        background: rgba(255, 59, 48, 0.1);
        border: 1px solid rgba(255, 59, 48, 0.3);
        color: #FF3B30;
    }
    
    .map-container {
        border-radius: 1.5rem;
        overflow: hidden;
        height: 400px;
        border: 1px solid var(--dark-separator);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    }
    
    @media (max-width: 768px) {
        .contact-hero {
            min-height: 40vh;
            margin-top: 64px;
        }
        
        .contact-form {
            padding: 2rem;
        }
        
        .info-card {
            padding: 1.5rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center text-white" data-aos="fade-up">
            <h1 class="text-5xl md:text-6xl font-bold mb-6">Hubungi Kami</h1>
            <p class="text-xl md:text-2xl" style="color: var(--dark-text-secondary);">
                Tim ahli kami siap membantu Anda menyelesaikan kebutuhan perizinan usaha
            </p>
        </div>
    </div>
</section>

<!-- Contact Info Cards -->
<section class="py-20 px-4" style="background: var(--dark-bg);">
    <div class="container mx-auto max-w-6xl">
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <!-- Phone -->
            <div class="info-card text-center" data-aos="fade-up" data-aos-delay="0">
                <div class="icon-box mx-auto">
                    <i class="fas fa-phone"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Telepon</h3>
                <p class="mb-4" style="color: var(--dark-text-secondary);">Senin - Jumat: 08:00 - 17:00 WIB</p>
                <a href="tel:+622112345678" class="text-xl font-semibold hover:text-blue-400 transition block mb-2" style="color: var(--apple-blue);">
                    +62 21 1234 5678
                </a>
                <a href="tel:+6283879602855" class="text-xl font-semibold hover:text-blue-400 transition block" style="color: var(--apple-blue);">
                    +62 838 7960 2855
                </a>
            </div>
            
            <!-- Email -->
            <div class="info-card text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="icon-box mx-auto">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Email</h3>
                <p class="mb-4" style="color: var(--dark-text-secondary);">Respon dalam 24 jam</p>
                <a href="mailto:cs@bizmark.id" class="text-xl font-semibold hover:text-blue-400 transition block" style="color: var(--apple-blue);">
                    cs@bizmark.id
                </a>
            </div>
            
            <!-- WhatsApp -->
            <div class="info-card text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="icon-box mx-auto" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">WhatsApp</h3>
                <p class="mb-4" style="color: var(--dark-text-secondary);">Chat langsung dengan tim kami</p>
                <a href="https://wa.me/6283879602855?text=Halo%20PT%20Cangah%20Pajaratan%20Mandiri%2C%20saya%20ingin%20konsultasi%20tentang%20perizinan" target="_blank" class="text-xl font-semibold text-green-500 hover:text-green-400 transition">
                    +62 838 7960 2855
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form & Map -->
<section class="py-20 px-4" style="background: var(--dark-bg);">
    <div class="container mx-auto max-w-6xl">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div data-aos="fade-right">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">Kirim Pesan</h2>
                <p class="mb-8" style="color: var(--dark-text-secondary);">
                    Isi form di bawah ini dan tim kami akan menghubungi Anda sesegera mungkin
                </p>
                
                @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle text-2xl"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-error" role="alert">
                    <i class="fas fa-exclamation-circle text-2xl"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif
                
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold mb-2">Nama Lengkap *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}"
                            class="form-input @error('name') border-red-500 @enderror" 
                            placeholder="Masukkan nama lengkap Anda"
                            required
                        >
                        @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold mb-2">Email *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="form-input @error('email') border-red-500 @enderror" 
                            placeholder="nama@perusahaan.com"
                            required
                        >
                        @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-semibold mb-2">Nomor Telepon *</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone') }}"
                            class="form-input @error('phone') border-red-500 @enderror" 
                            placeholder="08xx-xxxx-xxxx"
                            required
                        >
                        @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-semibold mb-2">Subjek *</label>
                        <input 
                            type="text" 
                            id="subject" 
                            name="subject" 
                            value="{{ old('subject') }}"
                            class="form-input @error('subject') border-red-500 @enderror" 
                            placeholder="Perihal pesan Anda"
                            required
                        >
                        @error('subject')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-semibold mb-2">Pesan *</label>
                        <textarea 
                            id="message" 
                            name="message" 
                            class="form-input form-textarea @error('message') border-red-500 @enderror" 
                            placeholder="Jelaskan kebutuhan atau pertanyaan Anda..."
                            required
                        >{{ old('message') }}</textarea>
                        @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn w-full justify-center">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Pesan</span>
                    </button>
                </form>
            </div>
            
            <!-- Address & Map -->
            <div data-aos="fade-left">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">Kantor Kami</h2>
                
                <div class="info-card mb-8">
                    <div class="flex items-start gap-4">
                        <div class="text-2xl mt-1" style="color: var(--apple-blue);">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-2">Alamat</h3>
                            <p class="leading-relaxed" style="color: var(--dark-text-secondary);">
                                Jl. Sudirman No. 123<br>
                                Jakarta Selatan 12190<br>
                                DKI Jakarta, Indonesia
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="info-card mb-8">
                    <div class="flex items-start gap-4">
                        <div class="text-2xl mt-1" style="color: var(--apple-blue);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-2">Jam Operasional</h3>
                            <div class="space-y-1" style="color: var(--dark-text-secondary);">
                                <p><strong style="color: var(--dark-text-primary);">Senin - Jumat:</strong> 08:00 - 17:00 WIB</p>
                                <p><strong style="color: var(--dark-text-primary);">Sabtu:</strong> 08:00 - 12:00 WIB</p>
                                <p><strong style="color: var(--dark-text-primary);">Minggu & Libur:</strong> Tutup</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Google Maps -->
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2296489309147!2d106.81507031476915!3d-6.225584695501583!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x5371bf2019b394!2sJl.%20Sudirman%2C%20Jakarta%20Selatan%2C%20DKI%20Jakarta!5e0!3m2!1sen!2sid!4v1732359000000!5m2!1sen!2sid" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 px-4" style="background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);">
    <div class="container mx-auto max-w-4xl text-center text-white" data-aos="zoom-in">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Butuh Konsultasi Cepat?
        </h2>
        <p class="text-xl mb-8 opacity-90">
            Dapatkan analisis gratis untuk kebutuhan perizinan bisnis Anda
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="https://wa.me/6283879602855?text=Halo%20PT%20Cangah%20Pajaratan%20Mandiri%2C%20saya%20ingin%20konsultasi%20tentang%20perizinan" target="_blank" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 px-8 py-4 rounded-xl font-semibold text-lg transition-all hover:scale-105">
                <i class="fab fa-whatsapp text-2xl"></i>
                Chat WhatsApp
            </a>
            <a href="{{ route('landing.service-inquiry.create') }}" class="inline-flex items-center gap-2 bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-semibold text-lg transition-all hover:scale-105">
                <i class="fas fa-robot text-2xl"></i>
                Analisis AI Gratis
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Mengirim...</span>';
    });
</script>
@endpush

@endsection
