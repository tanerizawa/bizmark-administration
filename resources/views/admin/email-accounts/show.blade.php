@extends('layouts.app')

@section('title', 'Email Account Details')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto"
     x-data="{
         assignOpen: false,
         editOpen: false,
         deleteOpen: false,
         unassignOpen: false,
         editPermForm: '',
         editRole: 'primary',
         editCanSend: true,
         editCanReceive: true,
         editCanDelete: false,
         editCanAssign: false,
         unassignForm: '',
         unassignName: '',
         deleteForm: '',
         openEditPerms(accountId, userId, role, canSend, canReceive, canDelete, canAssign) {
             this.editPermForm = `/admin/email-accounts/${accountId}/permissions/${userId}`;
             this.editRole = role;
             this.editCanSend = canSend;
             this.editCanReceive = canReceive;
             this.editCanDelete = canDelete;
             this.editCanAssign = canAssign;
             this.editOpen = true;
         },
         openUnassign(accountId, userId, name) {
             this.unassignForm = `/admin/email-accounts/${accountId}/unassign/${userId}`;
             this.unassignName = name;
             this.unassignOpen = true;
         },
         openDelete(id) {
             this.deleteForm = `/admin/email-accounts/${id}`;
             this.deleteOpen = true;
         }
     }">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-at text-blue-400"></i>{{ $emailAccount->email }}
            </h1>
            <p class="text-gray-400 mt-1">{{ $emailAccount->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.email-accounts.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 rounded-lg text-sm font-medium transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            <a href="{{ route('admin.email-accounts.edit', $emailAccount) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                <i class="fas fa-edit mr-2"></i>Edit Account
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Main --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Account Info --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-400"></i>Account Information
                    </h5>
                </div>
                <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-5">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Email Address</p>
                        <p class="text-white font-medium">{{ $emailAccount->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Display Name</p>
                        <p class="text-white font-medium">{{ $emailAccount->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Type</p>
                        @if($emailAccount->type === 'shared')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                <i class="fas fa-users"></i>Shared
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/20 text-purple-400">
                                <i class="fas fa-user"></i>Personal
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Department</p>
                        <p class="text-white">{{ ucfirst($emailAccount->department) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Status</p>
                        @if($emailAccount->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                <i class="fas fa-check-circle"></i>Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                                <i class="fas fa-times-circle"></i>Inactive
                            </span>
                        @endif
                    </div>
                    @if($emailAccount->description)
                    <div class="col-span-2 sm:col-span-3">
                        <p class="text-xs text-gray-400 mb-1">Description</p>
                        <p class="text-white">{{ $emailAccount->description }}</p>
                    </div>
                    @endif
                    @if($emailAccount->forward_to)
                    <div class="col-span-2 sm:col-span-3">
                        <p class="text-xs text-gray-400 mb-1">Forward To</p>
                        <p class="text-white flex items-center gap-2">
                            <i class="fas fa-arrow-right text-blue-400"></i>{{ $emailAccount->forward_to }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Assigned Users --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-users text-gray-400"></i>Assigned Users ({{ $emailAccount->users->count() }})
                    </h5>
                    <button @click="assignOpen = true"
                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                        <i class="fas fa-plus mr-1.5"></i>Assign User
                    </button>
                </div>
                @if($emailAccount->users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-gray-400 border-b border-gray-700">
                                <th class="px-5 py-3 font-medium">User</th>
                                <th class="px-5 py-3 font-medium">Role</th>
                                <th class="px-5 py-3 font-medium">Permissions</th>
                                <th class="px-5 py-3 font-medium">Assigned</th>
                                <th class="px-5 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($emailAccount->users as $user)
                            @php $assignment = $emailAccount->assignments->where('user_id', $user->id)->first(); @endphp
                            <tr class="hover:bg-gray-700/40 text-gray-300">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ $user->name }}</p>
                                            <p class="text-gray-400 text-xs">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    @if($assignment->role === 'primary')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-blue-500/20 text-blue-400">
                                            <i class="fas fa-star"></i>Primary
                                        </span>
                                    @elseif($assignment->role === 'backup')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-orange-500/20 text-orange-400">
                                            <i class="fas fa-user-shield"></i>Backup
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-gray-500/20 text-gray-400">
                                            <i class="fas fa-eye"></i>Viewer
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @if($assignment->can_send)
                                            <span class="px-1.5 py-0.5 rounded text-xs bg-green-500/20 text-green-400"><i class="fas fa-paper-plane mr-1"></i>Send</span>
                                        @endif
                                        @if($assignment->can_receive)
                                            <span class="px-1.5 py-0.5 rounded text-xs bg-cyan-500/20 text-cyan-400"><i class="fas fa-inbox mr-1"></i>Receive</span>
                                        @endif
                                        @if($assignment->can_delete)
                                            <span class="px-1.5 py-0.5 rounded text-xs bg-red-500/20 text-red-400"><i class="fas fa-trash mr-1"></i>Delete</span>
                                        @endif
                                        @if($assignment->can_assign_others)
                                            <span class="px-1.5 py-0.5 rounded text-xs bg-yellow-500/20 text-yellow-400"><i class="fas fa-user-plus mr-1"></i>Assign</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-400 text-xs">{{ $assignment->created_at->diffForHumans() }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                            @click="openEditPerms({{ $emailAccount->id }}, {{ $user->id }}, '{{ $assignment->role }}', {{ $assignment->can_send ? 'true' : 'false' }}, {{ $assignment->can_receive ? 'true' : 'false' }}, {{ $assignment->can_delete ? 'true' : 'false' }}, {{ $assignment->can_assign_others ? 'true' : 'false' }})"
                                            class="p-1.5 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 rounded-lg text-xs transition">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button"
                                            @click="openUnassign({{ $emailAccount->id }}, {{ $user->id }}, '{{ $user->name }}')"
                                            class="p-1.5 border border-red-700 text-red-400 hover:bg-red-900/30 rounded-lg text-xs transition">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-10">
                    <i class="fas fa-users text-3xl text-gray-600 mb-3"></i>
                    <p class="text-gray-400 mb-4">No users assigned yet</p>
                    <button @click="assignOpen = true"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                        <i class="fas fa-plus mr-2"></i>Assign First User
                    </button>
                </div>
                @endif
            </div>

            {{-- Recent Emails --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-envelope text-gray-400"></i>Recent Emails
                    </h5>
                    <a href="{{ route('admin.inbox.index', ['email_account_id' => $emailAccount->id]) }}"
                       class="text-sm text-gray-400 hover:text-white transition">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-5">
                    @if($recentEmails->count() > 0)
                    <div class="space-y-2">
                        @foreach($recentEmails as $email)
                        <a href="{{ route('admin.inbox.show', $email) }}"
                           class="flex items-start justify-between p-3 rounded-lg border border-gray-700 hover:border-gray-500 hover:bg-gray-700/40 transition">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h6 class="text-white font-medium truncate">{{ $email->subject }}</h6>
                                    @if($email->priority)
                                    <span class="px-1.5 py-0.5 rounded text-xs" style="background-color: {{ $email->priority_color }};">
                                        {{ ucfirst($email->priority) }}
                                    </span>
                                    @endif
                                </div>
                                <p class="text-gray-400 text-sm">From: {{ $email->from_email }}</p>
                            </div>
                            <span class="text-xs text-gray-500 ml-3 flex-shrink-0">{{ $email->received_at->diffForHumans() }}</span>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6 text-gray-400">
                        <i class="fas fa-inbox mr-2"></i>No emails yet
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Stats + Settings + Danger --}}
        <div class="space-y-5">

            {{-- Statistics --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-chart-bar text-gray-400"></i>Statistics
                    </h5>
                </div>
                <div class="p-5 space-y-5">
                    @php
                        $stats = [
                            ['label' => 'Total Received', 'value' => $emailAccount->total_received ?? 0, 'pct' => 100, 'color' => 'bg-green-500'],
                            ['label' => 'Total Sent', 'value' => $emailAccount->total_sent ?? 0,
                             'pct' => $emailAccount->total_received > 0 ? min(($emailAccount->total_sent / max($emailAccount->total_received, 1)) * 100, 100) : 0,
                             'color' => 'bg-blue-500'],
                            ['label' => 'Unread', 'value' => $emailAccount->getUnreadCount(),
                             'pct' => $emailAccount->total_received > 0 ? ($emailAccount->getUnreadCount() / $emailAccount->total_received) * 100 : 0,
                             'color' => 'bg-orange-500'],
                            ['label' => "Today's Emails", 'value' => $emailAccount->getTodayEmailCount(),
                             'pct' => ($emailAccount->max_daily_emails ?? 100) > 0 ? ($emailAccount->getTodayEmailCount() / ($emailAccount->max_daily_emails ?? 100)) * 100 : 0,
                             'color' => 'bg-purple-500'],
                        ];
                    @endphp
                    @foreach($stats as $stat)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm text-gray-400">{{ $stat['label'] }}</span>
                            <span class="text-white font-bold">{{ $stat['value'] }}</span>
                        </div>
                        <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                            <div class="{{ $stat['color'] }} h-full rounded-full" style="width: {{ round($stat['pct']) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                    <p class="text-xs text-gray-500">Limit: {{ $emailAccount->max_daily_emails ?? 100 }}/day</p>
                </div>
            </div>

            {{-- Settings --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-cog text-gray-400"></i>Settings
                    </h5>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-white">Auto-Reply</span>
                        @if($emailAccount->auto_reply_enabled)
                            <span class="px-2 py-0.5 rounded text-xs bg-green-500/20 text-green-400">Enabled</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs bg-gray-500/20 text-gray-400">Disabled</span>
                        @endif
                    </div>
                    @if($emailAccount->auto_reply_enabled && $emailAccount->auto_reply_message)
                    <div class="bg-gray-900 rounded-lg p-3">
                        <p class="text-xs text-gray-400">{{ Str::limit($emailAccount->auto_reply_message, 100) }}</p>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-white">Max Daily Emails</span>
                        <span class="text-gray-400">{{ $emailAccount->max_daily_emails ?? 100 }}</span>
                    </div>
                    @if($emailAccount->signature)
                    <div>
                        <p class="text-white mb-2">Email Signature</p>
                        <div class="bg-gray-900 rounded-lg p-3">
                            <p class="text-xs text-gray-400">{!! nl2br(e(Str::limit($emailAccount->signature, 100))) !!}</p>
                        </div>
                    </div>
                    @endif
                    <div class="pt-2 border-t border-gray-700 space-y-1">
                        <p class="text-xs text-gray-500"><i class="fas fa-calendar mr-2"></i>Created: {{ $emailAccount->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500"><i class="fas fa-clock mr-2"></i>Updated: {{ $emailAccount->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-gray-800 border border-red-900/60 rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-red-900/60">
                    <h5 class="text-red-400 font-semibold flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i>Danger Zone
                    </h5>
                </div>
                <div class="p-5">
                    <p class="text-white text-sm mb-4">Delete this email account permanently. This action cannot be undone.</p>
                    <button type="button" @click="openDelete({{ $emailAccount->id }})"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-trash mr-2"></i>Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Assign User --}}
    <div x-show="assignOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
         @keydown.escape.window="assignOpen = false">
        <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-md" @click.outside="assignOpen = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
                <h5 class="text-white font-semibold flex items-center gap-2"><i class="fas fa-user-plus text-gray-400"></i>Assign User</h5>
                <button @click="assignOpen = false" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>
            <form action="{{ route('admin.email-accounts.assign', $emailAccount) }}" method="POST">
                @csrf
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Select User</label>
                        <select name="user_id" required class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Choose a user...</option>
                            @foreach($availableUsers ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Role</label>
                        <select name="role" required class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="primary">Primary Handler</option>
                            <option value="backup">Backup Handler</option>
                            <option value="viewer">Viewer Only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Permissions</label>
                        <div class="space-y-2">
                            @foreach(['can_send' => 'Can Send Emails', 'can_receive' => 'Can Receive Emails', 'can_delete' => 'Can Delete Emails', 'can_assign_others' => 'Can Assign Others'] as $name => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="{{ $name }}" value="1" {{ in_array($name, ['can_send','can_receive']) ? 'checked' : '' }}
                                    class="w-4 h-4 accent-blue-500">
                                <span class="text-white text-sm">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-700">
                    <button type="button" @click="assignOpen = false"
                        class="px-4 py-2 border border-gray-600 text-gray-300 hover:text-white rounded-lg text-sm transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-save mr-1.5"></i>Assign User
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Edit Permissions --}}
    <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
         @keydown.escape.window="editOpen = false">
        <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-md" @click.outside="editOpen = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
                <h5 class="text-white font-semibold flex items-center gap-2"><i class="fas fa-edit text-gray-400"></i>Edit Permissions</h5>
                <button @click="editOpen = false" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>
            <form :action="editPermForm" method="POST">
                @csrf @method('PATCH')
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Role</label>
                        <select name="role" x-model="editRole" required class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="primary">Primary Handler</option>
                            <option value="backup">Backup Handler</option>
                            <option value="viewer">Viewer Only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Permissions</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="can_send" value="1" x-model="editCanSend" class="w-4 h-4 accent-blue-500">
                                <span class="text-white text-sm">Can Send Emails</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="can_receive" value="1" x-model="editCanReceive" class="w-4 h-4 accent-blue-500">
                                <span class="text-white text-sm">Can Receive Emails</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="can_delete" value="1" x-model="editCanDelete" class="w-4 h-4 accent-blue-500">
                                <span class="text-white text-sm">Can Delete Emails</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="can_assign_others" value="1" x-model="editCanAssign" class="w-4 h-4 accent-blue-500">
                                <span class="text-white text-sm">Can Assign Others</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-700">
                    <button type="button" @click="editOpen = false"
                        class="px-4 py-2 border border-gray-600 text-gray-300 hover:text-white rounded-lg text-sm transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-save mr-1.5"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Delete Confirmation --}}
    <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
         @keydown.escape.window="deleteOpen = false">
        <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-md" @click.outside="deleteOpen = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
                <h5 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>Confirm Delete
                </h5>
                <button @click="deleteOpen = false" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>
            <div class="p-5">
                <p class="text-white mb-3">Are you sure you want to delete this email account?</p>
                <p class="text-red-400 text-sm"><i class="fas fa-info-circle mr-1"></i>This action cannot be undone. All email history and assignments will be removed.</p>
            </div>
            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-700">
                <button type="button" @click="deleteOpen = false"
                    class="px-4 py-2 border border-gray-600 text-gray-300 hover:text-white rounded-lg text-sm transition">Cancel</button>
                <form :action="deleteForm" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-trash mr-1.5"></i>Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Unassign User --}}
    <div x-show="unassignOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
         @keydown.escape.window="unassignOpen = false">
        <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-md" @click.outside="unassignOpen = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
                <h5 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-user-times text-yellow-400"></i>Remove User
                </h5>
                <button @click="unassignOpen = false" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>
            <div class="p-5">
                <p class="text-white mb-3">Are you sure you want to remove <strong x-text="unassignName"></strong> from this email account?</p>
                <p class="text-yellow-400 text-sm"><i class="fas fa-info-circle mr-1"></i>The user will lose access to all emails in this account.</p>
            </div>
            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-700">
                <button type="button" @click="unassignOpen = false"
                    class="px-4 py-2 border border-gray-600 text-gray-300 hover:text-white rounded-lg text-sm transition">Cancel</button>
                <form :action="unassignForm" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg text-sm font-medium transition">
                        <i class="fas fa-user-times mr-1.5"></i>Remove User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
