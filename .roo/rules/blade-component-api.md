# Blade Component API — Design Rules
> **Last Updated:** 2026-05-03 | Laravel 11 · Blade Components · Alpine.js v3.15.1

---

## Konteks: Blade Components vs Direct HTML

App ini menggunakan **dua pendekatan** tergantung konteks:

| Pendekatan | Digunakan Di | Kapan |
|-----------|-------------|-------|
| `x-ui.*` Blade components | Landing page, generic UI | Komponen reusable dengan slot |
| Direct HTML + inline style | Admin panel views | Styling Apple dark theme reliabel |

> **Admin panel**: Prioritaskan direct HTML dengan inline style + CSS vars daripada Blade component, karena component kadang membawa class yang konflik dengan dark theme. Gunakan Blade component `x-ui.*` hanya untuk komponen yang benar-benar reusable (modal, alert, pagination).

---

## Naming Convention

### File naming — kebab-case
```
components/ui/button.blade.php        ✅
components/ui/stat-card.blade.php     ✅
components/ui/data-table.blade.php    ✅
```

### Namespace `x-ui.*`
```blade
{{-- Gunakan namespace ui untuk organisasi --}}
<x-ui.button variant="primary">Submit</x-ui.button>
<x-ui.card>{{ $slot }}</x-ui.card>
<x-ui.modal :open="$showModal" title="Konfirmasi">
```

---

## Props API Design

### WAJIB: Named Parameters
```blade
{{-- ✅ WAJIB --}}
<x-ui.button variant="primary" size="lg">Submit</x-ui.button>

{{-- ❌ DILARANG --}}
<x-ui.button primary large>Submit</x-ui.button>
```

### `@props` dengan default values
```blade
@props([
    'variant' => 'primary',   // primary | secondary | outline | ghost | danger
    'size' => 'md',           // sm | md | lg
    'disabled' => false,
    'loading' => false,
    'type' => 'button',
    'href' => null,
    'class' => '',
])
```

### Boolean props — WAJIB pakai colon
```blade
{{-- ✅ WAJIB --}}
<x-ui.button :disabled="true" :loading="$isSaving">

{{-- ❌ DILARANG --}}
<x-ui.button disabled loading>
```

---

## Blade Component + Dark Theme

Saat membuat Blade component untuk admin panel, gunakan CSS vars, bukan Tailwind color classes:

```blade
{{-- ✅ BENAR — component dengan CSS vars --}}
<div {{ $attributes->merge(['class' => '']) }}
     style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:{{ $padding ?? '16px' }}">
    {{ $slot }}
</div>

{{-- ❌ SALAH — Tailwind dark: di component --}}
<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-xl p-4']) }}>
    {{ $slot }}
</div>
```

---

## Slot System

### Default slot
```blade
{{-- Component --}}
<div {{ $attributes->merge(['class' => '']) }}>
    {{ $slot }}
</div>

{{-- Usage --}}
<x-ui.card>Konten di sini</x-ui.card>
```

### Named slots
```blade
{{-- Component (card.blade.php) --}}
<div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
    @if(isset($header))
    <div style="padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
        {{ $header }}
    </div>
    @endif
    <div style="padding:16px 20px">
        {{ $slot }}
    </div>
    @if(isset($footer))
    <div style="padding:12px 20px;border-top:1px solid var(--dark-separator)">
        {{ $footer }}
    </div>
    @endif
</div>

{{-- Usage --}}
<x-ui.card>
    <x-slot:header>
        <h3 style="color:var(--dark-text-primary);font-size:.9rem;font-weight:700">Judul</h3>
    </x-slot:header>
    Konten utama
    <x-slot:footer>
        <button>Simpan</button>
    </x-slot:footer>
</x-ui.card>
```

---

## `$attributes` Forwarding

WAJIB gunakan `$attributes->merge()` agar Livewire/Alpine directives bisa diteruskan:

```blade
{{-- ✅ WAJIB di setiap component --}}
<button {{ $attributes->merge(['type' => $type]) }}
        style="background:var(--apple-blue);color:#fff;...">
    {{ $slot }}
</button>

{{-- Maka bisa digunakan: --}}
<x-ui.button wire:click="save" @click="loading = true" type="submit">
    Simpan
</x-ui.button>
```

---

## Alpine.js di Blade Components

```blade
{{-- Component bisa embed Alpine state --}}
@props(['title' => '', 'open' => false])

<div x-data="{ open: {{ $open ? 'true' : 'false' }} }"
     @keydown.escape.window="open = false">
    <button @click="open = !open">{{ $title }}</button>
    <div x-cloak x-show="open" x-transition @click.outside="open = false">
        {{ $slot }}
    </div>
</div>
```

---

## Component Checklist

Saat membuat Blade component baru:

- [ ] `@props([...])` di baris pertama dengan defaults
- [ ] `$attributes->merge()` di root element
- [ ] CSS vars (bukan hardcoded hex) untuk warna
- [ ] `x-cloak` di semua show/hide Alpine elements
- [ ] `aria-*` attributes untuk accessibility
- [ ] Support untuk `$slot` (default) dan named slots jika diperlukan
- [ ] Tidak menggunakan Tailwind `dark:` color classes

---

## Testing

```php
// tests/Feature/BladeComponentTest.php
it('renders button with variant', function() {
    $view = $this->blade('<x-ui.button variant="primary">Save</x-ui.button>');
    $view->assertSee('Save');
});
```
