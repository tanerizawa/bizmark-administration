# Alpine.js v3 — Interaction Patterns
> **Last Updated:** 2026-05-03 | Alpine.js v3.15.1 + @alpinejs/collapse v3.15.1 (via npm/Vite, bukan CDN)

---

## Setup & Loading

Alpine.js diload via Vite (npm), bukan CDN:

```js
// resources/js/app.js
import Alpine from 'alpinejs';
import Collapse from '@alpinejs/collapse';

Alpine.plugin(Collapse);
window.Alpine = Alpine;
Alpine.start();
```

---

## Prinsip: Alpine.js untuk Interaktivitas, Bukan Styling

Alpine.js WAJIB digunakan untuk logika interaktif. `onclick`/`onsubmit`/`onchange` inline **DILARANG** kecuali untuk hover sederhana (lihat bagian hover).

### ❌ DILARANG
```html
<button onclick="doSomething()">
<form onsubmit="return validate()">
<select onchange="location.href=this.value">
```

### ✅ WAJIB — Gunakan Alpine.js directives
```html
<button @click="doSomething()">
<form @submit.prevent="validate()">
<select x-data @change="window.location.href = $event.target.value">
```

### ✅ EXCEPTION — Hover sederhana diizinkan inline
```html
{{-- onmouseover/onmouseout untuk opacity/color hover DIIZINKAN --}}
<button onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
<a onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
```

---

## Core Directives

### `x-data` — State Component
```html
<div x-data="{ open: false, count: 0, message: '' }">

{{-- Gunakan Alpine.data() untuk reusable components --}}
<div x-data="myComponent">
```

### `x-show` + `x-transition` — Show/Hide Animasi
```html
<div x-show="open" x-transition>...</div>
<div x-show="open" x-transition.duration.300ms>...</div>

{{-- Enter/leave terpisah --}}
<div x-show="open"
     x-transition:enter.duration.200ms
     x-transition:leave.duration.150ms>
```

### `x-cloak` — Cegah FOUC
```css
/* CSS ini WAJIB ada sebelum Alpine load */
[x-cloak] { display: none !important; }
```
```html
<div x-cloak x-show="open" x-transition>...</div>
```

### `x-model` — Two-way Binding
```html
<input type="text" x-model="search" placeholder="Cari...">
<select x-model="status">
<textarea x-model="content"></textarea>
```

### `x-text` dan `x-html`
```html
<span x-text="count"></span>
<div x-html="htmlContent"></div>
```

### `x-bind` / `:` — Attribute Binding
```html
<button :disabled="loading" :class="active ? 'active-class' : ''">
{{-- Style binding dengan CSS vars --}}
<div :style="selected ? 'color:var(--apple-blue)' : 'color:var(--dark-text-secondary)'">
```

### `x-ref` — DOM Reference
```html
<input x-ref="searchInput" type="text">
<button @click="$refs.searchInput.focus()">Focus</button>
```

### `x-init` — Initialization
```html
<div x-init="fetchData()" x-data="{ items: [] }">
<div x-init="$nextTick(() => { /* setelah DOM render */ })">
```

---

## Magic Properties

### `$el` — Current Element
```html
<button @click="$el.textContent = 'Clicked!'">Click</button>
```

### `$refs` — DOM Reference
```html
<div x-data>
    <input x-ref="email">
    <button @click="$refs.email.focus()">Focus Email</button>
</div>
```

### `$nextTick` — After DOM Update
```html
<div x-data="{ title: 'Hello' }">
    <button @click="title = 'World!'; $nextTick(() => console.log($el.textContent))">
        Update
    </button>
</div>
```

### `$dispatch` — Custom Events
```html
{{-- Trigger event --}}
<button @click="$dispatch('toast', { message: 'Disimpan!', type: 'success' })">Simpan</button>

{{-- Listener (di layout) --}}
<div x-data @toast.window="showToast($event.detail)">
```

### `$watch` — Reactive Watch
```html
<div x-data="{ value: '' }" x-init="$watch('value', val => console.log(val))">
```

### `$store` — Global Store Access
```html
<div x-data>
    <span x-text="$store.user.name"></span>
    <button @click="$store.cart.add(item)">Add to Cart</button>
</div>
```

---

## Alpine.store — Global State

```js
// Di app.js atau blade @push('scripts')
document.addEventListener('alpine:init', () => {
    Alpine.store('notifications', {
        items: [],
        add(msg) { this.items.push(msg) },
        remove(i) { this.items.splice(i, 1) }
    })
})
```

---

## Alpine.data — Reusable Components

```js
document.addEventListener('alpine:init', () => {
    Alpine.data('dropdown', () => ({
        open: false,
        trigger: {
            ['@click']() { this.open = !this.open }
        },
        dialogue: {
            ['x-show']() { return this.open }
        }
    }))
})
```

```html
<div x-data="dropdown">
    <button x-bind="trigger">Open</button>
    <div x-bind="dialogue" @click.outside="open = false">Content</div>
</div>
```

---

## Common Patterns

### Toggle / Dropdown
```html
<div x-data="{ open: false }" @keydown.escape.window="open = false">
    <button @click="open = !open" :aria-expanded="open">Menu</button>
    <div x-cloak x-show="open" @click.outside="open = false" x-transition role="menu">
        {{ $slot }}
    </div>
</div>
```

### Modal
```html
<div x-data="{ open: false }" @keydown.escape.window="open = false">
    <button @click="open = true">Buka Modal</button>

    <div x-cloak x-show="open" x-transition style="position:fixed;inset:0;z-index:50">
        {{-- Backdrop --}}
        <div style="position:absolute;inset:0;background:rgba(0,0,0,.7)" @click="open = false"></div>

        {{-- Dialog --}}
        <div role="dialog" aria-modal="true"
             style="position:relative;z-index:1;background:var(--dark-bg-secondary);border-radius:18px;padding:24px;max-width:500px;margin:auto;margin-top:10vh">
            <h2 id="modal-title" style="color:var(--dark-text-primary)">Judul Modal</h2>
            {{ $slot }}
            <button @click="open = false">Tutup</button>
        </div>
    </div>
</div>
```

### Tabs
```html
<div x-data="{ active: 'tab1' }">
    <div style="display:flex;border-bottom:1px solid var(--dark-separator)">
        <button @click="active = 'tab1'"
                :style="active === 'tab1' ? 'border-bottom:2px solid var(--apple-blue);color:var(--apple-blue)' : 'color:var(--dark-text-secondary)'">
            Tab 1
        </button>
        <button @click="active = 'tab2'"
                :style="active === 'tab2' ? 'border-bottom:2px solid var(--apple-blue);color:var(--apple-blue)' : 'color:var(--dark-text-secondary)'">
            Tab 2
        </button>
    </div>
    <div x-show="active === 'tab1'">Konten Tab 1</div>
    <div x-show="active === 'tab2'">Konten Tab 2</div>
</div>
```

### Accordion (pakai @alpinejs/collapse)
```html
<div x-data="{ open: false }">
    <button @click="open = !open" style="width:100%;display:flex;justify-content:space-between">
        <span>Judul Accordion</span>
        <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
    </button>
    {{-- x-collapse dari @alpinejs/collapse: animasi height smooth --}}
    <div x-show="open" x-collapse>
        <div style="padding:12px 0">Konten accordion</div>
    </div>
</div>
```

### Toast Notification
```html
{{-- Di layout --}}
<div x-data="{ toasts: [] }"
     @toast.window="toasts.push($event.detail); setTimeout(() => toasts.shift(), 3000)"
     style="position:fixed;bottom:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;gap:8px">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-transition
             :style="`background:${toast.type === 'success' ? 'var(--apple-green)' : toast.type === 'error' ? 'var(--apple-red)' : 'var(--dark-bg-tertiary)'};color:#fff;padding:12px 16px;border-radius:10px`">
            <span x-text="toast.message"></span>
        </div>
    </template>
</div>

{{-- Trigger dari mana saja --}}
<button @click="$dispatch('toast', { message: 'Berhasil!', type: 'success', id: Date.now() })">
```

### Loading State
```html
<div x-data="{ loading: false }">
    <button @click="loading = true; doAction().then(() => loading = false)"
            :disabled="loading"
            :style="loading ? 'opacity:.6;cursor:wait' : ''"
            style="background:var(--apple-blue);color:#fff;padding:8px 20px;border-radius:10px;border:none">
        <span x-show="!loading">Simpan</span>
        <span x-show="loading" x-cloak style="display:flex;align-items:center;gap:6px">
            <svg style="animation:spin 1s linear infinite;width:14px;height:14px" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity=".25"/>
                <path fill="currentColor" opacity=".75" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
            </svg>
            Menyimpan...
        </span>
    </button>
</div>
```

### Confirm Delete
```html
<form action="{{ route('...destroy', $id) }}" method="POST"
      x-data @submit.prevent="if(confirm('Yakin ingin menghapus?')) $el.submit()">
    @csrf
    @method('DELETE')
    <button type="submit" style="background:none;border:none;color:var(--apple-red);cursor:pointer">
        <i class="fas fa-trash"></i>
    </button>
</form>
```

---

## Accessibility dengan Alpine

| Directive | Tujuan |
|-----------|--------|
| `:aria-expanded` | Indikasi buka/tutup dropdown |
| `:aria-selected` | Indikasi tab aktif |
| `role="dialog"` | Semantik modal |
| `role="menu"` | Semantik dropdown |
| `role="tab"` + `role="tabpanel"` | Semantik tab |
| `@keydown.escape` | Tutup modal/dropdown |
| `@click.outside` | Tutup saat klik di luar |
| `aria-label` | Label tombol icon-only |
