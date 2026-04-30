# Alpine.js Interaction Patterns

## PENTING: Alpine.js adalah satu-satunya framework JavaScript yang diizinkan untuk interaktivitas frontend.

## Zero Inline JS Event Handlers

### ❌ DILARANG
```html
<div onmouseover="showMenu()" onmouseout="hideMenu()">
<button onclick="confirm('Yakin?')">
<select onchange="location.href=this.value">
<form onsubmit="return validate()">
```

### ✅ WAJIB — Gunakan Alpine.js directives
```html
<div x-data="{ open: false }"
     @mouseenter="open = true"
     @mouseleave="open = false">

<button @click="if(confirm('Yakin?')) $wire.delete()">

<select x-data @change="window.location.href = $event.target.value">

<form @submit.prevent="validate()">
```

## Alpine.js Data Patterns

### Component-scoped data dengan `x-data`
```blade
{{-- Setiap interactive component butuh x-data sendiri --}}
<div x-data="{ count: 0, message: '' }">
    <button @click="count++">Count: <span x-text="count"></span></button>
</div>
```

### Gunakan `$dispatch` untuk komunikasi antar komponen
```blade
{{-- Trigger --}}
<button @click="$dispatch('toast', { message: 'Saved!', type: 'success' })">
    Save
</button>

{{-- Listener (biasanya di layout) --}}
<div x-data @toast.window="showToast($event.detail)">
```

### Gunakan `x-ref` untuk DOM access
```blade
<input x-ref="searchInput" type="text">
<button @click="$refs.searchInput.focus()">Focus</button>
```

## Common Alpine.js Patterns

### Toggle / Dropdown
```blade
<div x-data="{ open: false }" @keydown.escape="open = false">
    <button @click="open = !open"
            :aria-expanded="open"
            aria-haspopup="true">
        Menu
    </button>
    <div x-show="open"
         @click.outside="open = false"
         x-transition
         role="menu">
        {{ $slot }}
    </div>
</div>
```

### Modal
```blade
<div x-data="{ open: false }"
     x-id="['modal-title']"
     @keydown.escape.window="open = false">
    
    <button @click="open = true">Open Modal</button>
    
    <template x-teleport="body">
        <div x-show="open" class="fixed inset-0 z-50" style="display: none;">
            <div x-show="open"
                 x-transition.opacity
                 class="fixed inset-0 bg-black/50"
                 @click="open = false">
            </div>
            <div x-show="open"
                 x-transition
                 class="fixed inset-0 flex items-center justify-center p-4"
                 @click.outside="open = false"
                 :aria-labelledby="$id('modal-title')"
                 role="dialog"
                 aria-modal="true">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-lg w-full">
                    <h2 :id="$id('modal-title')">Modal Title</h2>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
```

### Tabs
```blade
<div x-data="{ activeTab: 'overview' }">
    <nav role="tablist">
        <button @click="activeTab = 'overview'"
                :class="{ 'active': activeTab === 'overview' }"
                role="tab"
                :aria-selected="activeTab === 'overview'">
            Overview
        </button>
        <button @click="activeTab = 'details'"
                :class="{ 'active': activeTab === 'details' }"
                role="tab"
                :aria-selected="activeTab === 'details'">
            Details
        </button>
    </nav>
    
    <div x-show="activeTab === 'overview'" role="tabpanel">
        {{ $tabOverview }}
    </div>
    <div x-show="activeTab === 'details'" role="tabpanel">
        {{ $tabDetails }}
    </div>
</div>
```

### Toast Notification
```blade
{{-- Layout-level component --}}
<div x-data="{ toasts: [] }"
     @toast.window="toasts.push({...$event.detail, id: Date.now()})"
     class="fixed top-4 right-4 z-50 space-y-2">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show"
             x-init="setTimeout(() => toasts.splice(toasts.indexOf(toast), 1), toast.duration || 3000)"
             x-transition
             :class="{
                 'bg-green-500': toast.type === 'success',
                 'bg-red-500': toast.type === 'error',
                 'bg-blue-500': toast.type === 'info',
             }"
             class="rounded-xl px-6 py-3 text-white shadow-lg">
            <span x-text="toast.message"></span>
            <button @click="toasts.splice(toasts.indexOf(toast), 1)" class="ml-3">
                &times;
            </button>
        </div>
    </template>
</div>
```

### Loading State
```blade
<div x-data="{ loading: false }">
    <button @click="loading = true; $wire.save().then(() => loading = false)"
            :disabled="loading"
            class="relative">
        <span x-show="!loading">Save</span>
        <span x-show="loading" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Saving...
        </span>
    </button>
</div>
```

### Form dengan Validation
```blade
<form x-data="{ 
        form: { email: '', password: '' },
        errors: {},
        submit() {
            this.errors = {};
            if (!this.form.email) this.errors.email = 'Email wajib diisi';
            if (!this.form.password) this.errors.password = 'Password wajib diisi';
            if (Object.keys(this.errors).length === 0) {
                // submit
            }
        }
     }"
     @submit.prevent="submit()">
    
    <x-ui.input name="email" x-model="form.email" label="Email" />
    <template x-if="errors.email">
        <p class="text-red-500 text-sm" x-text="errors.email"></p>
    </template>
    
    <x-ui.input name="password" type="password" x-model="form.password" label="Password" />
    <template x-if="errors.password">
        <p class="text-red-500 text-sm" x-text="errors.password"></p>
    </template>
    
    <button type="submit" class="bg-blue-500 text-white rounded-xl px-6 py-3">
        Login
    </button>
</form>
```

## Accessibility with Alpine.js

| Directive | Accessibility Purpose |
|-----------|---------------------|
| `:aria-expanded` | Indicate expand/collapse state |
| `:aria-selected` | Indicate selected tab/item |
| `:aria-labelledby` | Associate label with element |
| `:aria-hidden` | Hide decorative elements |
| `role="dialog"` | Modal/alert semantics |
| `role="tablist"` | Tab container |
| `role="tab"` | Individual tab |
| `role="tabpanel"` | Tab content panel |
| `role="menu"` | Dropdown menu |
| `role="menuitem"` | Dropdown item |
| `@keydown.escape` | Close modal/dropdown on Escape |
| `@keydown.arrow-down` | Navigate dropdown items |
| `@click.outside` | Close on outside click |

## Pines UI Integration

Pines UI components boleh digunakan dengan ketentuan:

1. **Copy-paste** source code Pines UI ke Blade component
2. **Sesuaikan styling** dengan design tokens (ganti warna hardcoded)
3. **Bungkus** dalam Blade component dengan slot system
4. **Test** di semua browser support

```blade
{{-- Contoh: Pines UI Dropdown dibungkus dalam Blade component --}}
<div x-data="{ open: false, activeDescendant: null }"
     @keydown.escape="open = false"
     @keydown.down.prevent="..."
     @keydown.up.prevent="..."
     role="menu"
     {{ $attributes }}>
    {{ $trigger }}
    <div x-show="open" @click.outside="open = false" x-transition>
        {{ $slot }}
    </div>
</div>
```
