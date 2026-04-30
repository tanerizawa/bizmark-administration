@extends('layouts.app')

@section('title', 'Template Detail')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">
                <i class="fas fa-file-alt me-2"></i>{{ $template->name }}
            </h1>
            <p class="text-muted">
                <span class="badge 
                    @if($template->category === 'newsletter') bg-info
                    @elseif($template->category === 'promotional') bg-warning
                    @elseif($template->category === 'transactional') bg-success
                    @else bg-secondary
                    @endif">
                    {{ ucfirst($template->category) }}
                </span>
                @if($template->is_active)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
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
        <!-- Left Column - Preview -->
        <div class="col-lg-8">
            <!-- Email Preview -->
            <div class="card bg-dark border-dark shadow-sm mb-3">
                <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-eye me-2"></i>Template Preview
                    </h5>
                    <button class="btn btn-sm btn-outline-info" onclick="openFullPreview()">
                        <i class="fas fa-expand me-1"></i>Full Screen
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3 p-3 bg-secondary bg-opacity-25 rounded">
                        <div class="mb-2">
                            <strong class="text-white">Subject:</strong>
                            <span class="text-muted">{{ $template->subject }}</span>
                        </div>
                        <div>
                            <strong class="text-white">Variables:</strong>
                            <span class="text-muted">
                                @if($template->variables && is_array($template->variables))
                                    @foreach($template->variables as $var)
                                        <span class="badge bg-secondary">@{{ '{{' . $var . '}}' }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">@{{name}}</span>
                                    <span class="badge bg-secondary">@{{email}}</span>
                                    <span class="badge bg-secondary">@{{unsubscribe_url}}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div style="background: white; padding: 20px; border-radius: 4px; max-height: 600px; overflow-y: auto;">
                        {!! $template->content !!}
                    </div>
                </div>
            </div>

            <!-- Plain Text Version (if exists) -->
            @if($template->plain_content)
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-align-left me-2"></i>Plain Text Version
                    </h5>
                </div>
                <div class="card-body">
                    <pre class="text-muted mb-0" style="white-space: pre-wrap;">{{ $template->plain_content }}</pre>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Info & Actions -->
        <div class="col-lg-4">
            <!-- Template Information -->
            <div class="card bg-dark border-dark shadow-sm mb-3">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-info-circle me-2"></i>Template Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Template Name</small>
                        <p class="text-white mb-0">{{ $template->name }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Category</small>
                        <span class="badge 
                            @if($template->category === 'newsletter') bg-info
                            @elseif($template->category === 'promotional') bg-warning
                            @elseif($template->category === 'transactional') bg-success
                            @else bg-secondary
                            @endif fs-6">
                            {{ ucfirst($template->category) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Status</small>
                        @if($template->is_active)
                            <span class="badge bg-success fs-6">Active</span>
                        @else
                            <span class="badge bg-secondary fs-6">Inactive</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Subject Line</small>
                        <p class="text-white mb-0">{{ $template->subject }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Variables</small>
                        @if($template->variables && is_array($template->variables))
                            @foreach($template->variables as $var)
                                <span class="badge bg-secondary">@{{ '{{' . $var . '}}' }}</span>
                            @endforeach
                        @else
                            <span class="badge bg-secondary">@{{name}}</span>
                            <span class="badge bg-secondary">@{{email}}</span>
                            <span class="badge bg-secondary">@{{unsubscribe_url}}</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Created</small>
                        <p class="text-white mb-0">{{ $template->created_at->format('d M Y, H:i') }}</p>
                        <small class="text-muted">{{ $template->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block">Last Updated</small>
                        <p class="text-white mb-0">{{ $template->updated_at->format('d M Y, H:i') }}</p>
                        <small class="text-muted">{{ $template->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="card bg-dark border-dark shadow-sm mb-3">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-chart-bar me-2"></i>Usage Statistics
                    </h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-white mb-0">{{ $template->campaigns->count() }}</h2>
                    <small class="text-muted">Campaigns using this template</small>

                    @if($template->campaigns->count() > 0)
                    <hr class="border-secondary">
                    <div class="text-start">
                        <small class="text-muted d-block mb-2">Recent Campaigns:</small>
                        @foreach($template->campaigns()->latest()->limit(5)->get() as $campaign)
                        <div class="mb-2">
                            <a href="{{ route('admin.campaigns.show', $campaign->id) }}" class="text-info text-decoration-none">
                                {{ $campaign->name }}
                            </a>
                            <br>
                            <small class="text-muted">
                                {{ $campaign->created_at->format('d M Y') }}
                                • 
                                <span class="badge 
                                    @if($campaign->status === 'sent') bg-success
                                    @elseif($campaign->status === 'draft') bg-warning
                                    @else bg-info
                                    @endif">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </small>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Template
                        </a>

                        <a href="{{ route('admin.campaigns.create') }}?template_id={{ $template->id }}" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Use in Campaign
                        </a>

                        @if($template->is_active)
                        <form action="{{ route('admin.templates.update', $template->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="name" value="{{ $template->name }}">
                            <input type="hidden" name="subject" value="{{ $template->subject }}">
                            <input type="hidden" name="content" value="{{ $template->content }}">
                            <input type="hidden" name="category" value="{{ $template->category }}">
                            <input type="hidden" name="is_active" value="0">
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="fas fa-eye-slash me-2"></i>Deactivate
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.templates.update', $template->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="name" value="{{ $template->name }}">
                            <input type="hidden" name="subject" value="{{ $template->subject }}">
                            <input type="hidden" name="content" value="{{ $template->content }}">
                            <input type="hidden" name="category" value="{{ $template->category }}">
                            <input type="hidden" name="is_active" value="1">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-eye me-2"></i>Activate
                            </button>
                        </form>
                        @endif

                        <button class="btn btn-outline-info" onclick="copyTemplate()">
                            <i class="fas fa-copy me-2"></i>Copy HTML
                        </button>

                        @if($template->campaigns->count() === 0)
                        <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Delete this template? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i>Delete Template
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
                    <i class="fas fa-envelope me-2"></i>{{ $template->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="bg-light p-3 border-bottom">
                    <strong>Subject:</strong> {{ $template->subject }}
                </div>
                <div class="p-4" style="background: white;">
                    {!! $template->content !!}
                </div>
            </div>
        </div>
    </div>
</div>

@verbatim
<script>
function openFullPreview() {
    const modal = new bootstrap.Modal(document.getElementById('fullPreviewModal'));
    modal.show();
}

function copyTemplate() {
@endverbatim
    const content = `{!! addslashes($template->content) !!}`;
@verbatim
    
    navigator.clipboard.writeText(content).then(function() {
        alert('Template HTML copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endverbatim
@endsection
