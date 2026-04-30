@extends('layouts.app')

@section('title', 'Create Email Account')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto"
     x-data="{
         autoReply: {{ old('auto_reply_enabled') ? 'true' : 'false' }},
         typeHelp: 'Choose shared for team emails (cs@, sales@)',
         updateTypeHelp(val) {
             this.typeHelp = val === 'personal'
                 ? 'Personal accounts can only have one user assigned'
                 : 'Choose shared for team emails (cs@, sales@)';
         }
     }">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-400"></i>Create Email Account
            </h1>
            <p class="text-gray-400 mt-1">Add a new company email account</p>
        </div>
        <a href="{{ route('admin.email-accounts.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 text-sm font-medium rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Form --}}
        <div class="lg:col-span-2">
            <form action="{{ route('admin.email-accounts.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Basic Information --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400"></i>Basic Information
                        </h5>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <div>
                            <label class="block text-sm font-medium text-white mb-1">
                                Email Address <span class="text-red-400">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="cs@bizmark.id" required
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                            @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Use @bizmark.id domain</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white mb-1">
                                Display Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Customer Service" required
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white mb-1">
                                Account Type <span class="text-red-400">*</span>
                            </label>
                            <select name="type" required @change="updateTypeHelp($event.target.value)"
                                    class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                                <option value="">Select Type</option>
                                <option value="shared" {{ old('type') === 'shared' ? 'selected' : '' }}>Shared (Multiple Users)</option>
                                <option value="personal" {{ old('type') === 'personal' ? 'selected' : '' }}>Personal (Single User)</option>
                            </select>
                            @error('type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1" x-text="typeHelp"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white mb-1">
                                Department <span class="text-red-400">*</span>
                            </label>
                            <select name="department" required
                                    class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('department') border-red-500 @enderror">
                                <option value="">Select Department</option>
                                @foreach(['cs' => 'Customer Service', 'sales' => 'Sales', 'support' => 'Support', 'finance' => 'Finance', 'hr' => 'HR', 'it' => 'IT', 'marketing' => 'Marketing'] as $val => $label)
                                <option value="{{ $val }}" {{ old('department') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('department')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-white mb-1">Description</label>
                            <textarea name="description" rows="3" placeholder="Enter account description (optional)"
                                      class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                            @error('description')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Email Settings --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-cog text-gray-400"></i>Email Settings
                        </h5>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <div>
                            <label class="block text-sm font-medium text-white mb-1">Forward To (Optional)</label>
                            <input type="email" name="forward_to" value="{{ old('forward_to') }}" placeholder="forward@example.com"
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('forward_to')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Auto-forward all emails to this address</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white mb-1">Max Daily Emails</label>
                            <input type="number" name="max_daily_emails" value="{{ old('max_daily_emails', 100) }}" min="1"
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('max_daily_emails')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Maximum emails per day</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="flex items-center gap-3 cursor-pointer mb-3">
                                <input type="checkbox" name="auto_reply_enabled" x-model="autoReply"
                                       class="w-4 h-4 accent-blue-500">
                                <span class="text-white text-sm">Enable Auto-Reply</span>
                            </label>
                            <div x-show="autoReply" x-cloak>
                                <label class="block text-sm font-medium text-white mb-1">Auto-Reply Message</label>
                                <textarea name="auto_reply_message" rows="4"
                                          placeholder="Thank you for contacting us. We'll get back to you soon..."
                                          class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('auto_reply_message') }}</textarea>
                                @error('auto_reply_message')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Assign Users --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-users text-gray-400"></i>Assign Users
                        </h5>
                    </div>
                    <div class="p-5">
                        <div id="userAssignments" class="space-y-3"></div>
                        <button type="button" onclick="addUserAssignment()"
                            class="mt-3 inline-flex items-center px-3 py-1.5 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 text-sm rounded-lg transition">
                            <i class="fas fa-plus mr-1.5"></i>Add User
                        </button>
                        <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i>At least one primary handler required</p>
                    </div>
                </div>

                {{-- Status --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 accent-blue-500">
                        <span class="text-white text-sm">Active (Account can send/receive emails)</span>
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.email-accounts.index') }}"
                       class="px-4 py-2 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Create Email Account
                    </button>
                </div>
            </form>
        </div>

        {{-- Right: Help --}}
        <div>
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow sticky top-4">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-question-circle text-gray-400"></i>Help
                    </h5>
                </div>
                <div class="p-5 space-y-5 text-sm">
                    <div>
                        <p class="text-white font-medium flex items-center gap-2 mb-1">
                            <i class="fas fa-users text-green-400"></i>Shared Account
                        </p>
                        <p class="text-gray-400">Use for team emails like cs@, sales@, or support@. Multiple users can access and respond.</p>
                    </div>
                    <div>
                        <p class="text-white font-medium flex items-center gap-2 mb-1">
                            <i class="fas fa-user text-purple-400"></i>Personal Account
                        </p>
                        <p class="text-gray-400">Use for individual staff like john@bizmark.id. Only one user can be assigned.</p>
                    </div>
                    <div>
                        <p class="text-white font-medium flex items-center gap-2 mb-2">
                            <i class="fas fa-shield-alt text-blue-400"></i>User Roles
                        </p>
                        <ul class="text-gray-400 space-y-1 pl-4 list-disc">
                            <li><strong class="text-white">Primary:</strong> Main handler, full access</li>
                            <li><strong class="text-white">Backup:</strong> Can send/receive, limited delete</li>
                            <li><strong class="text-white">Viewer:</strong> Read-only access</li>
                        </ul>
                    </div>
                    <div class="flex items-start gap-2 bg-blue-500/10 border border-blue-500/30 text-blue-300 rounded-xl px-3 py-2.5 text-xs">
                        <i class="fas fa-lightbulb mt-0.5 flex-shrink-0"></i>
                        <span><strong>Tip:</strong> Configure Cloudflare Email Routing to point to your webhook URL for incoming emails.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let userIndex = 0;

function addUserAssignment() {
    const container = document.getElementById('userAssignments');
    const idx = userIndex;
    const row = document.createElement('div');
    row.id = `user-row-${idx}`;
    row.className = 'bg-gray-900 border border-gray-700 rounded-xl p-4';
    row.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-400 mb-1">User</label>
                <select name="assignments[${idx}][user_id]" required
                        class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select User</option>
                    @foreach($availableUsers ?? [] as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Role</label>
                <select name="assignments[${idx}][role]" required
                        class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="primary">Primary</option>
                    <option value="backup">Backup</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <label class="flex items-center gap-1.5 text-sm text-white cursor-pointer">
                    <input type="checkbox" name="assignments[${idx}][can_send]" value="1" checked class="w-4 h-4 accent-blue-500">Send
                </label>
                <label class="flex items-center gap-1.5 text-sm text-white cursor-pointer">
                    <input type="checkbox" name="assignments[${idx}][can_receive]" value="1" checked class="w-4 h-4 accent-blue-500">Receive
                </label>
                <label class="flex items-center gap-1.5 text-sm text-white cursor-pointer">
                    <input type="checkbox" name="assignments[${idx}][can_delete]" value="1" class="w-4 h-4 accent-blue-500">Delete
                </label>
                <button type="button" onclick="removeUserAssignment(${idx})"
                        class="ml-auto p-1.5 border border-red-700 text-red-400 hover:bg-red-900/30 rounded-lg text-xs transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(row);
    userIndex++;
}

function removeUserAssignment(idx) {
    document.getElementById(`user-row-${idx}`)?.remove();
}

document.addEventListener('DOMContentLoaded', () => addUserAssignment());
</script>
@endpush
@endsection
