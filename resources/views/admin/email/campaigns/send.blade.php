@extends('layouts.app')

@section('title', 'Send Campaign')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">
                <i class="fas fa-paper-plane me-2"></i>Send Campaign
            </h1>
            <p class="text-muted">Review and confirm before sending</p>
        </div>
        <a href="{{ route('admin.campaigns.show', $campaign->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <!-- Warning Alert -->
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Important:</strong> Once you send this campaign, it cannot be stopped or undone. Please review carefully.
    </div>

    <div class="row">
        <!-- Left Column - Preview -->
        <div class="col-lg-8">
            <!-- Email Preview -->
            <div class="card bg-dark border-dark shadow-sm mb-3">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-eye me-2"></i>Email Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 p-3 bg-secondary bg-opacity-25 rounded">
                        <div class="mb-2">
                            <strong class="text-white">From:</strong>
                            <span class="text-muted">{{ config('mail.from.name') }} &lt;{{ config('mail.from.address') }}&gt;</span>
                        </div>
                        <div class="mb-2">
                            <strong class="text-white">To:</strong>
                            <span class="text-muted">{{ $recipients->count() }} recipients</span>
                        </div>
                        <div>
                            <strong class="text-white">Subject:</strong>
                            <span class="text-muted">{{ $campaign->subject }}</span>
                        </div>
                    </div>

                    <div style="background: white; padding: 20px; border-radius: 4px; max-height: 600px; overflow-y: auto;">
                        {!! $campaign->content !!}
                    </div>
                </div>
            </div>

            <!-- Recipients Preview -->
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-users me-2"></i>Recipients (First 20)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recipients->take(20) as $recipient)
                                <tr>
                                    <td>{{ $recipient->email }}</td>
                                    <td>{{ $recipient->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($recipient->status === 'active') bg-success
                                            @elseif($recipient->status === 'unsubscribed') bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ ucfirst($recipient->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($recipient->tags && is_array($recipient->tags))
                                            @foreach(array_slice($recipient->tags, 0, 2) as $tag)
                                                <span class="badge bg-secondary">{{ $tag }}</span>
                                            @endforeach
                                            @if(count($recipient->tags) > 2)
                                                <span class="text-muted">+{{ count($recipient->tags) - 2 }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($recipients->count() > 20)
                    <p class="text-muted text-center mb-0 mt-2">
                        Showing first 20 of {{ number_format($recipients->count()) }} recipients
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Summary & Actions -->
        <div class="col-lg-4">
            <!-- Campaign Summary -->
            <div class="card bg-dark border-dark shadow-sm mb-3">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-info-circle me-2"></i>Campaign Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Campaign Name</small>
                        <p class="text-white mb-0">{{ $campaign->name }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Subject Line</small>
                        <p class="text-white mb-0">{{ $campaign->subject }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Total Recipients</small>
                        <h3 class="text-white mb-0">{{ number_format($recipients->count()) }}</h3>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Recipient Type</small>
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

                    <div class="mb-0">
                        <small class="text-muted d-block">Created</small>
                        <p class="text-white mb-0">{{ $campaign->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Checklist -->
            <div class="card bg-dark border-dark shadow-sm mb-3">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-check-square me-2"></i>Pre-Send Checklist
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="check1" onchange="updateSendButton()">
                        <label class="form-check-label text-white" for="check1">
                            Email content reviewed
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="check2" onchange="updateSendButton()">
                        <label class="form-check-label text-white" for="check2">
                            Subject line is clear
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="check3" onchange="updateSendButton()">
                        <label class="form-check-label text-white" for="check3">
                            Recipients verified
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="check4" onchange="updateSendButton()">
                        <label class="form-check-label text-white" for="check4">
                            Links tested
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check5" onchange="updateSendButton()">
                        <label class="form-check-label text-white" for="check5">
                            Ready to send
                        </label>
                    </div>
                </div>
            </div>

            <!-- Send Actions -->
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.campaigns.process-send', $campaign->id) }}" method="POST" id="sendForm">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="sendButton" disabled>
                                <i class="fas fa-paper-plane me-2"></i>Send Campaign Now
                            </button>
                            <a href="{{ route('admin.campaigns.edit', $campaign->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Edit Campaign
                            </a>
                            <a href="{{ route('admin.campaigns.show', $campaign->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>

                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Note:</strong> Make sure SMTP is configured in your .env file before sending.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateSendButton() {
    const checks = document.querySelectorAll('.form-check-input');
    const allChecked = Array.from(checks).every(check => check.checked);
    const sendButton = document.getElementById('sendButton');
    
    sendButton.disabled = !allChecked;
}

document.getElementById('sendForm').addEventListener('submit', function(e) {
    if (!confirm('Are you sure you want to send this campaign to {{ number_format($recipients->count()) }} recipients? This action cannot be undone.')) {
        e.preventDefault();
    }
});
</script>
@endsection
