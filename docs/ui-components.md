# Bizmark UI Component Library — Reference

> **Last Updated:** 2026-05-03
> **Components:** 28 Blade components in `resources/views/components/ui/`
> **Test Coverage:** See [`tests/Feature/BladeComponentTest.php`](/tests/Feature/BladeComponentTest.php)
> **Stack:** Tailwind CSS v4 · Alpine.js v3.15.1 · Font Awesome v7.1.0 · Vite v7

## Table of Contents

1. [Installation & Setup](#installation--setup)
2. [Component Index](#component-index)
3. [Core Components](#core-components)
   - [Button](#x-uibutton)
   - [Card](#x-uicard)
   - [Badge](#x-uibadge)
   - [Input](#x-uiinput)
   - [Select](#x-uiselect)
   - [Textarea](#x-uitextarea)
4. [Interactive Components](#interactive-components)
   - [Modal](#x-uimodal)
   - [Dropdown](#x-uidropdown)
   - [Tabs](#x-uitabs)
   - [Accordion](#x-uiaccordion)
   - [Tooltip](#x-uitooltip)
   - [Toast](#x-uitoast)
5. [Data Display](#data-display)
   - [Table](#x-uitable)
   - [Pagination](#x-uipagination)
   - [Stat Card](#x-uistat-card)
   - [Progress](#x-uiprogress)
   - [Empty State](#x-uiempty-state)
   - [Skeleton](#x-uiskeleton)
   - [Avatar](#x-uiavatar)
   - [Breadcrumb](#x-uibreadcrumb)
6. [Form Components](#form-components)
   - [Checkbox](#x-uicheckbox)
   - [Toggle](#x-uitoggle)
   - [Radio Group](#x-uiradio-group)
   - [File Upload](#x-uifile-upload)
7. [Utility Components](#utility-components)
   - [Alert](#x-uialert)
   - [Button Spinner](#x-uibutton-spinner)
   - [Dropdown Item](#x-uidropdown-item)
   - [Dropdown Divider](#x-uidropdown-divider)
8. [Usage Guidelines](#usage-guidelines)
9. [Migration Checklist](#migration-checklist)

---

## Installation & Setup

### Prerequisites

- Laravel 11+
- Tailwind CSS v4 (via `@tailwindcss/vite` plugin — no tailwind.config.js)
- Alpine.js v3.15.1 + @alpinejs/collapse v3.15.1 (via Vite/npm, bukan CDN)
- Font Awesome v7.1.0 Free (via Vite/npm, bukan CDN)

### Auto-discovery

Semua komponen di `resources/views/components/ui/` auto-registered oleh Laravel sebagai `x-ui.*` namespace.

```blade
{{-- Panggil dengan --}}
<x-ui.button variant="primary">Submit</x-ui.button>
<x-ui.card>
    {{ $slot }}
</x-ui.card>
```

### Global Dependencies

Semua library sudah di-bundle via Vite (npm) — **TIDAK menggunakan CDN**:

```js
// resources/js/app.js
import Alpine from 'alpinejs';           // Alpine.js v3.15.1
import Collapse from '@alpinejs/collapse'; // @alpinejs/collapse v3.15.1
Alpine.plugin(Collapse);
window.Alpine = Alpine;
Alpine.start();
```

```css
/* resources/css/admin.css */
@import '@fortawesome/fontawesome-free/css/fontawesome.css';  /* FA v7.1.0 */
@import '@fortawesome/fontawesome-free/css/regular.css';
@import '@fortawesome/fontawesome-free/css/solid.css';
@import '@fortawesome/fontawesome-free/css/brands.css';
```

> ⚠️ **Jangan** tambahkan CDN untuk Alpine atau Font Awesome — akan menyebabkan duplikasi.

---

## Component Index

| # | Component | File | Type | Alpine | Livewire |
|---|-----------|------|------|--------|----------|
| 1 | [`x-ui.button`](#x-uibutton) | [`button.blade.php`](/resources/views/components/ui/button.blade.php) | Core | ✅ | ✅ |
| 2 | [`x-ui.card`](#x-uicard) | [`card.blade.php`](/resources/views/components/ui/card.blade.php) | Core | ❌ | ✅ |
| 3 | [`x-ui.badge`](#x-uibadge) | [`badge.blade.php`](/resources/views/components/ui/badge.blade.php) | Core | ❌ | ❌ |
| 4 | [`x-ui.input`](#x-uiinput) | [`input.blade.php`](/resources/views/components/ui/input.blade.php) | Core | ✅ | ✅ |
| 5 | [`x-ui.select`](#x-uiselect) | [`select.blade.php`](/resources/views/components/ui/select.blade.php) | Core | ❌ | ✅ |
| 6 | [`x-ui.textarea`](#x-uitextarea) | [`textarea.blade.php`](/resources/views/components/ui/textarea.blade.php) | Core | ❌ | ✅ |
| 7 | [`x-ui.modal`](#x-uimodal) | [`modal.blade.php`](/resources/views/components/ui/modal.blade.php) | Interactive | ✅ | ✅ |
| 8 | [`x-ui.dropdown`](#x-uidropdown) | [`dropdown.blade.php`](/resources/views/components/ui/dropdown.blade.php) | Interactive | ✅ | ✅ |
| 9 | [`x-ui.tabs`](#x-uitabs) | [`tabs.blade.php`](/resources/views/components/ui/tabs.blade.php) | Interactive | ✅ | ✅ |
| 10 | [`x-ui.accordion`](#x-uiaccordion) | [`accordion.blade.php`](/resources/views/components/ui/accordion.blade.php) | Interactive | ✅ | ❌ |
| 11 | [`x-ui.tooltip`](#x-uitooltip) | [`tooltip.blade.php`](/resources/views/components/ui/tooltip.blade.php) | Interactive | ✅ | ❌ |
| 12 | [`x-ui.toast`](#x-uitoast) | [`toast.blade.php`](/resources/views/components/ui/toast.blade.php) | Interactive | ✅ | ✅ |
| 13 | [`x-ui.table`](#x-uitable) | [`table.blade.php`](/resources/views/components/ui/table.blade.php) | Data | ❌ | ✅ |
| 14 | [`x-ui.pagination`](#x-uipagination) | [`pagination.blade.php`](/resources/views/components/ui/pagination.blade.php) | Data | ❌ | ✅ |
| 15 | [`x-ui.stat-card`](#x-uistat-card) | [`stat-card.blade.php`](/resources/views/components/ui/stat-card.blade.php) | Data | ❌ | ❌ |
| 16 | [`x-ui.progress`](#x-uiprogress) | [`progress.blade.php`](/resources/views/components/ui/progress.blade.php) | Data | ❌ | ❌ |
| 17 | [`x-ui.empty-state`](#x-uiempty-state) | [`empty-state.blade.php`](/resources/views/components/ui/empty-state.blade.php) | Data | ❌ | ❌ |
| 18 | [`x-ui.skeleton`](#x-uiskeleton) | [`skeleton.blade.php`](/resources/views/components/ui/skeleton.blade.php) | Data | ❌ | ❌ |
| 19 | [`x-ui.avatar`](#x-uiavatar) | [`avatar.blade.php`](/resources/views/components/ui/avatar.blade.php) | Data | ❌ | ❌ |
| 20 | [`x-ui.breadcrumb`](#x-uibreadcrumb) | [`breadcrumb.blade.php`](/resources/views/components/ui/breadcrumb.blade.php) | Data | ❌ | ❌ |
| 21 | [`x-ui.checkbox`](#x-uicheckbox) | [`checkbox.blade.php`](/resources/views/components/ui/checkbox.blade.php) | Form | ❌ | ✅ |
| 22 | [`x-ui.toggle`](#x-uitoggle) | [`toggle.blade.php`](/resources/views/components/ui/toggle.blade.php) | Form | ✅ | ✅ |
| 23 | [`x-ui.radio-group`](#x-uiradio-group) | [`radio-group.blade.php`](/resources/views/components/ui/radio-group.blade.php) | Form | ❌ | ✅ |
| 24 | [`x-ui.file-upload`](#x-uifile-upload) | [`file-upload.blade.php`](/resources/views/components/ui/file-upload.blade.php) | Form | ✅ | ✅ |
| 25 | [`x-ui.alert`](#x-uialert) | [`alert.blade.php`](/resources/views/components/ui/alert.blade.php) | Utility | ✅ | ❌ |
| 26 | [`x-ui.button-spinner`](#x-uibutton-spinner) | [`button-spinner.blade.php`](/resources/views/components/ui/button-spinner.blade.php) | Utility | ❌ | ❌ |
| 27 | [`x-ui.dropdown-item`](#x-uidropdown-item) | [`dropdown-item.blade.php`](/resources/views/components/ui/dropdown-item.blade.php) | Utility | ❌ | ❌ |
| 28 | [`x-ui.dropdown-divider`](#x-uidropdown-divider) | [`dropdown-divider.blade.php`](/resources/views/components/ui/dropdown-divider.blade.php) | Utility | ❌ | ❌ |

---

## Core Components

### `x-ui.button`

**File:** [`button.blade.php`](/resources/views/components/ui/button.blade.php)

Versatile button component that renders as `<button>` or `<a>` depending on `href` prop.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `variant` | string | `primary` | `primary`, `secondary`, `outline`, `ghost`, `danger` | Visual style |
| `size` | string | `md` | `sm`, `md`, `lg` | Size preset |
| `disabled` | bool | `false` | — | Disabled state |
| `loading` | bool | `false` | — | Loading state (shows spinner) |
| `loadingText` | string | `null` | — | Text to show during loading |
| `type` | string | `button` | `button`, `submit`, `reset` | Button type attribute |
| `href` | string | `null` | — | If set, renders `<a>` instead of `<button>` |
| `class` | string | `''` | — | Additional CSS classes |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Button content / label |

#### Usage

```blade
{{-- Button variants --}}
<x-ui.button variant="primary">Submit</x-ui.button>
<x-ui.button variant="secondary">Cancel</x-ui.button>
<x-ui.button variant="outline">View</x-ui.button>
<x-ui.button variant="ghost">More</x-ui.button>
<x-ui.button variant="danger">Delete</x-ui.button>

{{-- Sizes --}}
<x-ui.button size="sm">Small</x-ui.button>
<x-ui.button size="md">Medium</x-ui.button>
<x-ui.button size="lg">Large</x-ui.button>

{{-- States --}}
<x-ui.button :disabled="true">Disabled</x-ui.button>
<x-ui.button :loading="true" loadingText="Saving...">Save</x-ui.button>

{{-- As link --}}
<x-ui.button href="{{ route('users.show', $user) }}">View</x-ui.button>

{{-- With Livewire --}}
<x-ui.button wire:click="save" wire:loading.attr="disabled">Save</x-ui.button>

{{-- With Alpine --}}
<button x-data @click="if(confirm('Yakin?')) $wire.delete()">
    Hapus
</button>
```

#### Testing

```php
public function test_button_renders_all_variants(): void
{
    $variants = ['primary', 'secondary', 'outline', 'ghost', 'danger'];
    foreach ($variants as $variant) {
        $html = Blade::render("<x-ui.button variant=\"$variant\">Test</x-ui.button>");
        $this->assertStringContainsString('Test', $html);
    }
}
```

---

### `x-ui.card`

**File:** [`card.blade.php`](/resources/views/components/ui/card.blade.php)

Container component with multiple visual variants.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `variant` | string | `elevated` | `elevated`, `bordered`, `flat` | Visual style |
| `padding` | string | `md` | `none`, `sm`, `md`, `lg` | Internal padding |
| `class` | string | `''` | — | Additional CSS classes |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Main content |
| `header` | Optional header section |
| `footer` | Optional footer section |

#### Usage

```blade
{{-- Basic --}}
<x-ui.card>
    <p>Content here</p>
</x-ui.card>

{{-- With header & footer --}}
<x-ui.card>
    <x-slot:header>
        <h2 class="text-lg font-semibold">Card Title</h2>
    </x-slot:header>
    
    <p>Main content</p>
    
    <x-slot:footer>
        <p class="text-sm text-gray-500">Footer text</p>
    </x-slot:footer>
</x-ui.card>

{{-- Variants --}}
<x-ui.card variant="elevated">...</x-ui.card>  {{-- shadow --}}
<x-ui.card variant="bordered">...</x-ui.card>  {{-- border only --}}
<x-ui.card variant="flat">...</x-ui.card>       {{-- no shadow/border --}}
```

---

### `x-ui.badge`

**File:** [`badge.blade.php`](/resources/views/components/ui/badge.blade.php)

Small label/tag for status indicators.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `variant` | string | `neutral` | `neutral`, `primary`, `success`, `warning`, `danger`, `info` | Color variant |
| `size` | string | `md` | `sm`, `md`, `lg` | Size |
| `pill` | bool | `false` | — | Fully rounded (pill) shape |
| `dot` | bool | `false` | — | Show a colored dot before text |
| `class` | string | `''` | — | Additional classes |

#### Usage

```blade
<x-ui.badge variant="success">Active</x-ui.badge>
<x-ui.badge variant="danger">Inactive</x-ui.badge>
<x-ui.badge variant="warning">Pending</x-ui.badge>
<x-ui.badge variant="info">Draft</x-ui.badge>
<x-ui.badge :dot="true" variant="success">Online</x-ui.badge>
<x-ui.badge :pill="true">New</x-ui.badge>
```

---

### `x-ui.input`

**File:** [`input.blade.php`](/resources/views/components/ui/input.blade.php)

Text input with label, error state, helper text, and leading icon support.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `name` | string | `''` | — | Input name attribute |
| `type` | string | `text` | `text`, `email`, `password`, `number`, `tel`, `url`, `date`, `search` | Input type |
| `label` | string | `null` | — | Label text |
| `placeholder` | string | `null` | — | Placeholder text |
| `value` | string | `null` | — | Default value |
| `error` | string | `null` | — | Error message (shows red styling) |
| `helper` | string | `null` | — | Helper text below input |
| `required` | bool | `false` | — | Show required indicator |
| `disabled` | bool | `false` | — | Disabled state |
| `leadingIcon` | string | `null` | — | Font Awesome class (e.g. `fa-solid fa-search`) |
| `class` | string | `''` | — | Additional classes |

#### Usage

```blade
{{-- Basic --}}
<x-ui.input name="email" type="email" label="Email" placeholder="user@example.com" />

{{-- With error --}}
<x-ui.input name="email" label="Email" :error="$errors->first('email')" />

{{-- With leading icon --}}
<x-ui.input name="search" placeholder="Search..." leadingIcon="fa-solid fa-search" />

{{-- Required --}}
<x-ui.input name="name" label="Name" :required="true" />

{{-- With Livewire --}}
<x-ui.input name="email" label="Email" wire:model="email" />
```

---

### `x-ui.select`

**File:** [`select.blade.php`](/resources/views/components/ui/select.blade.php)

Dropdown select with label, error state, and placeholder support.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `name` | string | `''` | — | Select name attribute |
| `label` | string | `null` | — | Label text |
| `options` | array | `[]` | — | Options array `['value' => 'Label']` or collection |
| `placeholder` | string | `null` | — | Placeholder option |
| `selected` | string | `null` | — | Currently selected value |
| `error` | string | `null` | — | Error message |
| `required` | bool | `false` | — | Required indicator |
| `class` | string | `''` | — | Additional classes |

#### Usage

```blade
<x-ui.select
    name="category"
    label="Category"
    :options="[
        'general' => 'General',
        'support' => 'Support',
        'billing' => 'Billing',
    ]"
    placeholder="Pilih kategori"
    wire:model="category"
/>
```

---

### `x-ui.textarea`

**File:** [`textarea.blade.php`](/resources/views/components/ui/textarea.blade.php)

Multi-line text input with label and error state.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `''` | Textarea name |
| `label` | string | `null` | Label text |
| `placeholder` | string | `null` | Placeholder text |
| `value` | string | `null` | Default value |
| `error` | string | `null` | Error message |
| `rows` | int | `3` | Number of rows |
| `required` | bool | `false` | Required indicator |
| `class` | string | `''` | Additional classes |

#### Usage

```blade
<x-ui.textarea
    name="description"
    label="Description"
    placeholder="Enter description..."
    :rows="5"
    wire:model="description"
/>
```

---

## Interactive Components

### `x-ui.modal`

**File:** [`modal.blade.php`](/resources/views/components/ui/modal.blade.php)

Dialog modal with Alpine.js, teleport, backdrop, keyboard dismiss, and focus trap.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | string | `'modal-'.uniqid()` | Unique modal ID |
| `title` | string | `null` | Modal title |
| `size` | string | `md` | `sm`, `md`, `lg`, `xl`, `full` |
| `submitLabel` | string | `null` | Submit button label |
| `cancelLabel` | string | `'Batal'` | Cancel button label |

#### Slots

| Slot | Description |
|------|-------------|
| `trigger` | Button/element to open modal |
| Default | Modal body content |
| `footer` | Custom footer actions |

#### Usage

```blade
<x-ui.modal title="Delete User" submitLabel="Delete">
    <x-slot:trigger>
        <x-ui.button variant="danger">Delete</x-ui.button>
    </x-slot:trigger>

    <p>Are you sure you want to delete this user?</p>

    <x-slot:footer>
        <x-ui.button variant="ghost" @click="open = false">Cancel</x-ui.button>
        <x-ui.button variant="danger" wire:click="delete">Delete</x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

#### Alpine State

| State | Type | Description |
|-------|------|-------------|
| `open` | Boolean | Modal visibility |
| `open = true` | — | Opens modal |
| `open = false` | — | Closes modal |

---

### `x-ui.dropdown`

**File:** [`dropdown.blade.php`](/resources/views/components/ui/dropdown.blade.php)

Dropdown menu with Alpine, keyboard navigation, and outside click dismiss.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `align` | string | `right` | `left`, `right` | Dropdown alignment |
| `width` | string | `'w-48'` | — | Width class |

#### Slots

| Slot | Description |
|------|-------------|
| `trigger` | Button to toggle dropdown |

#### Usage

```blade
<x-ui.dropdown>
    <x-slot:trigger>
        <x-ui.button variant="ghost">Menu</x-ui.button>
    </x-slot:trigger>

    <x-ui.dropdown-item href="{{ route('users.edit', $user) }}">
        <i class="fas fa-edit mr-2"></i>Edit
    </x-ui.dropdown-item>
    <x-ui.dropdown-divider />
    <x-ui.dropdown-item variant="danger" method="POST"
        :href="route('users.destroy', $user)">
        <i class="fas fa-trash mr-2"></i>Delete
    </x-ui.dropdown-item>
</x-ui.dropdown>
```

---

### `x-ui.tabs`

**File:** [`tabs.blade.php`](/resources/views/components/ui/tabs.blade.php)

Tabbed interface with Alpine.js, keyboard navigation, and accessible roles.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `tabs` | array | `[]` | Tab definitions: `[['id' => 'tab1', 'label' => 'Tab 1', 'icon' => null, 'badge' => null]]` |
| `defaultTab` | string | `null` | Default active tab ID |
| `activeTab` | string | `null` | Controlled active tab (for Livewire) |
| `variant` | string | `'underline'` | `underline`, `pills`, `buttons` |

#### Slots

| Slot | Description |
|------|-------------|
| `tab-{id}` | Content for each tab by ID |
| `tab-{id}-label` | Custom label for each tab |

#### Usage

```blade
<x-ui.tabs
    :tabs="[
        ['id' => 'overview', 'label' => 'Overview'],
        ['id' => 'details', 'label' => 'Details', 'badge' => 5],
        ['id' => 'history', 'label' => 'History', 'icon' => 'fa-solid fa-clock'],
    ]"
    defaultTab="overview"
>
    <x-slot:tab-overview>
        <p>Overview content</p>
    </x-slot:tab-overview>
    <x-slot:tab-details>
        <p>Details content</p>
    </x-slot:tab-details>
    <x-slot:tab-history>
        <p>History content</p>
    </x-slot:tab-history>
</x-ui.tabs>
```

---

### `x-ui.accordion`

**File:** [`accordion.blade.php`](/resources/views/components/ui/accordion.blade.php)

Collapsible accordion sections with Alpine.js.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `[]` | `[['title' => '...', 'content' => '...'], ...]` |
| `multiple` | bool | `false` | Allow multiple open sections |
| `class` | string | `''` | Additional classes |

#### Usage

```blade
<x-ui.accordion
    :items="[
        ['title' => 'Section 1', 'content' => 'Content 1'],
        ['title' => 'Section 2', 'content' => 'Content 2'],
    ]"
/>
```

---

### `x-ui.tooltip`

**File:** [`tooltip.blade.php`](/resources/views/components/ui/tooltip.blade.php)

Simple tooltip on hover.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `content` | string | `''` | Tooltip text |
| `position` | string | `'top'` | `top`, `bottom`, `left`, `right` |

#### Usage

```blade
<x-ui.tooltip content="Click to save">
    <x-ui.button>Save</x-ui.button>
</x-ui.tooltip>
```

---

### `x-ui.toast`

**File:** [`toast.blade.php`](/resources/views/components/ui/toast.blade.php)

Global toast notification system using Alpine.js `$dispatch`.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `position` | string | `top-right` | `top-right`, `top-left`, `bottom-right`, `bottom-left`, `top-center`, `bottom-center` | Position |
| `maxVisible` | int | `5` | — | Max visible toasts |
| `duration` | int | `3000` | — | Default duration in ms |

#### Alpine Events

| Event | Detail | Description |
|-------|--------|-------------|
| `@toast.window` | `{ message, type, duration }` | Show a toast notification |

#### Usage

```blade
{{-- In layout --}}
<x-ui.toast position="top-right" :maxVisible="5" />

{{-- Trigger from anywhere --}}
<button @click="$dispatch('toast', {
    message: 'Data saved successfully!',
    type: 'success',
    duration: 3000
})">
    Save
</button>
```

---

## Data Display

### `x-ui.table`

**File:** [`table.blade.php`](/resources/views/components/ui/table.blade.php)

Data table with columns, cell renderers, striped/hoverable variants, and empty state.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `columns` | array | `[]` | Column definitions: `[['key' => 'name', 'label' => 'Name', 'sortable' => true, 'align' => 'left']]` |
| `rows` | iterable | `[]` | Data rows |
| `striped` | bool | `false` | Alternating row colors |
| `hoverable` | bool | `false` | Hover highlight |
| `compact` | bool | `false` | Smaller padding |
| `showHeader` | bool | `true` | Show/hide header |
| `emptyMessage` | string | `'Tidak ada data.'` | Empty state message |

#### Slots

| Slot | Description |
|------|-------------|
| `cell-{key}` | Custom cell renderer: `{ row, column, index }` |
| `header-{key}` | Custom header renderer |

#### Usage

```blade
<x-ui.table
    :columns="[
        ['key' => 'name', 'label' => 'Name', 'sortable' => true],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'actions', 'label' => 'Actions'],
    ]"
    :rows="$users"
    :striped="true"
    :hoverable="true"
>
    <x-slot:cell-status="{ row }">
        <x-ui.badge :variant="$row->status === 'active' ? 'success' : 'warning'">
            {{ $row->status }}
        </x-ui.badge>
    </x-slot:cell-status>

    <x-slot:cell-actions="{ row }">
        <x-ui.button size="sm" wire:click="edit({{ $row->id }})">Edit</x-ui.button>
    </x-slot:cell-actions>
</x-ui.table>
```

---

### `x-ui.pagination`

**File:** [`pagination.blade.php`](/resources/views/components/ui/pagination.blade.php)

Pagination with page numbers, info text, and size variants.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `paginator` | LengthAwarePaginator | `null` | — | Laravel paginator instance |
| `variant` | string | `full` | `full`, `simple` | Full pagination or prev/next only |
| `size` | string | `md` | `sm`, `md` | Size |
| `showInfo` | bool | `true` | — | Show "Showing 1-10 of 100" text |

#### Usage

```blade
<x-ui.pagination :paginator="$users" variant="full" :showInfo="true" />
```

---

### `x-ui.stat-card`

**File:** [`stat-card.blade.php`](/resources/views/components/ui/stat-card.blade.php)

Statistics display card with label, value, icon, and trend indicator.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string | `''` | Card label |
| `value` | mixed | `''` | Main value |
| `icon` | string | `null` | Font Awesome icon class |
| `trend` | string | `null` | Trend direction: `up`, `down` |
| `trendValue` | string | `null` | Trend percentage/text |
| `color` | string | `'primary'` | Color theme |

#### Usage

```blade
<x-ui.stat-card
    label="Total Revenue"
    value="Rp 1.000.000"
    icon="fa-solid fa-dollar-sign"
    trend="up"
    trendValue="+12.5%"
    color="primary"
/>
```

---

### `x-ui.progress`

**File:** [`progress.blade.php`](/resources/views/components/ui/progress.blade.php)

Progress bar with label and value display.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | int | `0` | Progress value (0-100) |
| `label` | string | `null` | Label text |
| `showValue` | bool | `true` | Show percentage |
| `variant` | string | `'primary'` | Color variant |
| `size` | string | `'md'` | Bar thickness |

#### Usage

```blade
<x-ui.progress :value="75" label="Project Completion" />
```

---

### `x-ui.empty-state`

**File:** [`empty-state.blade.php`](/resources/views/components/ui/empty-state.blade.php)

Empty state placeholder with icon, title, and description.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `icon` | string | `null` | Font Awesome icon |
| `title` | string | `'Tidak ada data'` | Title text |
| `description` | string | `null` | Description text |

#### Usage

```blade
<x-ui.empty-state
    icon="fa-solid fa-inbox"
    title="No messages"
    description="You have no unread messages."
/>
```

---

### `x-ui.skeleton`

**File:** [`skeleton.blade.php`](/resources/views/components/ui/skeleton.blade.php)

Loading skeleton placeholder.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `variant` | string | `text` | `text`, `circle`, `rect`, `card` | Shape variant |
| `width` | string | `null` | — | Width (e.g., `w-32`) |
| `height` | string | `null` | — | Height (e.g., `h-8`) |
| `count` | int | `1` | — | Number of skeleton items |

#### Usage

```blade
<x-ui.skeleton variant="card" :count="3" />
<x-ui.skeleton variant="circle" width="w-12" height="h-12" />
<x-ui.skeleton variant="text" width="w-48" />
```

---

### `x-ui.avatar`

**File:** [`avatar.blade.php`](/resources/views/components/ui/avatar.blade.php)

User avatar with image or initials fallback.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `src` | string | `null` | Image URL |
| `alt` | string | `''` | Alt text |
| `size` | string | `'md'` | `sm`, `md`, `lg`, `xl` |
| `initials` | string | `null` | Fallback initials |

#### Usage

```blade
<x-ui.avatar src="{{ $user->avatar_url }}" alt="{{ $user->name }}" />
<x-ui.avatar initials="JD" size="lg" />
```

---

### `x-ui.breadcrumb`

**File:** [`breadcrumb.blade.php`](/resources/views/components/ui/breadcrumb.blade.php)

Breadcrumb navigation with automatic active state.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `[]` | `[['label' => 'Home', 'url' => '/'], ['label' => 'Users', 'url' => '/users']]` |

#### Usage

```blade
<x-ui.breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['label' => 'Users', 'url' => route('admin.users.index')],
    ['label' => $user->name],
]" />
```

---

## Form Components

### `x-ui.checkbox`

**File:** [`checkbox.blade.php`](/resources/views/components/ui/checkbox.blade.php)

Checkbox input with label.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `''` | Input name |
| `label` | string | `null` | Label text |
| `checked` | bool | `false` | Checked state |
| `value` | string | `'1'` | Input value |
| `error` | string | `null` | Error message |
| `class` | string | `''` | Additional classes |

#### Usage

```blade
<x-ui.checkbox name="agree" label="I agree to terms" wire:model="agree" />
```

---

### `x-ui.toggle`

**File:** [`toggle.blade.php`](/resources/views/components/ui/toggle.blade.php)

Toggle switch with Alpine.js.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `''` | Input name |
| `label` | string | `null` | Label text |
| `checked` | bool | `false` | Toggle state |
| `disabled` | bool | `false` | Disabled state |
| `class` | string | `''` | Additional classes |

#### Usage

```blade
<x-ui.toggle name="active" label="Active" wire:model="isActive" />
```

---

### `x-ui.radio-group`

**File:** [`radio-group.blade.php`](/resources/views/components/ui/radio-group.blade.php)

Radio button group.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `''` | Input name |
| `options` | array | `[]` | `[['value' => '1', 'label' => 'Option 1']]` or flat `['value' => 'Label']` |
| `selected` | string | `null` | Selected value |
| `label` | string | `null` | Group label |
| `class` | string | `''` | Additional classes |

#### Usage

```blade
<x-ui.radio-group
    name="gender"
    label="Gender"
    :options="['male' => 'Male', 'female' => 'Female']"
    wire:model="gender"
/>
```

---

### `x-ui.file-upload`

**File:** [`file-upload.blade.php`](/resources/views/components/ui/file-upload.blade.php)

File upload with drag-drop, preview, and Alpine state.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `''` | Input name |
| `label` | string | `null` | Label text |
| `accept` | string | `null` | Accepted MIME types |
| `multiple` | bool | `false` | Allow multiple files |
| `maxSize` | int | `2048` | Max file size in KB |
| `error` | string | `null` | Error message |
| `class` | string | `''` | Additional classes |

#### Usage

```blade
<x-ui.file-upload
    name="document"
    label="Upload Document"
    wire:model="document"
    accept=".pdf,.jpg,.png"
    :maxSize="2048"
/>
```

---

## Utility Components

### `x-ui.alert`

**File:** [`alert.blade.php`](/resources/views/components/ui/alert.blade.php)

Alert banner with dismiss and variants.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `variant` | string | `info` | `info`, `success`, `warning`, `danger` | Color variant |
| `title` | string | `null` | — | Alert title |
| `dismissible` | bool | `false` | — | Show close button |

#### Usage

```blade
<x-ui.alert variant="success" title="Success!">
    Data has been saved.
</x-ui.alert>

<x-ui.alert variant="danger" :dismissible="true">
    Please fix the errors above.
</x-ui.alert>
```

---

### `x-ui.button-spinner`

**File:** [`button-spinner.blade.php`](/resources/views/components/ui/button-spinner.blade.php)

Animated spinner for loading states inside buttons.

#### Props

| Prop | Type | Default | Values | Description |
|------|------|---------|--------|-------------|
| `size` | string | `md` | `sm`, `md`, `lg` | Spinner size |

#### Usage

```blade
<x-ui.button-spinner size="sm" />
```

---

### `x-ui.dropdown-item`

**File:** [`dropdown-item.blade.php`](/resources/views/components/ui/dropdown-item.blade.php)

Individual dropdown menu item.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `href` | string | `#` | Link URL |
| `variant` | string | `'default'` | `default`, `danger` |
| `disabled` | bool | `false` | Disabled state |
| `method` | string | `'GET'` | HTTP method (for forms) |
| `icon` | string | `null` | Font Awesome icon |

#### Usage

```blade
<x-ui.dropdown-item href="{{ route('users.edit', $user) }}" icon="fa-solid fa-edit">
    Edit
</x-ui.dropdown-item>
<x-ui.dropdown-item variant="danger" method="POST" :href="route('users.destroy', $user)">
    Delete
</x-ui.dropdown-item>
```

---

### `x-ui.dropdown-divider`

**File:** [`dropdown-divider.blade.php`](/resources/views/components/ui/dropdown-divider.blade.php)

Visual divider for dropdown menus.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `class` | string | `''` | Additional classes |

#### Usage

```blade
<x-ui.dropdown-divider />
```

---

## Usage Guidelines

### Naming Convention

```blade
{{-- ✅ Correct --}}
<x-ui.button variant="primary" size="lg">Submit</x-ui.button>
<x-ui.input name="email" label="Email" />
<x-ui.stat-card label="Revenue" value="Rp 1.000.000" />

{{-- ❌ Wrong --}}
<x-button primary large>Submit</x-button>      {{-- Too generic --}}
<x-input name="email" />                         {{-- Missing x-ui prefix --}}
```

### Dark Mode

Semua komponen mendukung dark mode via Tailwind `dark:` modifier:

```blade
{{-- Internal implementation --}}
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
```

### Accessibility Checklist

| Component | Roles & Attributes |
|-----------|-------------------|
| Button | `role="button"`, `aria-disabled` |
| Modal | `role="dialog"`, `aria-modal="true"`, `aria-labelledby`, Escape key |
| Dropdown | `role="menu"`, `role="menuitem"`, `aria-expanded`, keyboard nav |
| Tabs | `role="tablist"`, `role="tab"`, `role="tabpanel"`, `aria-selected` |
| Alert | `role="alert"` |
| Progress | `role="progressbar"`, `aria-valuenow`, `aria-valuemin`, `aria-valuemax` |
| Toggle | `role="switch"`, `aria-checked` |
| Tooltip | `role="tooltip"` |
| Breadcrumb | `aria-label="Breadcrumb"` |
| Toast | `role="status"`, `aria-live="polite"` |

### Livewire Compatibility

Semua komponen dengan metode `$attributes->merge()` mendukung `wire:*` attributes:

```blade
<x-ui.button wire:click="save" wire:loading.attr="disabled">
    Save
</x-ui.button>

<x-ui.input wire:model="email" />
<x-ui.select wire:model="category" :options="$categories" />
```

### Alpine.js Integration

Interactive components menggunakan Alpine.js directives:

```blade
{{-- Modal --}}
<div x-data="{ open: false }" @keydown.escape.window="open = false">
    <button @click="open = true">Open</button>
    <div x-show="open" @click.outside="open = false">
        ...
    </div>
</div>

{{-- Toast dispatch --}}
<button @click="$dispatch('toast', { message: 'Saved!', type: 'success' })">
    Save
</button>
```

---

## Migration Checklist

Use this checklist when migrating existing views to use UI components:

| Check | Description |
|-------|-------------|
| [ ] | Replace raw `<button>` with `<x-ui.button>` |
| [ ] | Replace raw `<div class="bg-white rounded-2xl...">` with `<x-ui.card>` |
| [ ] | Replace raw `<span class="px-2...">` status with `<x-ui.badge>` |
| [ ] | Replace raw `<input>` with `<x-ui.input>` |
| [ ] | Replace raw `<select>` with `<x-ui.select>` |
| [ ] | Replace raw `<textarea>` with `<x-ui.textarea>` |
| [ ] | Replace raw modal HTML with `<x-ui.modal>` |
| [ ] | Replace raw dropdown with `<x-ui.dropdown>` |
| [ ] | Replace raw tab logic with `<x-ui.tabs>` |
| [ ] | Replace raw table + loop with `<x-ui.table>` |
| [ ] | Replace raw pagination with `<x-ui.pagination>` |
| [ ] | Replace inline JS confirm with Alpine `@submit.prevent="if(confirm('...')) $el.submit()"` |
| [ ] | Replace inline JS event handlers with Alpine `@event` directives |
| [ ] | Replace hardcoded colors with design tokens / Tailwind utilities |
| [ ] | Verify dark mode support (`dark:` classes) |
| [ ] | Verify accessibility (roles, aria-*, keyboard) |

---

## Test Coverage

Refer to [`tests/Feature/BladeComponentTest.php`](/tests/Feature/BladeComponentTest.php) for the complete test suite covering all 28 components.

```bash
# Run all component tests
php artisan test --filter=BladeComponentTest

# Run specific component test
php artisan test --filter=test_button_renders_all_variants
```

---

*This documentation is auto-generated from component source code. Update this file when adding or modifying components.*

---

## Client Portal Components

> **Location:** `resources/views/client/components/`  
> **Stack:** Same Vite/Alpine/Tailwind pipeline. CSS in `resources/css/client.css`.  
> **Last Updated:** 2026-05-06 (Phase 4–6 complete)

### `client.components.status-badge`

**File:** [`client/components/status-badge.blade.php`](/resources/views/client/components/status-badge.blade.php)

Renders a colored pill badge for application/permit status values.

```blade
@include('client.components.status-badge', [
    'status' => 'active',    // 'active'|'pending'|'inactive'|'quoted'|'rejected'|'completed'
    'label'  => 'Aktif',     // display text
    'size'   => 'sm',        // 'xs'|'sm'|'md' (default 'sm')
])
```

CSS class pattern: `.status-badge.status-badge--{status}` defined in `client.css`.

---

### `client.components.empty-state`

**File:** [`client/components/empty-state.blade.php`](/resources/views/client/components/empty-state.blade.php)

Full-page or in-card empty state with icon, title, message, and optional CTA.

```blade
@include('client.components.empty-state', [
    'icon'          => 'fa-inbox',          // Font Awesome class (required)
    'title'         => 'Belum Ada Data',    // required
    'message'       => 'Penjelasan...',     // optional
    'ctaLabel'      => 'Tambah Sekarang',   // optional
    'ctaHref'       => route('...'),        // optional
    'ctaIcon'       => 'fa-plus',           // optional, default fa-arrow-right
    'secondary'     => true,                // optional: show secondary button
    'secondaryLabel'=> 'Reset',             // optional
    'secondaryHref' => route('...'),        // optional
    'size'          => 'md',                // 'sm'|'md'|'lg'
    'color'         => 'blue',              // 'gray'|'blue'|'green'|'amber'
])
```

---

### CSS Patterns (client.css)

| Class | Purpose |
|-------|---------|
| `.client-hero` | Full-width hero bar with `--client-primary` background |
| `.card-hover` | translateY(-2px) + shadow on hover |
| `.toast-animate` | Slide-in animation for toast notifications |
| `.sr-only` | Screen-reader-only visually hidden element |
| `.status-badge--{status}` | Semantic status pill colors |
| `.client-card` / `.client-card-header` / `.client-card-body` | Card layout primitives |

### Design Tokens (client.css variables)

| Token | Value |
|-------|-------|
| `--client-primary` | `#0a66c2` |
| `--client-primary-hover` | `#004182` |
| `--surface` / `--surface-card` | white / #f8fafc |
| `--text-primary` / `--text-secondary` | #111827 / #374151 |
| `--shadow-sm` / `--shadow-md` / `--shadow-lg` | Layered shadows |
| `--radius-sm` / `--radius-md` / `--radius-lg` | Border radius scale |
| `--transition-fast` / `--transition-normal` | 150ms / 200ms ease |

### Accessibility Notes

- All dynamic regions use `aria-live="polite"` (or `"assertive"` for errors)
- Global `#a11y-announcer` and `#a11y-alerter` elements in `client/layouts/app.blade.php`
- `:focus-visible` ring set to `2px solid #0a66c2` in layout inline styles
- `prefers-reduced-motion` respected for all CSS animations
- `prefers-contrast: more` increases font weight and outline width

