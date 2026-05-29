{{--
    Client Status Badge Component
    Usage: @include('client.components.status-badge', ['status' => $application->status, 'label' => $application->status_label])
    Props:
      - $status  : machine status slug (draft|submitted|under_review|...)
      - $label   : human-readable label (optional, falls back to slug)
      - $icon    : optional FA icon class override (e.g. 'fa-check')
      - $size    : 'sm' (default) | 'xs' | 'md'
--}}
@php
    $statusIcons = [
        'draft'               => 'fa-file-alt',
        'submitted'           => 'fa-paper-plane',
        'under_review'        => 'fa-search',
        'document_incomplete' => 'fa-exclamation-triangle',
        'quoted'              => 'fa-file-invoice-dollar',
        'quotation_accepted'  => 'fa-check-circle',
        'payment_pending'     => 'fa-credit-card',
        'payment_verified'    => 'fa-check-double',
        'in_progress'         => 'fa-spinner',
        'completed'           => 'fa-check-circle',
        'cancelled'           => 'fa-times-circle',
        // compliance-monitor statuses
        'active'              => 'fa-shield-check',
        'expiring_soon'       => 'fa-clock',
        'expired'             => 'fa-exclamation-circle',
        'renewed'             => 'fa-sync-alt',
        // api-key statuses
        'active'              => 'fa-circle-check',
        'inactive'            => 'fa-circle-xmark',
    ];
    $resolvedIcon = $icon ?? ($statusIcons[$status] ?? 'fa-circle');
    $resolvedLabel = $label ?? ucfirst(str_replace('_', ' ', $status));
    $sizeClass = match($size ?? 'sm') {
        'xs'  => 'text-[10px] px-1.5 py-0.5 gap-1',
        'md'  => 'text-sm px-3 py-1 gap-1.5',
        default => 'text-xs px-2.5 py-0.5 gap-1',
    };
@endphp

<span class="status-badge status-badge--{{ $status }} {{ $sizeClass }} min-h-0">
    <i class="fas {{ $resolvedIcon }} text-[0.65em]" aria-hidden="true"></i>
    {{ $resolvedLabel }}
</span>
