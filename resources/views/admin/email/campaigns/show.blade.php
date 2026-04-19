@extends('layouts.app')

@section('title', 'Campaign Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">
                <i class="fas fa-paper-plane me-2"></i>{{ $campaign->name }}
            </h1>
            <p class="text-muted">
                <span class="badge 
                    @if($campaign->status === 'draft') bg-warning
                    @elseif($campaign->status === 'scheduled') bg-info
                    @elseif($campaign->status === 'sending') bg-primary
                    @elseif($campaign->status === 'sent') bg-success
                    @else bg-secondary
                    @endif">
                    {{ ucfirst($campaign->status) }}
                </span>
                @if($campaign->sent_at)
                    • Sent {{ $campaign->sent_at->diffForHumans() }}
                @elseif($campaign->scheduled_at)
                    • Scheduled for {{ $campaign->scheduled_at->format('d M Y, H:i') }}
                @endif
            </p>
        </div>
        <div>
            @if($campaign->status === 'draft')
            <a href="{{ route('admin.campaigns.edit', $campaign->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            @endif
            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column - Campaign Details -->
        <div class="col-lg-8">
            <!-- Statistics (if sent) -->
            @if($campaign->status === 'sent' || $campaign->status === 'sending')
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-dark border-dark shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-paper-plane fs-3 text-primary mb-2"></i>
                            <h3 class="mb-0 text-white">{{ number_format($campaign->sent_count) }}</h3>
                            <small class="text-muted">Sent</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark border-dark shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-envelope-open fs-3 text-success mb-2"></i>
                            <h3 class="mb-0 text-white">{{ number_format($campaign->opened_count) }}</h3>
                            <small class="text-muted">Opened ({{ number_format($campaign->open_rate, 1) }}%)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark border-dark shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-mouse-pointer fs-3 text-info mb-2"></i>
                            <h3 class="mb-0 text-white">{{ number_format($campaign->clicked_count) }}</h3>
                            <small class="text-muted">Clicked ({{ number_format($campaign->click_rate, 1) }}%)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark border-dark shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle fs-3 text-danger mb-2"></i>
                            <h3 class="mb-0 text-white">{{ number_format($campaign->bounced_count) }}</h3>
                            <small class="text-muted">Bounced ({{ number_format($campaign->bounce_rate, 1) }}%)</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Email Content Preview -->
            <div class="card bg-dark border-dark shadow-sm mb-3">
                <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-envelope me-2"></i>Email Content
                    </h5>
                    <button class="btn btn-sm btn-outline-info" onclick="openFullPreview()">
                        <i class="fas fa-expand me-1"></i>Full Preview
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="text-white">Subject:</strong>
                        <p class="text-muted mb-0">{{ $campaign->subject }}</p>
                    </div>
                    <hr class="border-secondary">
                    <div style="max-height: 500px; overflow-y: auto; background: white; padding: 20px; border-radius: 4px;">
                        {!! $campaign->content !!}
                    </div>
                </div>
            </div>

            <!-- Email Logs (if sent) -->
            @if($campaign->status === 'sent' && $campaign->emailLogs->count() > 0)
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-list me-2"></i>Email Delivery Log
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Recipient</th>
                                    <th>Status</th>
                                    <th>Sent</th>
                                    <th>Opened</th>
                                    <th>Clicked</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campaign->emailLogs()->limit(50)->get() as $log)
                                <tr>
                                    <td>{{ $log->recipient_email }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($log->status === 'sent') bg-secondary
                                            @elseif($log->status === 'delivered') bg-info
                                            @elseif($log->status === 'opened') bg-success
                                            @elseif($log->status === 'clicked') bg-primary
                                            @elseif($log->status === 'bounced') bg-danger
                                            @else bg-warning
                                            @endif">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->sent_at->format('d M, H:i') }}</td>
                                    <td>{{ $log->opened_at ? $log->opened_at->format('d M, H:i') : '-' }}</td>
                                    <td>{{ $log->clicked_at ? $log->clicked_at->format('d M, H:i') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($campaign->emailLogs->count() > 50)
                    <p class="text-muted text-center mb-0">
                        Showing first 50 of {{ number_format($campaign->emailLogs->count()) }} emails
                    </p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Campaign Info & Actions -->
        <div class="col-lg-4">
            <!-- Campaign Information -->
            <div class="card bg-dark border-dark shadow-sm mb-3">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-info-circle me-2"></i>Campaign Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge 
                            @if($campaign->status === 'draft') bg-warning
                            @elseif($campaign->status === 'scheduled') bg-info
                            @elseif($campaign->status === 'sending') bg-primary
                            @elseif($campaign->status === 'sent') bg-success
                            @else bg-secondary
                            @endif fs-6">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Recipients</small>
                        <p class="text-white mb-0">
                            {{ ucfirst($campaign->recipient_type) }}
                            @if($campaign->recipient_type === 'tags' && $campaign->recipient_tags)
                                <br><small class="text-muted">Tags: {{ implode(', ', $campaign->recipient_tags) }}</small>
                            @endif
                        </p>
                    </div>

                    @if($campaign->template)
                    <div class="mb-3">
                        <small class="text-muted d-block">Template</small>
                        <p class="text-white mb-0">{{ $campaign->template->name }}</p>
                    </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted d-block">Created</small>
                        <p class="text-white mb-0">{{ $campaign->created_at->format('d M Y, H:i') }}</p>
                        <small class="text-muted">{{ $campaign->created_at->diffForHumans() }}</small>
                    </div>

                    @if($campaign->scheduled_at)
                    <div class="mb-3">
                        <small class="text-muted d-block">Scheduled For</small>
                        <p class="text-white mb-0">{{ $campaign->scheduled_at->format('d M Y, H:i') }}</p>
                        <small class="text-muted">{{ $campaign->scheduled_at->diffForHumans() }}</small>
                    </div>
                    @endif

                    @if($campaign->sent_at)
                    <div class="mb-3">
                        <small class="text-muted d-block">Sent At</small>
                        <p class="text-white mb-0">{{ $campaign->sent_at->format('d M Y, H:i') }}</p>
                        <small class="text-muted">{{ $campaign->sent_at->diffForHumans() }}</small>
                    </div>
                    @endif

                    @if($campaign->creator)
                    <div class="mb-0">
                        <small class="text-muted d-block">Created By</small>
                        <p class="text-white mb-0">{{ $campaign->creator->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($campaign->status === 'draft')
                        <form action="{{ route('admin.campaigns.send', $campaign->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to send this campaign now?')">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Now
                            </button>
                        </form>
                        <a href="{{ route('admin.campaigns.edit', $campaign->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Campaign
                        </a>
                        @elseif($campaign->status === 'scheduled')
                        <form action="{{ route('admin.campaigns.cancel', $campaign->id) }}" method="POST" onsubmit="return confirm('Cancel this scheduled campaign?')">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-times me-2"></i>Cancel Schedule
                            </button>
                        </form>
                        @endif

                        @if($campaign->status === 'sent')
                        <button class="btn btn-outline-info" onclick="exportReport()">
                            <i class="fas fa-download me-2"></i>Export Report
                        </button>
                        @endif

                        @if($campaign->status === 'draft' || $campaign->status === 'cancelled')
                        <form action="{{ route('admin.campaigns.destroy', $campaign->id) }}" method="POST" onsubmit="return confirm('Delete this campaign? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i>Delete Campaign
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full Preview Modal -->
<div class="modal fade" id="fullPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark border-secondary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-envelope me-2"></i>Email Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="bg-light p-3 border-bottom">
                    <strong>Subject:</strong> {{ $campaign->subject }}
                </div>
                <div class="p-4" style="background: white;">
                    {!! $campaign->content !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openFullPreview() {
    const modal = new bootstrap.Modal(document.getElementById('fullPreviewModal'));
    modal.show();
}

function exportReport() {
    // Placeholder for export functionality
    alert('Export functionality will be implemented soon');
}
</script>
@endsection
