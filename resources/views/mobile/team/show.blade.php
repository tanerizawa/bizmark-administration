@extends('mobile.layouts.app')

@section('title', $user->name ?? 'Detail Anggota')

@section('content')
<div class="pb-20">

    {{-- Profile Header --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
        <div class="bg-gradient-to-br from-[#0A66C2] to-[#004182] p-6 text-white text-center">
            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl font-bold">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <h2 class="text-xl font-bold">{{ $user->name }}</h2>
            <p class="text-sm opacity-90">{{ $user->email }}</p>
            @if($user->role)
            <span class="inline-block mt-2 text-xs px-3 py-1 bg-white/20 rounded-full">
                {{ ucfirst(is_object($user->role) ? ($user->role->name ?? '') : $user->role) }}
            </span>
            @endif
        </div>

        {{-- Contact Info --}}
        <div class="p-4 space-y-3 text-sm">
            @if($user->phone)
            <div class="flex items-center gap-3 text-gray-600">
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-phone text-green-600 text-xs"></i>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block">Telepon</span>
                    <a href="tel:{{ $user->phone }}" class="font-medium text-gray-900">{{ $user->phone }}</a>
                </div>
            </div>
            @endif

            <div class="flex items-center gap-3 text-gray-600">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-envelope text-blue-600 text-xs"></i>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block">Email</span>
                    <a href="mailto:{{ $user->email }}" class="font-medium text-gray-900">{{ $user->email }}</a>
                </div>
            </div>

            <div class="flex items-center gap-3 text-gray-600">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                    {{ ($user->is_active ?? false) ? 'bg-green-50' : 'bg-red-50' }}">
                    <i class="fas fa-circle text-xs {{ ($user->is_active ?? false) ? 'text-green-600' : 'text-red-600' }}"></i>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block">Status</span>
                    <span class="font-medium {{ ($user->is_active ?? false) ? 'text-green-700' : 'text-red-700' }}">
                        {{ ($user->is_active ?? false) ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </div>
            </div>

            @if($user->created_at)
            <div class="flex items-center gap-3 text-gray-600">
                <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar text-gray-500 text-xs"></i>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block">Bergabung</span>
                    <span class="font-medium text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Assigned Tasks --}}
    @if($user->assignedTasks && $user->assignedTasks->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-tasks text-gray-400"></i>
                Task Terbaru ({{ $user->assignedTasks->count() }})
            </h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($user->assignedTasks as $task)
            <a href="{{ route('mobile.tasks.show', $task->id) }}" 
               class="block p-3 active:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-md border-2 flex items-center justify-center flex-shrink-0
                        @if($task->status === 'done') bg-green-500 border-green-500 @else border-gray-300 @endif">
                        @if($task->status === 'done')
                        <i class="fas fa-check text-white text-xs"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate
                            @if($task->status === 'done') line-through text-gray-500 @endif">
                            {{ $task->title }}
                        </p>
                        @if($task->due_date)
                        <p class="text-xs mt-0.5
                            @if($task->due_date < now() && $task->status !== 'done') text-red-600 @else text-gray-400 @endif">
                            {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                        </p>
                        @endif
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        @if($task->status === 'done') bg-green-100 text-green-700
                        @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        @if($task->status === 'done') Selesai
                        @elseif($task->status === 'in_progress') Dikerjakan
                        @else To Do
                        @endif
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-tasks text-gray-300 text-xl"></i>
        </div>
        <p class="text-sm text-gray-400">Belum ada task yang ditugaskan</p>
    </div>
    @endif
</div>
@endsection
