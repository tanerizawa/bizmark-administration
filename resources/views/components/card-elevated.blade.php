{{-- 
    Apple HIG Card Component - Dark Mode
    Usage: <x-card-elevated>content</x-card-elevated>
--}}
<div {{ $attributes->merge(['class' => 'card-elevated rounded-apple-lg p-4 hover-lift']) }}>
    {{ $slot }}
</div>
