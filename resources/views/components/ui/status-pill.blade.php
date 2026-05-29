@props([
    'status' => 'neutral',     // success|warning|danger|info|neutral OR raw status enum
    'label' => null,           // optional text override
    'icon' => null,            // optional FA class instead of dot
    'size' => 'sm',            // sm | md
])

@php
    // Map common Indonesian status enums → variant
    $variantMap = [
        // Application / Project status
        'draft'              => 'neutral',
        'submitted'          => 'info',
        'diajukan'           => 'info',
        'in_review'          => 'info',
        'verifikasi'         => 'info',
        'sedang diproses'    => 'info',
        'dalam proses'       => 'info',
        'in_progress'        => 'info',
        'quoted'             => 'info',
        'paid'               => 'warning',
        'pending'            => 'warning',
        'menunggu'           => 'warning',
        'document_incomplete'=> 'warning',
        'dokumen kurang'     => 'warning',
        'expiring'           => 'warning',
        'completed'          => 'success',
        'selesai'            => 'success',
        'diterbitkan'        => 'success',
        'lunas'              => 'success',
        'cancelled'          => 'danger',
        'expired'            => 'danger',
        'rejected'           => 'danger',
        'ditolak'            => 'danger',
    ];

    $key = strtolower(trim((string) $status));
    $variant = $variantMap[$key] ?? (in_array($key, ['success','warning','danger','info','neutral']) ? $key : 'neutral');
    $text = $label ?? \Illuminate\Support\Str::headline($status);
    $sizeClass = $size === 'md' ? 'text-xs px-2.5 py-1' : '';
    $iconClass = $icon ? 'portal-pill--with-icon' : '';
@endphp

<span {{ $attributes->merge(['class' => "portal-pill portal-pill--{$variant} {$iconClass} {$sizeClass}"]) }}>
    @if($icon)
        <i class="{{ $icon }}" aria-hidden="true"></i>
    @endif
    {{ $text }}
</span>
