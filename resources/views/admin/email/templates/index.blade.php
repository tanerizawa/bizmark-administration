@extends('layouts.app')

@section('title', 'Email Templates')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">Email Templates</h1>
            <p class="text-muted">Kelola template email reusable</p>
        </div>
        <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Buat Template Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-primary text-white rounded">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Total Templates</div>
                            <h4 class="mb-0 text-white">{{ $stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-success text-white rounded">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Active</div>
                            <h4 class="mb-0 text-white">{{ $stats['active'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-info text-white rounded">
                                <i class="fas fa-newspaper"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Newsletter</div>
                            <h4 class="mb-0 text-white">{{ $stats['newsletter'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-warning text-white rounded">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Promotional</div>
                            <h4 class="mb-0 text-white">{{ $stats['promotional'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates List -->
    <div class="card bg-dark border-dark shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-alt text-primary me-2"></i>
                                        <strong class="text-white">{{ $template->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    @if($template->category === 'newsletter')
                                        <span class="badge bg-info">Newsletter</span>
                                    @elseif($template->category === 'promotional')
                                        <span class="badge bg-warning">Promotional</span>
                                    @elseif($template->category === 'transactional')
                                        <span class="badge bg-success">Transactional</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($template->category) }}</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ \Illuminate\Support\Str::limit($template->subject, 50) }}</td>
                                <td>
                                    @if($template->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $template->created_at->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.templates.show', $template) }}" 
                                           class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.templates.edit', $template) }}" 
                                           class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.templates.destroy', $template) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus template ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-file-alt fa-3x mb-3 d-block"></i>
                                    <p>Belum ada template. Buat template pertama Anda!</p>
                                    <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Buat Template
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($templates->hasPages())
                <div class="mt-4">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
