@extends('layouts.app')

@section('title', 'Email Campaigns')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-white">Email Campaigns</h1>
            <p class="text-muted">Kelola dan kirim email marketing campaign</p>
        </div>
        <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Buat Campaign Baru
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
                                <i class="fas fa-paper-plane fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Campaigns</h6>
                            <h3 class="mb-0 text-white">{{ $stats['total'] }}</h3>
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
                                <i class="fas fa-edit fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Draft</h6>
                            <h3 class="mb-0 text-white">{{ $stats['draft'] }}</h3>
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
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Scheduled</h6>
                            <h3 class="mb-0 text-white">{{ $stats['scheduled'] }}</h3>
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
                            <h6 class="text-muted mb-1">Sent</h6>
                            <h3 class="mb-0 text-white">{{ $stats['sent'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card bg-dark border-dark mb-4">
        <div class="card-body">
            <form action="{{ route('admin.campaigns.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control bg-dark text-white border-secondary" 
                           placeholder="Cari campaign..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select bg-dark text-white border-secondary">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="sending" {{ request('status') == 'sending' ? 'selected' : '' }}>Sending</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="card bg-dark border-dark">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Status</th>
                            <th>Recipients</th>
                            <th>Sent</th>
                            <th>Opened</th>
                            <th>Clicked</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $campaign)
                            <tr>
                                <td>
                                    <div>
                                        <a href="{{ route('admin.campaigns.show', $campaign) }}" 
                                           class="text-white fw-bold text-decoration-none">
                                            {{ $campaign->name }}
                                        </a>
                                        <div class="text-muted small">{{ $campaign->subject }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($campaign->status === 'draft')
                                        <span class="badge bg-warning">Draft</span>
                                    @elseif($campaign->status === 'scheduled')
                                        <span class="badge bg-info">Scheduled</span>
                                    @elseif($campaign->status === 'sending')
                                        <span class="badge bg-primary">Sending</span>
                                    @elseif($campaign->status === 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @endif
                                </td>
                                <td>{{ number_format($campaign->total_recipients) }}</td>
                                <td>{{ number_format($campaign->sent_count) }}</td>
                                <td>
                                    {{ number_format($campaign->opened_count) }}
                                    <span class="text-success small">({{ $campaign->open_rate }}%)</span>
                                </td>
                                <td>
                                    {{ number_format($campaign->clicked_count) }}
                                    <span class="text-info small">({{ $campaign->click_rate }}%)</span>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $campaign->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.campaigns.show', $campaign) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($campaign->status === 'draft')
                                            <a href="{{ route('admin.campaigns.edit', $campaign) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.campaigns.send', $campaign) }}" 
                                               class="btn btn-sm btn-success" title="Send">
                                                <i class="fas fa-paper-plane"></i>
                                            </a>
                                        @endif
                                        @if($campaign->status !== 'sending')
                                            <form action="{{ route('admin.campaigns.destroy', $campaign) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus campaign ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Belum ada campaign. Buat campaign pertama Anda!</p>
                                    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Buat Campaign
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($campaigns->hasPages())
                <div class="mt-4">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
