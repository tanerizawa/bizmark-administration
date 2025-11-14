@extends('layouts.app')

@section('title', 'Email Accounts Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Header -->
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 text-white">
                        <i class="fas fa-at me-2"></i>Email Accounts
                    </h1>
                    <p class="text-dark-text-secondary mb-0">Manage company email accounts and user assignments</p>
                </div>
                <div>
                    <a href="{{ route('admin.email-accounts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>New Email Account
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="col-12 mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card card-elevated rounded-apple">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-dark-text-secondary mb-1 small">Total Accounts</p>
                                    <h3 class="text-white mb-0">{{ $stats['total'] ?? 0 }}</h3>
                                </div>
                                <div class="bg-apple-blue rounded-circle p-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-at text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-elevated rounded-apple">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-dark-text-secondary mb-1 small">Shared Accounts</p>
                                    <h3 class="text-white mb-0">{{ $stats['shared'] ?? 0 }}</h3>
                                </div>
                                <div class="bg-apple-green rounded-circle p-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-elevated rounded-apple">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-dark-text-secondary mb-1 small">Personal Accounts</p>
                                    <h3 class="text-white mb-0">{{ $stats['personal'] ?? 0 }}</h3>
                                </div>
                                <div class="bg-apple-purple rounded-circle p-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-elevated rounded-apple">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-dark-text-secondary mb-1 small">Active Users</p>
                                    <h3 class="text-white mb-0">{{ $stats['active_users'] ?? 0 }}</h3>
                                </div>
                                <div class="bg-apple-orange rounded-circle p-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-check text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="col-12 mb-4">
            <div class="card card-elevated rounded-apple">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.email-accounts.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control bg-dark text-white border-secondary" 
                                       placeholder="Search by email or name..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="type" class="form-select bg-dark text-white border-secondary" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Types</option>
                                    <option value="shared" {{ request('type') === 'shared' ? 'selected' : '' }}>Shared</option>
                                    <option value="personal" {{ request('type') === 'personal' ? 'selected' : '' }}>Personal</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="department" class="form-select bg-dark text-white border-secondary" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Departments</option>
                                    <option value="cs" {{ request('department') === 'cs' ? 'selected' : '' }}>Customer Service</option>
                                    <option value="sales" {{ request('department') === 'sales' ? 'selected' : '' }}>Sales</option>
                                    <option value="support" {{ request('department') === 'support' ? 'selected' : '' }}>Support</option>
                                    <option value="finance" {{ request('department') === 'finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="hr" {{ request('department') === 'hr' ? 'selected' : '' }}>HR</option>
                                    <option value="it" {{ request('department') === 'it' ? 'selected' : '' }}>IT</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select bg-dark text-white border-secondary" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Email Accounts Table -->
        <div class="col-12">
            <div class="card card-elevated rounded-apple">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--dark-separator);">
                                    <th class="px-4 py-3">Email Address</th>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Department</th>
                                    <th class="px-4 py-3">Assigned Users</th>
                                    <th class="px-4 py-3">Emails</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emailAccounts as $account)
                                <tr style="border-bottom: 1px solid var(--dark-separator);">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-apple-blue rounded-circle me-3" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-envelope text-white" style="font-size: 12px;"></i>
                                            </div>
                                            <div>
                                                <div class="text-white fw-medium">{{ $account->email }}</div>
                                                @if($account->forward_to)
                                                    <small class="text-dark-text-secondary">
                                                        <i class="fas fa-arrow-right me-1"></i>{{ $account->forward_to }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-white">{{ $account->name }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($account->type === 'shared')
                                            <span class="badge bg-apple-green">
                                                <i class="fas fa-users me-1"></i>Shared
                                            </span>
                                        @else
                                            <span class="badge bg-apple-purple">
                                                <i class="fas fa-user me-1"></i>Personal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge" style="background-color: var(--dark-bg-tertiary); color: var(--dark-text-secondary);">
                                            {{ ucfirst($account->department ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <span class="text-white me-2">{{ $account->users->count() }}</span>
                                            @if($account->users->count() > 0)
                                                <div class="d-flex" style="margin-left: 8px;">
                                                    @foreach($account->users->take(3) as $user)
                                                        <div class="rounded-circle bg-apple-blue text-white d-flex align-items-center justify-content-center" 
                                                             style="width: 24px; height: 24px; font-size: 10px; margin-left: -8px; border: 2px solid var(--dark-bg-secondary);"
                                                             title="{{ $user->name }}">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                    @endforeach
                                                    @if($account->users->count() > 3)
                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                             style="width: 24px; height: 24px; font-size: 10px; margin-left: -8px; border: 2px solid var(--dark-bg-secondary);">
                                                            +{{ $account->users->count() - 3 }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-white">
                                            <i class="fas fa-arrow-down text-apple-green me-1"></i>{{ $account->total_received ?? 0 }}
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-arrow-up text-apple-blue me-1"></i>{{ $account->total_sent ?? 0 }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($account->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.email-accounts.show', $account) }}" 
                                               class="btn btn-sm btn-outline-light" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.email-accounts.edit', $account) }}" 
                                               class="btn btn-sm btn-outline-light" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteAccount({{ $account->id }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-dark-text-secondary">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">No email accounts found</p>
                                            @if(request()->hasAny(['search', 'type', 'department', 'status']))
                                                <a href="{{ route('admin.email-accounts.index') }}" class="btn btn-sm btn-outline-light mt-3">
                                                    <i class="fas fa-times me-2"></i>Clear Filters
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($emailAccounts->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $emailAccounts->links() }}
            </div>
            @endif
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
                    <i class="fas fa-info-circle me-1"></i>This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete
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
</script>
@endpush
