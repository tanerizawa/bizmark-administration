@extends('layouts.app')

@section('title', 'Email Account Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Header -->
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 text-white">
                        <i class="fas fa-at me-2"></i>{{ $emailAccount->email }}
                    </h1>
                    <p class="text-dark-text-secondary mb-0">{{ $emailAccount->name }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.email-accounts.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="{{ route('admin.email-accounts.edit', $emailAccount) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Account
                    </a>
                </div>
            </div>
        </div>

        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Account Info Card -->
            <div class="card card-elevated rounded-apple mb-4">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-info-circle me-2"></i>Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-dark-text-secondary small">Email Address</label>
                            <div class="text-white fw-medium">{{ $emailAccount->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-dark-text-secondary small">Display Name</label>
                            <div class="text-white fw-medium">{{ $emailAccount->name }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark-text-secondary small">Type</label>
                            <div>
                                @if($emailAccount->type === 'shared')
                                    <span class="badge bg-apple-green">
                                        <i class="fas fa-users me-1"></i>Shared
                                    </span>
                                @else
                                    <span class="badge bg-apple-purple">
                                        <i class="fas fa-user me-1"></i>Personal
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark-text-secondary small">Department</label>
                            <div class="text-white">{{ ucfirst($emailAccount->department) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark-text-secondary small">Status</label>
                            <div>
                                @if($emailAccount->is_active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($emailAccount->description)
                        <div class="col-12">
                            <label class="text-dark-text-secondary small">Description</label>
                            <div class="text-white">{{ $emailAccount->description }}</div>
                        </div>
                        @endif
                        @if($emailAccount->forward_to)
                        <div class="col-12">
                            <label class="text-dark-text-secondary small">Forward To</label>
                            <div class="text-white">
                                <i class="fas fa-arrow-right text-apple-blue me-2"></i>{{ $emailAccount->forward_to }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Assigned Users Card -->
            <div class="card card-elevated rounded-apple mb-4">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-users me-2"></i>Assigned Users ({{ $emailAccount->users->count() }})
                        </h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                            <i class="fas fa-plus me-2"></i>Assign User
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($emailAccount->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--dark-separator);">
                                    <th class="px-4 py-3">User</th>
                                    <th class="px-4 py-3">Role</th>
                                    <th class="px-4 py-3">Permissions</th>
                                    <th class="px-4 py-3">Assigned Date</th>
                                    <th class="px-4 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emailAccount->users as $user)
                                @php
                                    $assignment = $emailAccount->assignments->where('user_id', $user->id)->first();
                                @endphp
                                <tr style="border-bottom: 1px solid var(--dark-separator);">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-apple-blue rounded-circle text-white d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-white fw-medium">{{ $user->name }}</div>
                                                <small class="text-dark-text-secondary">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($assignment->role === 'primary')
                                            <span class="badge bg-apple-blue">
                                                <i class="fas fa-star me-1"></i>Primary
                                            </span>
                                        @elseif($assignment->role === 'backup')
                                            <span class="badge bg-apple-orange">
                                                <i class="fas fa-user-shield me-1"></i>Backup
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-eye me-1"></i>Viewer
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex gap-2">
                                            @if($assignment->can_send)
                                                <span class="badge bg-success" style="font-size: 10px;">
                                                    <i class="fas fa-paper-plane"></i> Send
                                                </span>
                                            @endif
                                            @if($assignment->can_receive)
                                                <span class="badge bg-info" style="font-size: 10px;">
                                                    <i class="fas fa-inbox"></i> Receive
                                                </span>
                                            @endif
                                            @if($assignment->can_delete)
                                                <span class="badge bg-danger" style="font-size: 10px;">
                                                    <i class="fas fa-trash"></i> Delete
                                                </span>
                                            @endif
                                            @if($assignment->can_assign_others)
                                                <span class="badge bg-warning" style="font-size: 10px;">
                                                    <i class="fas fa-user-plus"></i> Assign
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-dark-text-secondary">
                                            {{ $assignment->created_at->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-light" 
                                                    onclick="editPermissions({{ $emailAccount->id }}, {{ $user->id }}, '{{ $assignment->role }}', {{ $assignment->can_send ? 'true' : 'false' }}, {{ $assignment->can_receive ? 'true' : 'false' }}, {{ $assignment->can_delete ? 'true' : 'false' }}, {{ $assignment->can_assign_others ? 'true' : 'false' }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="unassignUser({{ $emailAccount->id }}, {{ $user->id }}, '{{ $user->name }}')">
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
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-dark-text-secondary opacity-50 mb-3"></i>
                        <p class="text-dark-text-secondary mb-0">No users assigned yet</p>
                        <button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                            <i class="fas fa-plus me-2"></i>Assign First User
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Emails Card -->
            <div class="card card-elevated rounded-apple mb-4">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-envelope me-2"></i>Recent Emails
                        </h5>
                        <a href="{{ route('admin.inbox.index', ['email_account_id' => $emailAccount->id]) }}" class="btn btn-sm btn-outline-light">
                            View All <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentEmails->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentEmails as $email)
                        <a href="{{ route('admin.inbox.show', $email) }}" class="list-group-item list-group-item-action bg-transparent text-white border-secondary">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="mb-0">{{ $email->subject }}</h6>
                                        @if($email->priority)
                                            <span class="badge badge-sm" style="background-color: {{ $email->priority_color }};">
                                                {{ ucfirst($email->priority) }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mb-1 text-dark-text-secondary small">
                                        From: {{ $email->from_email }}
                                    </p>
                                </div>
                                <small class="text-dark-text-secondary">{{ $email->received_at->diffForHumans() }}</small>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-3 text-dark-text-secondary">
                        <i class="fas fa-inbox me-2"></i>No emails yet
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Stats -->
        <div class="col-lg-4">
            <!-- Statistics Card -->
            <div class="card card-elevated rounded-apple mb-4">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-chart-bar me-2"></i>Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-dark-text-secondary">Total Received</span>
                            <span class="text-white fw-bold">{{ $emailAccount->total_received ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px; background-color: var(--dark-bg-tertiary);">
                            <div class="progress-bar bg-apple-green" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-dark-text-secondary">Total Sent</span>
                            <span class="text-white fw-bold">{{ $emailAccount->total_sent ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px; background-color: var(--dark-bg-tertiary);">
                            <div class="progress-bar bg-apple-blue" style="width: {{ $emailAccount->total_sent > 0 ? min(($emailAccount->total_sent / max($emailAccount->total_received, 1)) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-dark-text-secondary">Unread Emails</span>
                            <span class="text-white fw-bold">{{ $emailAccount->getUnreadCount() }}</span>
                        </div>
                        <div class="progress" style="height: 8px; background-color: var(--dark-bg-tertiary);">
                            <div class="progress-bar bg-apple-orange" style="width: {{ $emailAccount->total_received > 0 ? ($emailAccount->getUnreadCount() / $emailAccount->total_received) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-dark-text-secondary">Today's Emails</span>
                            <span class="text-white fw-bold">{{ $emailAccount->getTodayEmailCount() }}</span>
                        </div>
                        <div class="progress" style="height: 8px; background-color: var(--dark-bg-tertiary);">
                            <div class="progress-bar bg-apple-purple" style="width: {{ $emailAccount->max_daily_emails > 0 ? ($emailAccount->getTodayEmailCount() / $emailAccount->max_daily_emails) * 100 : 0 }}%"></div>
                        </div>
                        <small class="text-dark-text-secondary">Limit: {{ $emailAccount->max_daily_emails ?? 100 }}/day</small>
                    </div>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="card card-elevated rounded-apple mb-4">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-cog me-2"></i>Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white">Auto-Reply</span>
                            @if($emailAccount->auto_reply_enabled)
                                <span class="badge bg-success">Enabled</span>
                            @else
                                <span class="badge bg-secondary">Disabled</span>
                            @endif
                        </div>
                        @if($emailAccount->auto_reply_enabled && $emailAccount->auto_reply_message)
                            <div class="mt-2 p-2 rounded" style="background-color: var(--dark-bg-tertiary);">
                                <small class="text-dark-text-secondary">{{ Str::limit($emailAccount->auto_reply_message, 100) }}</small>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white">Max Daily Emails</span>
                            <span class="text-dark-text-secondary">{{ $emailAccount->max_daily_emails ?? 100 }}</span>
                        </div>
                    </div>

                    @if($emailAccount->signature)
                    <div class="mb-3">
                        <div class="text-white mb-2">Email Signature</div>
                        <div class="p-2 rounded" style="background-color: var(--dark-bg-tertiary);">
                            <small class="text-dark-text-secondary">{!! nl2br(e(Str::limit($emailAccount->signature, 100))) !!}</small>
                        </div>
                    </div>
                    @endif

                    <div class="mt-4">
                        <div class="text-dark-text-secondary small mb-2">
                            <i class="fas fa-calendar me-2"></i>Created: {{ $emailAccount->created_at->format('M d, Y') }}
                        </div>
                        <div class="text-dark-text-secondary small">
                            <i class="fas fa-clock me-2"></i>Updated: {{ $emailAccount->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone Card -->
            <div class="card card-elevated rounded-apple border-danger">
                <div class="card-header bg-transparent border-bottom border-danger">
                    <h5 class="mb-0 text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-white mb-3">Delete this email account permanently. This action cannot be undone.</p>
                    <button type="button" class="btn btn-danger w-100" onclick="deleteAccount({{ $emailAccount->id }})">
                        <i class="fas fa-trash me-2"></i>Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign User Modal -->
<div class="modal fade" id="assignUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user-plus me-2"></i>Assign User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.email-accounts.assign', $emailAccount) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Select User</label>
                        <select name="user_id" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">Choose a user...</option>
                            @foreach($availableUsers ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Role</label>
                        <select name="role" class="form-select bg-dark text-white border-secondary" required>
                            <option value="primary">Primary Handler</option>
                            <option value="backup">Backup Handler</option>
                            <option value="viewer">Viewer Only</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Permissions</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="can_send" id="canSend" value="1" checked>
                            <label class="form-check-label text-white" for="canSend">Can Send Emails</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="can_receive" id="canReceive" value="1" checked>
                            <label class="form-check-label text-white" for="canReceive">Can Receive Emails</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="can_delete" id="canDelete" value="1">
                            <label class="form-check-label text-white" for="canDelete">Can Delete Emails</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="can_assign_others" id="canAssign" value="1">
                            <label class="form-check-label text-white" for="canAssign">Can Assign Others</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Assign User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Permissions Modal -->
<div class="modal fade" id="editPermissionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-edit me-2"></i>Edit Permissions
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPermissionsForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Role</label>
                        <select name="role" id="editRole" class="form-select bg-dark text-white border-secondary" required>
                            <option value="primary">Primary Handler</option>
                            <option value="backup">Backup Handler</option>
                            <option value="viewer">Viewer Only</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Permissions</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="can_send" id="editCanSend" value="1">
                            <label class="form-check-label text-white" for="editCanSend">Can Send Emails</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="can_receive" id="editCanReceive" value="1">
                            <label class="form-check-label text-white" for="editCanReceive">Can Receive Emails</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="can_delete" id="editCanDelete" value="1">
                            <label class="form-check-label text-white" for="editCanDelete">Can Delete Emails</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="can_assign_others" id="editCanAssign" value="1">
                            <label class="form-check-label text-white" for="editCanAssign">Can Assign Others</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-white">
                <p>Are you sure you want to delete this email account?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-info-circle me-1"></i>This action cannot be undone. All email history and assignments will be removed.
                </p>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Unassign User Modal -->
<div class="modal fade" id="unassignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user-times text-warning me-2"></i>Remove User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-white">
                <p>Are you sure you want to remove <strong id="unassignUserName"></strong> from this email account?</p>
                <p class="text-warning mb-0">
                    <i class="fas fa-info-circle me-1"></i>The user will lose access to all emails in this account.
                </p>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="unassignForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-user-times me-2"></i>Remove User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function deleteAccount(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/email-accounts/${id}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function editPermissions(accountId, userId, role, canSend, canReceive, canDelete, canAssign) {
    const form = document.getElementById('editPermissionsForm');
    form.action = `/admin/email-accounts/${accountId}/permissions/${userId}`;
    
    document.getElementById('editRole').value = role;
    document.getElementById('editCanSend').checked = canSend;
    document.getElementById('editCanReceive').checked = canReceive;
    document.getElementById('editCanDelete').checked = canDelete;
    document.getElementById('editCanAssign').checked = canAssign;
    
    const modal = new bootstrap.Modal(document.getElementById('editPermissionsModal'));
    modal.show();
}

function unassignUser(accountId, userId, userName) {
    const form = document.getElementById('unassignForm');
    form.action = `/admin/email-accounts/${accountId}/unassign/${userId}`;
    document.getElementById('unassignUserName').textContent = userName;
    
    const modal = new bootstrap.Modal(document.getElementById('unassignModal'));
    modal.show();
}
</script>
@endpush
