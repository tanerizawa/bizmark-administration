@extends('mobile.layouts.app')

@section('title', 'Tentang Aplikasi')

@section('content')
<div class="pb-20">
    
    {{-- App Logo & Name --}}
    <div class="text-center py-8">
        <div class="w-24 h-24 bg-gradient-to-br from-[#0077b5] to-[#004d6d] rounded-3xl mx-auto mb-4 flex items-center justify-center shadow-lg">
            <i class="fas fa-briefcase text-4xl text-white"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Bizmark Admin</h1>
        <div class="text-sm text-gray-600">Versi 2.5.3</div>
        <div class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
            PWA Ready
        </div>
    </div>

    {{-- About Info --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <i class="fas fa-info-circle text-[#0077b5] text-xl"></i>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Tentang Aplikasi</div>
                    <div class="text-sm text-gray-600 mt-1">
                        Bizmark Admin adalah aplikasi manajemen proyek dan administrasi yang dirancang untuk memudahkan pengelolaan proyek, keuangan, dan tim secara efisien.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <i class="fas fa-code-branch text-[#0077b5] text-xl"></i>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Build Information</div>
                    <div class="text-xs text-gray-600 mt-1 space-y-0.5">
                        <div>Environment: <span class="font-mono">{{ config('app.env') }}</span></div>
                        <div>Laravel: <span class="font-mono">{{ app()->version() }}</span></div>
                        <div>PHP: <span class="font-mono">{{ PHP_VERSION }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-clock text-[#0077b5] text-xl"></i>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Last Updated</div>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ \Carbon\Carbon::parse('2025-11-18')->format('d F Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Features --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
        <div class="p-4 bg-gray-50 border-b border-gray-100">
            <div class="font-semibold text-gray-900">Fitur Utama</div>
        </div>
        <div class="divide-y divide-gray-100">
            <div class="p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-blue-600"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-900">Manajemen Proyek</div>
                    <div class="text-xs text-gray-500">Kelola proyek dan tugas dengan mudah</div>
                </div>
            </div>
            <div class="p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-green-600"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-900">Keuangan</div>
                    <div class="text-xs text-gray-500">Monitor cash flow dan expenses</div>
                </div>
            </div>
            <div class="p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-purple-600"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-900">Approval System</div>
                    <div class="text-xs text-gray-500">Sistem persetujuan terintegrasi</div>
                </div>
            </div>
            <div class="p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bell text-orange-600"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-900">Notifikasi Real-time</div>
                    <div class="text-xs text-gray-500">Update instan untuk setiap aktivitas</div>
                </div>
            </div>
            <div class="p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-mobile-alt text-indigo-600"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-900">PWA Support</div>
                    <div class="text-xs text-gray-500">Install & gunakan seperti native app</div>
                </div>
            </div>
            <div class="p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wifi-slash text-gray-600"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-900">Offline Mode</div>
                    <div class="text-xs text-gray-500">Tetap produktif tanpa internet</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contact & Support --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
        <div class="p-4 bg-gray-50 border-b border-gray-100">
            <div class="font-semibold text-gray-900">Kontak & Bantuan</div>
        </div>
        <div class="divide-y divide-gray-100">
            <a href="mailto:support@bizmark.id" class="p-4 flex items-center gap-3 active:bg-gray-50">
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-envelope text-red-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Email Support</div>
                    <div class="text-xs text-gray-500">support@bizmark.id</div>
                </div>
                <i class="fas fa-external-link-alt text-gray-400 text-xs"></i>
            </a>
            <a href="https://bizmark.id" target="_blank" class="p-4 flex items-center gap-3 active:bg-gray-50">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-globe text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Website</div>
                    <div class="text-xs text-gray-500">bizmark.id</div>
                </div>
                <i class="fas fa-external-link-alt text-gray-400 text-xs"></i>
            </a>
        </div>
    </div>

    {{-- Legal --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
        <a href="#" onclick="alert('Privacy Policy akan ditampilkan di sini'); return false;" class="p-4 flex items-center justify-between active:bg-gray-50">
            <div class="font-medium text-gray-900">Privacy Policy</div>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        </a>
        <div class="border-t border-gray-100"></div>
        <a href="#" onclick="alert('Terms of Service akan ditampilkan di sini'); return false;" class="p-4 flex items-center justify-between active:bg-gray-50">
            <div class="font-medium text-gray-900">Terms of Service</div>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        </a>
        <div class="border-t border-gray-100"></div>
        <a href="#" onclick="alert('Licenses akan ditampilkan di sini'); return false;" class="p-4 flex items-center justify-between active:bg-gray-50">
            <div class="font-medium text-gray-900">Open Source Licenses</div>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        </a>
    </div>

    {{-- Copyright --}}
    <div class="text-center text-xs text-gray-500 py-4">
        <div>© 2025 Bizmark. All rights reserved.</div>
        <div class="mt-1">Made with <i class="fas fa-heart text-red-500"></i> in Indonesia</div>
    </div>

</div>
@endsection
