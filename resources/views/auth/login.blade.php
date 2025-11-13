<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Bizmark Permit Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            /* Apple Design System Colors */
            --apple-blue: #007AFF;
            --apple-blue-dark: #0051D5;
            --apple-green: #34C759;
            --apple-indigo: #5856D6;
            --apple-orange: #FF9500;
            --apple-pink: #FF2D55;
            --apple-purple: #AF52DE;
            --apple-red: #FF3B30;
            --apple-teal: #5AC8FA;
            --apple-yellow: #FFCC00;

            /* Dark Mode Colors */
            --dark-bg: #000000;
            --dark-bg-secondary: #1C1C1E;
            --dark-bg-tertiary: #2C2C2E;
            --dark-bg-elevated: rgba(28, 28, 30, 0.9);
            --dark-separator: rgba(84, 84, 88, 0.35);
            --dark-text-primary: #FFFFFF;
            --dark-text-secondary: rgba(235, 235, 245, 0.6);
            --dark-text-tertiary: rgba(235, 235, 245, 0.3);
        }

        body {
            background-color: var(--dark-bg);
            color: var(--dark-text-primary);
        }

        .login-card {
            background-color: var(--dark-bg-elevated);
            border: 1px solid var(--dark-separator);
            backdrop-filter: blur(20px);
        }

        .login-header {
            background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);
        }

        .form-input {
            background-color: var(--dark-bg-tertiary);
            border: 1px solid var(--dark-separator);
            color: var(--dark-text-primary);
        }

        .form-input:focus {
            border-color: var(--apple-blue);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
        }

        .form-input::placeholder {
            color: var(--dark-text-tertiary);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 122, 255, 0.3);
        }

        .icon-container {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .info-box {
            background-color: rgba(28, 28, 30, 0.8);
            border: 1px solid var(--dark-separator);
            backdrop-filter: blur(10px);
        }

        .error-box {
            background-color: rgba(255, 59, 48, 0.1);
            border-left: 4px solid var(--apple-red);
        }

        .success-box {
            background-color: rgba(52, 199, 89, 0.1);
            border-left: 4px solid var(--apple-green);
        }

        .checkbox-custom {
            accent-color: var(--apple-blue);
        }

        .label-text {
            color: var(--dark-text-secondary);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="login-card rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="login-header p-8 text-center">
                <div class="w-20 h-20 icon-container rounded-full mx-auto mb-4 flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Bizmark.ID</h1>
                <p class="text-white/80">Sistem Manajemen Perizinan</p>
            </div>

            <!-- Form -->
            <div class="p-8">
                @if ($errors->any())
                    <div class="mb-6 error-box p-4 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" style="color: var(--apple-red);" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm font-medium" style="color: var(--apple-red);">
                                {{ $errors->first() }}
                            </p>
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-6 success-box p-4 rounded-lg">
                        <p class="text-sm" style="color: var(--apple-green);">{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email/Username Field -->
                    <div>
                        <label for="login" class="block text-sm font-medium label-text mb-2">
                            Username atau Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5" style="color: var(--dark-text-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input 
                                id="login" 
                                type="text" 
                                name="login" 
                                value="{{ old('login') }}" 
                                required 
                                autocomplete="username" 
                                autofocus
                                class="form-input block w-full pl-10 pr-3 py-3 rounded-lg transition @error('login') border-red-500 @enderror"
                                placeholder="Masukkan username atau email"
                            >
                        </div>
                        @error('login')
                            <p class="mt-1 text-sm" style="color: var(--apple-red);">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium label-text mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5" style="color: var(--dark-text-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                class="form-input block w-full pl-10 pr-3 py-3 rounded-lg transition @error('password') border-red-500 @enderror"
                                placeholder="Masukkan password"
                            >
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm" style="color: var(--apple-red);">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            {{ old('remember') ? 'checked' : '' }}
                            class="h-4 w-4 checkbox-custom rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm label-text">
                            Ingat saya
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="btn-primary w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-medium text-white"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Masuk
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-8 py-6 border-t" style="background-color: var(--dark-bg-secondary); border-color: var(--dark-separator);">
                <p class="text-center text-sm" style="color: var(--dark-text-secondary);">
                    &copy; {{ date('Y') }} Bizmark.ID - Sistem Manajemen Perizinan
                </p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="info-box mt-6 rounded-lg p-4 text-center">
            <p class="text-sm" style="color: var(--dark-text-secondary);">
                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                Gunakan kredensial yang telah diberikan untuk mengakses sistem
            </p>
        </div>
    </div>
</body>
</html>
