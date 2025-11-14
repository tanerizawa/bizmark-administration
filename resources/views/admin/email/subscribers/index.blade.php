@extends('layouts.app')

@section('title', 'Email Subscribers')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-white">Email Subscribers</h1>
            <p class="text-muted">Kelola daftar subscriber newsletter</p>
        </div>
        <a href="{{ route('admin.subscribers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Subscriber
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-dark border-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Subscribers</h6>
                            <h3 class="mb-0 text-white">{{ number_format($stats['total']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active</h6>
                            <h3 class="mb-0 text-white">{{ number_format($stats['active']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                                <i class="fas fa-user-slash fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Unsubscribed</h6>
                            <h3 class="mb-0 text-white">{{ number_format($stats['unsubscribed']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Bounced</h6>
                            <h3 class="mb-0 text-white">{{ number_format($stats['bounced']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card bg-dark border-dark mb-4">
        <div class="card-body">
            <form action="{{ route('admin.subscribers.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control bg-dark text-white border-secondary" 
                           placeholder="Cari email atau nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select bg-dark text-white border-secondary">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                        <option value="bounced" {{ request('status') == 'bounced' ? 'selected' : '' }}>Bounced</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.subscribers.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Subscribers List -->
    <div class="card bg-dark border-dark">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th>Subscribed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.subscribers.show', $subscriber) }}" 
                                       class="text-white text-decoration-none">
                                        {{ $subscriber->email }}
                                    </a>
                                </td>
                                <td>{{ $subscriber->name ?? '-' }}</td>
                                <td>
                                    @if($subscriber->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($subscriber->status === 'unsubscribed')
                                        <span class="badge bg-warning">Unsubscribed</span>
                                    @elseif($subscriber->status === 'bounced')
                                        <span class="badge bg-danger">Bounced</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $subscriber->source ?? 'manual' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $subscriber->subscribed_at->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.subscribers.show', $subscriber) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.subscribers.edit', $subscriber) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.subscribers.destroy', $subscriber) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus subscriber ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>Belum ada subscriber</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($subscribers->hasPages())
                <div class="mt-4">
                    {{ $subscribers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
