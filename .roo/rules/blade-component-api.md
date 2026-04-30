# Blade Component API Design Rules

## Naming Convention

### File naming
Semua komponen menggunakan **kebab-case**:
```
components/ui/button.blade.php        ✅
components/ui/stat-card.blade.php     ✅
components/ui/data-table.blade.php    ✅
```

### Component prefix
Gunakan prefix `x-` (bukan `ui.`):
```
<x-button variant="primary">         ✅
<x-ui.button variant="primary">       ✅ (alternatif, menggunakan namespace)
```

Gunakan `.` namespace untuk organisasi di folder `components/ui/`:
```blade
{{-- Panggil dengan: --}}
<x-ui.button />
<x-ui.card />
<x-ui.stat-card />

{{-- BUKAN: --}}
<x-button /> {{-- Terlalu generic, bentrok dengan Livewire --}}
```

## Props API Design

### Wajib: Named Parameters
```blade
<!-- ✅ WAJIB -->
<x-ui.button variant="primary" size="lg">Submit</x-ui.button>

<!-- ❌ DILARANG - Positional magic -->
<x-ui.button primary large>Submit</x-ui.button>
```

### Props dengan default values
```blade
@props([
    'variant' => 'primary',   // primary | secondary | outline | ghost | danger
    'size' => 'md',           // sm | md | lg
    'disabled' => false,
    'loading' => false,
    'type' => 'button',       // button | submit | reset
    'href' => null,           // jika diisi render <a> bukan <button>
    'class' => '',            // additional classes
])
```

### Boolean props harus pakai colon
```blade
<!-- ✅ WAJIB -->
<x-ui.button :disabled="true" :loading="isSaving">

<!-- ❌ DILARANG -->
<x-ui.button disabled loading>
```

## Slot System

### Default slot
```blade
{{-- Component --}}
<div {{ $attributes->merge(['class' => '...']) }}>
    {{ $slot }}
</div>

{{-- Usage --}}
<x-ui.button>Click Me</x-ui.button>
```

### Named slots
```blade
{{-- Component --}}
<div>
    <header>{{ $header }}</header>
    <main>{{ $slot }}</main>
    <footer>{{ $footer }}</footer>
</div>

{{-- Usage --}}
<x-ui.card>
    <x-slot:header>Title</x-slot:header>
    Main content here
    <x-slot:footer>Footer</x-slot:footer>
</x-ui.card>
```

### Scoped slots (for table rows etc)
```blade
{{-- Component --}}
@foreach($items as $item)
    {{ $row($item) }}
@endforeach

{{-- Usage --}}
<x-ui.table :items="$users">
    <x-slot:row="$user">
        <td>{{ $user->name }}</td>
    </x-slot:row>
</x-ui.table>
```

## Attributes Forwarding

### Merge classes dengan parent
```blade
<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl p-6']) }}>
```

### Conditional class merge
```blade
@php
    $classes = [
        'bg-blue-500 text-white' => $variant === 'primary',
        'bg-gray-100 text-gray-700' => $variant === 'ghost',
        'px-4 py-2 text-sm' => $size === 'sm',
        'px-6 py-3 text-base' => $size === 'md',
    ];
@endphp

<div {{ $attributes->class($classes) }}>
```

## Alpine.js + Blade Integration

### Component dengan Alpine data
```blade
@props(['id' => 'dropdown-'.uniqid()])

<div
    x-data="{ open: false }"
    x-id="['dropdown-button']"
    {{ $attributes }}
>
    <button
        :id="$id('dropdown-button')"
        @click="open = !open"
        @keydown.escape="open = false"
        :aria-expanded="open"
        aria-haspopup="true"
    >
        {{ $trigger }}
    </button>

    <div
        x-show="open"
        x-transition
        @click.outside="open = false"
        role="menu"
    >
        {{ $slot }}
    </div>
</div>
```

## Component Testing Checklist

Setiap komponen WAJIB memenuhi:

| Check | Keterangan |
|-------|-----------|
| [ ] Props documentation | Setiap prop didokumentasikan dengan komentar |
| [ ] Default values | Semua props punya default value |
| [ ] Dark mode | Bekerja di `dark:` mode |
| [ ] Accessibility | `role`, `aria-*`, keyboard navigation |
| [ ] Responsive | Bekerja di mobile + desktop |
| [ ] Loading state | Punya state loading jika relevan |
| [ ] Error state | Punya state error untuk form components |
| [ ] Edge cases | Empty state, null values, long text |
