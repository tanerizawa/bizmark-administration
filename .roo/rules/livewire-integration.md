# Livewire + Blade Component Integration Patterns

## PENTING: Livewire v4 digunakan untuk stateful interaktivitas admin panel.

## Blade Components dengan Livewire Directives

Semua `x-ui.*` komponen mendukung Livewire `wire:*` directives secara native karena menggunakan `$attributes->merge()`:

```blade
{{-- ✅ WAJIB --}}
<x-ui.button wire:click="save" wire:loading.attr="disabled">
    Simpan
</x-ui.button>

{{-- ❌ DILARANG --}}
<button wire:click="save">Simpan</button>
```

### Loading State dengan Livewire + Alpine

```blade
{{-- ✅ WAJIB -- Gunakan Alpine.js untuk loading state --}}
<x-ui.button
    wire:click="save"
    wire:loading.attr="disabled"
    :loading="$saving ?? false"
    loadingText="Menyimpan..."
>
    Simpan
</x-ui.button>

{{-- Atau dengan Alpine x-data --}}
<div x-data="{ saving: false }">
    <x-ui.button
        @click="saving = true; $wire.save().then(() => saving = false)"
        :loading="saving"
    >
        Simpan
    </x-ui.button>
</div>
```

## Form Components dengan Livewire Binding

### `wire:model` pada form components

```blade
{{-- ✅ WAJIB -- Gunakan wire:model dengan x-ui.input --}}
<x-ui.input
    name="email"
    label="Email"
    wire:model="email"
    :error="$errors->first('email')"
/>

<x-ui.select
    name="category"
    label="Kategori"
    wire:model="category"
    :options="$categories"
/>

<x-ui.checkbox
    name="agree"
    label="Saya setuju dengan syarat & ketentuan"
    wire:model="agree"
/>

<x-ui.toggle
    name="is_active"
    label="Aktif"
    wire:model="isActive"
/>
```

### File Upload dengan Livewire

```blade
{{-- ✅ WAJIB -- Gunakan wire:model untuk file upload --}}
<x-ui.file-upload
    name="document"
    label="Upload Dokumen"
    wire:model="document"
    accept=".pdf,.jpg,.png"
    :maxSize="2048"
/>

{{-- Dengan temporary preview --}}
@if ($document)
    <p class="text-sm text-gray-500 mt-1">
        File: {{ $document->getClientOriginalName() }}
    </p>
@endif
```

## Toast Notification dengan Livewire

```blade
{{-- ✅ WAJIB -- Dispatch toast dari Livewire component --}}
<button wire:click="save" @click="$dispatch('toast', {
    message: 'Data berhasil disimpan',
    type: 'success'
})">
    Simpan
</button>

{{-- Atau via Alpine --}}
<button x-data @click="
    $wire.save()
        .then(() => $dispatch('toast', { message: 'Sukses!', type: 'success' }))
        .catch(() => $dispatch('toast', { message: 'Gagal!', type: 'error' }));
">
    Simpan
</button>
```

## Modal dengan Livewire

```blade
{{-- ✅ WAJIB -- Gunakan x-ui.modal dengan Livewire actions --}}
<x-ui.modal
    title="Konfirmasi Hapus"
    submitLabel="Ya, Hapus"
>
    <x-slot:trigger>
        <x-ui.button variant="danger" size="sm">
            Hapus
        </x-ui.button>
    </x-slot:trigger>

    <p>Apakah Anda yakin ingin menghapus data ini?</p>

    <x-slot:footer>
        <x-ui.button variant="ghost" @click="open = false">
            Batal
        </x-ui.button>
        <x-ui.button
            variant="danger"
            wire:click="delete"
            @click="open = false"
            loadingText="Menghapus..."
        >
            Ya, Hapus
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

## Data Table dengan Livewire

Untuk tabel dengan Livewire pagination + search:

```blade
<div>
    {{-- Search input --}}
    <x-ui.input
        name="search"
        placeholder="Cari data..."
        wire:model.live.debounce.300ms="search"
        leadingIcon="fa-solid fa-search"
    />

    {{-- Table --}}
    <x-ui.table
        :columns="[
            ['key' => 'name', 'label' => 'Nama', 'sortable' => true],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'actions', 'label' => 'Aksi'],
        ]"
        :rows="$users"
        :striped="true"
        :hoverable="true"
    >
        <x-slot:cell-status="{ row }">
            <x-ui.badge
                :variant="$row->status === 'active' ? 'success' : 'warning'"
            >
                {{ $row->status }}
            </x-ui.badge>
        </x-slot:cell-status>

        <x-slot:cell-actions="{ row }">
            <x-ui.button size="sm" variant="outline"
                         wire:click="edit({{ $row->id }})">
                Edit
            </x-ui.button>
        </x-slot:cell-actions>
    </x-ui.table>

    {{-- Pagination --}}
    <x-ui.pagination :paginator="$users" variant="full" :showInfo="true" />
</div>
```

## Actions dengan Alpine + $wire

### Confirm dialog sebelum action

```blade
<button x-data @click="
    if (confirm('Yakin ingin menghapus?')) {
        $wire.delete({{ $item->id }})
            .then(() => $dispatch('toast', { message: 'Terhapus!', type: 'success' }));
    }
">
    Hapus
</button>
```

### Batch actions

```blade
<div x-data="{ selectedIds: [] }">
    {{-- Checkbox untuk select all --}}
    <x-ui.checkbox
        name="select_all"
        label="Pilih Semua"
        @change="selectedIds = $event.target.checked ? {{ json_encode($items->pluck('id')) }} : []"
    />

    <x-ui.button
        variant="danger"
        @click="if (selectedIds.length) {
            $wire.batchDelete(selectedIds)
                .then(() => $dispatch('toast', { message: selectedIds.length + ' data dihapus', type: 'success' }));
        }"
        :disabled="selectedIds.length === 0"
    >
        Hapus Terpilih ({{ count($selected) }})
    </x-ui.button>
</div>
```

## Tab Navigation dengan Livewire

```blade
{{-- ✅ WAJIB -- Untuk Livewire tab switching, gunakan wire:click --}}
<x-ui.tabs
    :tabs="[
        ['id' => 'overview', 'label' => 'Overview'],
        ['id' => 'details', 'label' => 'Details'],
        ['id' => 'history', 'label' => 'History', 'badge' => 5],
    ]"
    defaultTab="overview"
    :activeTab="$activeTab"
>
    @php $tabs = ['overview', 'details', 'history']; @endphp
    @foreach($tabs as $tab)
        <x-slot:tab-{{ $tab }}>
            <div wire:key="{{ $tab }}">
                {{-- Konten per tab dengan Livewire loading --}}
                @switch($tab)
                    @case('overview')
                        @include('admin.permits.tabs.overview')
                        @break
                    @case('details')
                        @include('admin.permits.tabs.details')
                        @break
                    @case('history')
                        @include('admin.permits.tabs.history')
                        @break
                @endswitch
            </div>
        </x-slot:tab-{{ $tab }}>
    @endforeach
</x-ui.tabs>

{{-- Di Livewire component --}}
<script>
    // Trigger tab change
    document.addEventListener('livewire:init', () => {
        Livewire.on('tabChanged', (tab) => {
            Alpine.store('tabs', { activeTab: tab });
        });
    });
</script>
```

## Accessibility dengan Livewire

| Directive | Accessibility Purpose |
|-----------|---------------------|
| `wire:loading.attr="disabled"` | Disable button during submission |
| `wire:loading.class="opacity-50"` | Visual loading indicator |
| `wire:target="save"` | Scope loading state to specific action |
| `wire:loading.remove` | Hide element during loading |
| `wire:loading.add` | Show element during loading |
| `aria-busy="true"` via `wire:loading` | Screen reader loading announcement |

```blade
<x-ui.button
    wire:click="save"
    wire:loading.attr="disabled"
    wire:target="save"
    aria-busy="false"
    wire:loading.attr="aria-busy"
    wire:target="save"
    wire:loading.remove
>
    Simpan
</x-ui.button>
```

## Checklist Penggunaan

| Check | Keterangan |
|-------|-----------|
| [ ] | Gunakan `x-ui.*` components + `wire:*`, bukan HTML element langsung |
| [ ] | `wire:loading.attr="disabled"` untuk semua submit buttons |
| [ ] | Dispatch `toast` event setelah setiap operasi sukses/gagal |
| [ ] | File upload selalu pakai `wire:model` dengan `x-ui.file-upload` |
| [ ] | Tabel dengan pagination/search selalu pakai `x-ui.table` + `x-ui.pagination` |
| [ ] | Modal dengan konfirmasi selalu pakai `x-ui.modal` |
| [ ] | Loading state indikator untuk semua operasi async |
