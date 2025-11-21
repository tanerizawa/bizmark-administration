<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Bizmark.id</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        linkedin: {
                            50: '#e7f3f8',
                            100: '#cce7f1',
                            200: '#99cfe3',
                            300: '#66b7d5',
                            400: '#339fc7',
                            500: '#0077B5', // Official LinkedIn Blue
                            600: '#005582',
                            700: '#004161',
                            800: '#002d41',
                            900: '#001820',
                        },
                        gold: {
                            400: '#F2CD49',
                            500: '#F2CD49',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background: linear-gradient(135deg, #0077B5 0%, #005582 50%, #003d5c 100%);
        }
        .text-gradient {
            background: linear-gradient(135deg, #0077B5 0%, #005582 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-3">
                <i class="fas fa-building text-gold-400 text-3xl mr-2"></i>
                <h1 class="text-4xl font-bold text-white">
                    Bizmark<span class="text-gold-400">.id</span>
                </h1>
            </div>
            <p class="text-white/80 mt-2">Portal Klien - Verifikasi Email</p>
        </div>

        <!-- Email Verification Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-linkedin-50 rounded-full mb-4">
                    <i class="fas fa-envelope-open-text text-linkedin-500 text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Email Anda</h2>
                <p class="text-gray-600">
                    Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan.
                </p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-start">
                    <i class="fas fa-check-circle mt-0.5 mr-3 flex-shrink-0 text-green-600"></i>
                    <div>
                        <p class="font-semibold">Berhasil!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-linkedin-50 border border-linkedin-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-linkedin-600 mt-0.5 mr-3 flex-shrink-0"></i>
                    <div class="text-sm text-linkedin-800">
                        <p class="font-semibold mb-1">Cek Email Anda</p>
                        <p>Link verifikasi dikirim ke: <strong>{{ Auth::guard('client')->user()->email }}</strong></p>
                        <p class="mt-1 text-xs">Jika tidak menemukan email, cek folder spam atau junk.</p>
                    </div>
                </div>
            </div>

            <!-- Resend Button -->
            <form method="POST" action="{{ route('client.verification.send') }}">
                @csrf
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-linkedin-500 to-linkedin-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-linkedin-600 hover:to-linkedin-700 transition duration-300 shadow-lg mb-4"
                >
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Ulang Email Verifikasi
                </button>
            </form>

            <!-- Logout -->
            <form method="POST" action="{{ route('client.logout') }}">
                @csrf
                <button 
                    type="submit" 
                    class="w-full border-2 border-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-50 transition duration-300"
                >
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </button>
            </form>
        </div>

        <!-- Help Section -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 mt-6 border border-white/20">
            <h3 class="text-white font-semibold mb-2 flex items-center">
                <i class="fas fa-question-circle mr-2"></i>Butuh Bantuan?
            </h3>
            <ul class="text-white/90 text-sm space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check mr-2 mt-0.5 flex-shrink-0 text-gold-400"></i>
                    <span>Pastikan email yang Anda masukkan sudah benar</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check mr-2 mt-0.5 flex-shrink-0 text-gold-400"></i>
                    <span>Tunggu beberapa menit untuk email masuk</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check mr-2 mt-0.5 flex-shrink-0 text-gold-400"></i>
                    <span>Periksa folder spam/junk di email Anda</span>
                </li>
            </ul>
            <a href="https://wa.me/6283879602855" target="_blank" class="block mt-3 text-white hover:text-gold-400 text-sm font-medium transition-colors">
                <i class="fab fa-whatsapp mr-2"></i>Hubungi Support via WhatsApp
            </a>
        </div>

        <!-- Footer -->
        <p class="text-center text-white/70 text-sm mt-8">
            © {{ date('Y') }} Bizmark.id - Platform Perizinan Digital
        </p>
    </div>
</body>
</html>
