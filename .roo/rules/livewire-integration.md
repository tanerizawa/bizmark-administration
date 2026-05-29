# Livewire + Blade Integration Patterns
> **Last Updated:** 2026-05-03 | Laravel 11 · Livewire v3/v4 · Alpine.js v3.15.1

---

## Wire Directives dengan Blade Components

Semua `x-ui.*` components mendukung `wire:*` directives karena menggunakan `$attributes->merge()`:

```blade
{{-- ✅ WAJIB --}}
<x-ui.button wire:click="save" wire:loading.attr="disabled">
    Simpan
</x-ui.button>

{{-- Atau langsung jika tidak pakai Blade component --}}
<button wire:click="save"
        wire:loading.attr="disabled"
        style="background:var(--apple-blue);color:#fff;padding:8px 20px;border-radius:10px;border:none">
    Simpan
</button>
```

---

## Loading State

### `wire:loading` (classic approach)
```blade
<button wire:click="save" style="background:var(--apple-blue);color:#fff;...">
    <span wire:loading.remove>Simpan</span>
    <span wire:loading style="display:none;align-items:center;gap:6px">
        <svg style="animation:spin 1s linear infinite;width:14px;height:14px" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity=".25"/>
            <path fill="currentColor" opacity=".75" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
        </svg>
        Menyimpan...
    </span>
</button>
```

### Alpine.js loading state (lebih fleksibel)
```blade
<div x-data="{ saving: false }">
    <button @click="saving = true; $wire.save().then(() => saving = false)"
            :disabled="saving"
            :style="saving ? 'opacity:.6;cursor:wait' : ''"
            style="background:var(--apple-blue);color:#fff;padding:8px 20px;border-radius:10px;border:none">
        <span x-show="!saving">Simpan</span>
        <span x-cloak x-show="saving" style="display:flex;align-items:center;gap:6px">
            Menyimpan...
        </span>
    </button>
</div>
```

---

## `$wire` Magic di Alpine

### Akses properti Livewire
```html
<span x-text="$wire.title"></span>
<span x-text="$wire.items.length + ' item'"></span>
```

### Panggil method Livewire
```html
<button @click="$wire.save()">Simpan</button>
<button @click="$wire.delete(item.id).then(() => showToast('Dihapus'))">Hapus</button>
```

### Set properti Livewire dari Alpine
```html
<button @click="$wire.status = 'active'">Aktifkan</button>
```

---

## `$wire.entangle()` — Two-way Binding Alpine ↔ Livewire

```blade
<div x-data="{ isOpen: $wire.entangle('isOpen') }"
     x-show="isOpen"
     x-transition
     x-cloak
     @keydown.escape.window="$wire.close()">

    {{-- Backdrop --}}
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.7)" @click="$wire.close()"></div>

    {{-- Dialog --}}
    <div role="dialog" aria-modal="true"
         style="position:relative;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:24px">
        <h2 x-text="$wire.title" style="color:var(--dark-text-primary)"></h2>
        <button @click="$wire.confirm()">Konfirmasi</button>
        <button @click="$wire.close()">Batal</button>
    </div>
</div>
```

---

## `$dispatch` Toast dari Livewire

### Di Livewire component (PHP)
```php
// Method di Livewire component
public function save()
{
    // ... save logic
    $this->dispatch('toast', message: 'Data berhasil disimpan!', type: 'success');
}

public function delete($id)
{
    // ... delete logic
    $this->dispatch('toast', message: 'Data dihapus.', type: 'error');
}
```

### Di Alpine listener (di layout)
```html
<div x-data="{ toasts: [] }"
     @toast.window="
         toasts.push({ ...$event.detail, id: Date.now() });
         setTimeout(() => toasts.shift(), 3500)
     "
     style="position:fixed;bottom:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;gap:8px">
    <template x-for="t in toasts" :key="t.id">
        <div x-transition
             :style="`background:${t.type==='success'?'var(--apple-green)':t.type==='error'?'var(--apple-red)':'var(--dark-bg-tertiary)'};color:#fff;padding:12px 20px;border-radius:10px;min-width:200px`">
            <i :class="`fas fa-${t.type==='success'?'check':'exclamation'}-circle`" style="margin-right:8px"></i>
            <span x-text="t.message"></span>
        </div>
    </template>
</div>
```

---

## Form dengan Livewire

```blade
<form wire:submit.prevent="submit">
    {{-- Input dengan wire:model --}}
    <div style="margin-bottom:12px">
        <label style="font-size:.8rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">
            Nama
        </label>
        <input type="text" wire:model="form.name" class="input-apple"
               placeholder="Masukkan nama...">
        @error('form.name')
            <span style="font-size:.75rem;color:var(--apple-red)">{{ $message }}</span>
        @enderror
    </div>

    {{-- Submit button --}}
    <button type="submit"
            wire:loading.attr="disabled"
            style="background:var(--apple-blue);color:#fff;padding:9px 24px;border-radius:10px;border:none;font-weight:600;cursor:pointer">
        <span wire:loading.remove>Simpan</span>
        <span wire:loading>Menyimpan...</span>
    </button>
</form>
```

---

## Table dengan Livewire

```blade
{{-- Livewire table dengan search + filter --}}
<div>
    {{-- Search header --}}
    <div style="display:flex;gap:10px;margin-bottom:12px">
        <input type="text" wire:model.live.debounce.400ms="search"
               class="input-apple" style="flex:1" placeholder="Cari...">
        <select wire:model.live="perPage" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>

    {{-- Table --}}
    <div wire:loading.class="opacity-50" style="transition:opacity .2s">
        <table style="width:100%;border-collapse:collapse">
            <thead style="background:var(--dark-bg-tertiary)">
                <tr>
                    <th style="padding:10px 16px;text-align:left;font-size:.75rem;color:var(--dark-text-secondary)">Nama</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr style="border-top:1px solid var(--dark-separator)">
                    <td style="padding:12px 16px;color:var(--dark-text-primary)">{{ $item->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="padding:12px 0">
        {{ $items->links() }}
    </div>
</div>
```

---

## `wire:model` Modifiers

| Modifier | Kapan Digunakan |
|----------|----------------|
| `wire:model` | Default (lazy: update saat blur) |
| `wire:model.live` | Reactive real-time update |
| `wire:model.live.debounce.400ms` | Search input (debounced) |
| `wire:model.blur` | Update saat focus keluar |

---

## Lazy Loading Livewire

```blade
{{-- Placeholder saat loading --}}
<div wire:init="loadData">
    <div wire:loading style="color:var(--dark-text-secondary);padding:20px;text-align:center">
        <svg style="animation:spin 1s linear infinite;width:20px;height:20px;display:inline-block">...</svg>
        Memuat data...
    </div>
    <div wire:loading.remove>
        {{-- konten --}}
    </div>
</div>
```

---

## Polling

```blade
{{-- Update setiap 5 detik --}}
<div wire:poll.5s>
    <span>Total antrian: {{ $queueCount }}</span>
</div>

{{-- Hanya poll saat visible (lebih efisien) --}}
<div wire:poll.visible.10s>
```
