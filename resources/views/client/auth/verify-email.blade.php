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
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white">Bizmark<span class="text-yellow-300">.id</span></h1>
            <p class="text-white/80 mt-2">Portal Klien - Verifikasi Email</p>
        </div>

        <!-- Email Verification Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                    <i class="fas fa-envelope-open-text text-yellow-600 text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Verifikasi Email Anda</h2>
                <p class="text-gray-600">
                    Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan.
                </p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-start">
                    <i class="fas fa-check-circle mt-0.5 mr-3 flex-shrink-0"></i>
                    <div>
                        <p class="font-semibold">Berhasil!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3 flex-shrink-0"></i>
                    <div class="text-sm text-blue-800">
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
                    class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-purple-800 transition duration-300 shadow-lg mb-4"
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
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 mt-6">
            <h3 class="text-white font-semibold mb-2 flex items-center">
                <i class="fas fa-question-circle mr-2"></i>Butuh Bantuan?
            </h3>
            <ul class="text-white/80 text-sm space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check mr-2 mt-0.5 flex-shrink-0"></i>
                    <span>Pastikan email yang Anda masukkan sudah benar</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check mr-2 mt-0.5 flex-shrink-0"></i>
                    <span>Tunggu beberapa menit untuk email masuk</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check mr-2 mt-0.5 flex-shrink-0"></i>
                    <span>Periksa folder spam/junk di email Anda</span>
                </li>
            </ul>
            <a href="https://wa.me/62838796028550" target="_blank" class="block mt-3 text-white hover:text-yellow-300 text-sm font-medium">
                <i class="fab fa-whatsapp mr-2"></i>Hubungi Support via WhatsApp
            </a>
        </div>

        <!-- Footer -->
        <p class="text-center text-white/60 text-sm mt-8">
            © {{ date('Y') }} Bizmark.id - Solusi Perizinan Terpercaya
        </p>
    </div>
</body>
</html>
