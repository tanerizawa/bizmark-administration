@extends('mobile.layouts.app')

@section('title', 'Profile')

@section('content')
<div class="pb-20">
    
    {{-- Profile Header --}}
    <div class="bg-gradient-to-br from-[#0077b5] to-[#004d6d] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center gap-4 mb-4">
            @if($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full border-4 border-white/20 object-cover">
            @else
                <div class="w-20 h-20 rounded-full border-4 border-white/20 bg-white/10 flex items-center justify-center text-2xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div class="flex-1">
                <h2 class="text-xl font-bold mb-1">{{ $user->name }}</h2>
                <div class="text-sm opacity-90">{{ $user->email }}</div>
                @if($user->role)
                <div class="inline-block mt-2 px-3 py-1 bg-white/20 rounded-full text-xs font-medium">
                    {{ $user->role->name }}
                </div>
                @endif
            </div>
        </div>
        
        @if($user->phone)
        <div class="flex items-center gap-2 text-sm opacity-90">
            <i class="fas fa-phone text-xs"></i>
            <span>{{ $user->phone }}</span>
        </div>
        @endif
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-2xl font-bold text-[#0077b5] mb-1">{{ $stats['total_tasks'] }}</div>
            <div class="text-xs text-gray-600">Total Tugas</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-2xl font-bold text-green-600 mb-1">{{ $stats['tasks_completed'] }}</div>
            <div class="text-xs text-gray-600">Selesai</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-2xl font-bold text-orange-600 mb-1">{{ $stats['tasks_pending'] }}</div>
            <div class="text-xs text-gray-600">Pending</div>
        </div>
    </div>

    {{-- Menu Options --}}
    <div class="space-y-2">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <a href="#" onclick="editProfile()" class="flex items-center gap-4 p-4 active:bg-gray-50">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-edit text-[#0077b5]"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Edit Profile</div>
                    <div class="text-xs text-gray-500">Ubah nama, email, dan foto</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <a href="#" onclick="changePassword()" class="flex items-center gap-4 p-4 active:bg-gray-50">
                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-amber-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Ubah Password</div>
                    <div class="text-xs text-gray-500">Keamanan akun</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <a href="#" onclick="notificationSettings()" class="flex items-center gap-4 p-4 active:bg-gray-50">
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bell text-purple-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Notifikasi</div>
                    <div class="text-xs text-gray-500">Pengaturan pemberitahuan</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-4 p-4 active:bg-gray-50 text-left">
                    <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-red-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Logout</div>
                        <div class="text-xs text-gray-500">Keluar dari akun</div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Account Info --}}
    <div class="mt-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
        <div class="text-xs font-medium text-gray-600 mb-2">INFORMASI AKUN</div>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">User ID</span>
                <span class="font-medium text-gray-900">#{{ $user->id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Status</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Bergabung</span>
                <span class="font-medium text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>

</div>

<script>
function editProfile() {
    alert('Feature coming soon: Edit Profile');
}

function changePassword() {
    alert('Feature coming soon: Change Password');
}

function notificationSettings() {
    alert('Feature coming soon: Notification Settings');
}
</script>
@endsection
