{{-- Financial Settings Tab --}}
<div>
    <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Financial Settings</h3>

    <div class="space-y-6">
        {{-- Expense Categories --}}
        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h4 class="text-base font-medium" style="color: #FFFFFF;">
                        <i class="fas fa-tag mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                        Expense Categories
                    </h4>
                    <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.6);">
                        Kelola kategori pengeluaran yang digunakan saat mencatat biaya proyek.
                    </p>
                </div>
                <span class="px-2 py-1 text-xs rounded-apple" style="background: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 0.9);">
                    {{ $expenseCategories->count() }} categories
                </span>
            </div>

            <form method="POST" action="{{ route('settings.financial.expense-categories.store') }}" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
                @csrf
                <input type="text" name="slug" class="input-apple" placeholder="Slug (ex: communication)" value="{{ old('slug') }}" required>
                <input type="text" name="name" class="input-apple" placeholder="Display name" value="{{ old('name') }}" required>
                <input type="text" name="group" class="input-apple" placeholder="Group" value="{{ old('group') }}">
                <input type="text" name="icon" class="input-apple" placeholder="Icon (fa-name)" value="{{ old('icon') }}" required>
                <div class="flex items-center space-x-2">
                    <input type="number" name="sort_order" class="input-apple" placeholder="Order" value="{{ old('sort_order', 0) }}">
                    <label class="flex items-center space-x-1">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Active</span>
                    </label>
                </div>
                <div class="md:col-span-5 flex items-center justify-between">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Set as default</span>
                    </label>
                    <button type="submit" class="px-3 py-2 rounded-apple text-xs font-medium" style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                        <i class="fas fa-plus mr-1"></i>Add Category
                    </button>
                </div>
                @if($errors->has('slug') || $errors->has('name') || $errors->has('icon'))
                    <div class="md:col-span-5 text-xs" style="color: rgba(255, 69, 58, 0.9);">
                        @error('slug')<div>{{ $message }}</div>@enderror
                        @error('name')<div>{{ $message }}</div>@enderror
                        @error('icon')<div>{{ $message }}</div>@enderror
                    </div>
                @endif
            </form>

            <div class="divide-y divide-gray-800/40">
                @forelse($expenseCategories as $category)
                    <div class="py-3">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full" style="background: rgba(0, 122, 255, 0.12); color: rgba(0, 122, 255, 1);">
                                    <i class="fas fa-{{ $category->icon ?? 'layer-group' }}"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-medium" style="color: #FFFFFF;">{{ $category->name }}</p>
                                    <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">{{ $category->slug }} &mdash; {{ $category->group ?? 'Ungrouped' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 mt-2 md:mt-0">
                                @if($category->is_default)
                                    <span class="px-2 py-0.5 text-xs rounded-apple" style="background: rgba(52, 199, 89, 0.15); color: rgba(52, 199, 89, 1);">Default</span>
                                @endif
                                <span class="px-2 py-0.5 text-xs rounded-apple" style="background: {{ $category->is_active ? 'rgba(0, 122, 255, 0.15)' : 'rgba(142, 142, 147, 0.2)' }}; color: {{ $category->is_active ? 'rgba(0, 122, 255, 1)' : 'rgba(142, 142, 147, 0.9)' }};">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <form method="POST" action="{{ route('settings.financial.expense-categories.delete', $category) }}" onsubmit="return confirm('Hapus kategori {{ $category->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded-apple text-xs" style="background: rgba(255, 59, 48, 0.15); color: rgba(255, 59, 48, 1);" {{ $category->is_default ? 'disabled' : '' }}>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <details class="mt-3">
                            <summary class="text-xs cursor-pointer" style="color: rgba(235, 235, 245, 0.6);">Edit details</summary>
                            <form method="POST" action="{{ route('settings.financial.expense-categories.update', $category) }}" class="mt-3 grid grid-cols-1 md:grid-cols-5 gap-3">
                                @csrf
                                @method('PUT')
                                <input type="text" name="slug" class="input-apple" value="{{ old('slug', $category->slug) }}" required>
                                <input type="text" name="name" class="input-apple" value="{{ old('name', $category->name) }}" required>
                                <input type="text" name="group" class="input-apple" value="{{ old('group', $category->group) }}">
                                <input type="text" name="icon" class="input-apple" value="{{ old('icon', $category->icon) }}" required>
                                <div class="flex items-center space-x-2">
                                    <input type="number" name="sort_order" class="input-apple" value="{{ old('sort_order', $category->sort_order) }}">
                                    <label class="flex items-center space-x-1">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Active</span>
                                    </label>
                                </div>
                                <div class="md:col-span-5 flex items-center justify-between">
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $category->is_default) ? 'checked' : '' }}>
                                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Set as default</span>
                                    </label>
                                    <button type="submit" class="px-3 py-2 rounded-apple text-xs font-medium" style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                                        <i class="fas fa-save mr-1"></i>Update
                                    </button>
                                </div>
                            </form>
                        </details>
                    </div>
                @empty
                    <div class="text-center py-6" style="color: rgba(235, 235, 245, 0.6);">
                        Belum ada kategori pengeluaran.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Payment Methods --}}
        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h4 class="text-base font-medium" style="color: #FFFFFF;">
                        <i class="fas fa-credit-card mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                        Payment Methods
                    </h4>
                    <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.6);">
                        Tentukan metode pembayaran yang tersedia saat mencatat pemasukan dan pengeluaran.
                    </p>
                </div>
                <span class="px-2 py-1 text-xs rounded-apple" style="background: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 0.9);">
                    {{ $paymentMethods->count() }} methods
                </span>
            </div>

            <form method="POST" action="{{ route('settings.financial.payment-methods.store') }}" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
                @csrf
                <input type="text" name="code" class="input-apple" placeholder="Code (ex: bank_transfer)" value="{{ old('code') }}" required>
                <input type="text" name="name" class="input-apple" placeholder="Display name" value="{{ old('name') }}" required>
                <input type="text" name="description" class="input-apple md:col-span-2" placeholder="Description" value="{{ old('description') }}">
                <div class="flex items-center space-x-3">
                    <label class="flex items-center space-x-1">
                        <input type="checkbox" name="requires_cash_account" value="1" {{ old('requires_cash_account', true) ? 'checked' : '' }}>
                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Needs cash account</span>
                    </label>
                    <input type="number" name="sort_order" class="input-apple" placeholder="Order" value="{{ old('sort_order', 0) }}">
                </div>
                <div class="md:col-span-5 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <label class="flex items-center space-x-1">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Active</span>
                        </label>
                        <label class="flex items-center space-x-1">
                            <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Default</span>
                        </label>
                    </div>
                    <button type="submit" class="px-3 py-2 rounded-apple text-xs font-medium" style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                        <i class="fas fa-plus mr-1"></i>Add Method
                    </button>
                </div>
                @if($errors->has('code') || $errors->has('name'))
                    <div class="md:col-span-5 text-xs" style="color: rgba(255, 69, 58, 0.9);">
                        @error('code')<div>{{ $message }}</div>@enderror
                        @error('name')<div>{{ $message }}</div>@enderror
                    </div>
                @endif
            </form>

            <div class="divide-y divide-gray-800/40">
                @forelse($paymentMethods as $method)
                    <div class="py-3">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-medium" style="color: #FFFFFF;">{{ $method->name }}</p>
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">{{ $method->code }} &mdash; {{ $method->description ?? 'No description' }}</p>
                            </div>
                            <div class="flex items-center space-x-2 mt-2 md:mt-0">
                                @if($method->is_default)
                                    <span class="px-2 py-0.5 text-xs rounded-apple" style="background: rgba(52, 199, 89, 0.15); color: rgba(52, 199, 89, 1);">Default</span>
                                @endif
                                <span class="px-2 py-0.5 text-xs rounded-apple" style="background: {{ $method->is_active ? 'rgba(0, 122, 255, 0.15)' : 'rgba(142, 142, 147, 0.2)' }}; color: {{ $method->is_active ? 'rgba(0, 122, 255, 1)' : 'rgba(142, 142, 147, 0.9)' }};">
                                    {{ $method->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <form method="POST" action="{{ route('settings.financial.payment-methods.delete', $method) }}" onsubmit="return confirm('Hapus metode {{ $method->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded-apple text-xs" style="background: rgba(255, 59, 48, 0.15); color: rgba(255, 59, 48, 1);" {{ $method->is_default ? 'disabled' : '' }}>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <details class="mt-3">
                            <summary class="text-xs cursor-pointer" style="color: rgba(235, 235, 245, 0.6);">Edit details</summary>
                            <form method="POST" action="{{ route('settings.financial.payment-methods.update', $method) }}" class="mt-3 grid grid-cols-1 md:grid-cols-5 gap-3">
                                @csrf
                                @method('PUT')
                                <input type="text" name="code" class="input-apple" value="{{ old('code', $method->code) }}" required>
                                <input type="text" name="name" class="input-apple" value="{{ old('name', $method->name) }}" required>
                                <input type="text" name="description" class="input-apple md:col-span-2" value="{{ old('description', $method->description) }}">
                                <div class="flex items-center space-x-3">
                                    <label class="flex items-center space-x-1">
                                        <input type="checkbox" name="requires_cash_account" value="1" {{ old('requires_cash_account', $method->requires_cash_account) ? 'checked' : '' }}>
                                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Needs cash account</span>
                                    </label>
                                    <input type="number" name="sort_order" class="input-apple" value="{{ old('sort_order', $method->sort_order) }}">
                                </div>
                                <div class="md:col-span-5 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <label class="flex items-center space-x-1">
                                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $method->is_active) ? 'checked' : '' }}>
                                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Active</span>
                                        </label>
                                        <label class="flex items-center space-x-1">
                                            <input type="checkbox" name="is_default" value="1" {{ old('is_default', $method->is_default) ? 'checked' : '' }}>
                                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Default</span>
                                        </label>
                                    </div>
                                    <button type="submit" class="px-3 py-2 rounded-apple text-xs font-medium" style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                                        <i class="fas fa-save mr-1"></i>Update
                                    </button>
                                </div>
                            </form>
                        </details>
                    </div>
                @empty
                    <div class="text-center py-6" style="color: rgba(235, 235, 245, 0.6);">
                        Belum ada metode pembayaran.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Tax Rates --}}
        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h4 class="text-base font-medium" style="color: #FFFFFF;">
                        <i class="fas fa-percent mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                        Tax Rates
                    </h4>
                    <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.6);">
                        Kelola tarif pajak yang digunakan saat membuat invoice dan laporan.
                    </p>
                </div>
                <span class="px-2 py-1 text-xs rounded-apple" style="background: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 0.9);">
                    {{ $taxRates->count() }} rates
                </span>
            </div>

            <form method="POST" action="{{ route('settings.financial.tax-rates.store') }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                @csrf
                <input type="text" name="name" class="input-apple md:col-span-2" placeholder="Rate name (ex: PPN 11%)" value="{{ old('name') }}" required>
                <input type="number" step="0.01" name="rate" class="input-apple" placeholder="Rate %" value="{{ old('rate', 11) }}" required>
                <input type="text" name="description" class="input-apple" placeholder="Description" value="{{ old('description') }}">
                <div class="md:col-span-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <label class="flex items-center space-x-1">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Active</span>
                        </label>
                        <label class="flex items-center space-x-1">
                            <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Default</span>
                        </label>
                        <input type="number" name="sort_order" class="input-apple w-24" placeholder="Order" value="{{ old('sort_order', 0) }}">
                    </div>
                    <button type="submit" class="px-3 py-2 rounded-apple text-xs font-medium" style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                        <i class="fas fa-plus mr-1"></i>Add Rate
                    </button>
                </div>
                @if($errors->has('name') || $errors->has('rate'))
                    <div class="md:col-span-4 text-xs" style="color: rgba(255, 69, 58, 0.9);">
                        @error('name')<div>{{ $message }}</div>@enderror
                        @error('rate')<div>{{ $message }}</div>@enderror
                    </div>
                @endif
            </form>

            <div class="divide-y divide-gray-800/40">
                @forelse($taxRates as $rate)
                    <div class="py-3">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-medium" style="color: #FFFFFF;">{{ $rate->name }}</p>
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">{{ number_format($rate->rate, 2) }}% &mdash; {{ $rate->description ?? 'No description' }}</p>
                            </div>
                            <div class="flex items-center space-x-2 mt-2 md:mt-0">
                                @if($rate->is_default)
                                    <span class="px-2 py-0.5 text-xs rounded-apple" style="background: rgba(52, 199, 89, 0.15); color: rgba(52, 199, 89, 1);">Default</span>
                                @endif
                                <span class="px-2 py-0.5 text-xs rounded-apple" style="background: {{ $rate->is_active ? 'rgba(0, 122, 255, 0.15)' : 'rgba(142, 142, 147, 0.2)' }}; color: {{ $rate->is_active ? 'rgba(0, 122, 255, 1)' : 'rgba(142, 142, 147, 0.9)' }};">
                                    {{ $rate->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <form method="POST" action="{{ route('settings.financial.tax-rates.delete', $rate) }}" onsubmit="return confirm('Hapus tarif {{ $rate->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded-apple text-xs" style="background: rgba(255, 59, 48, 0.15); color: rgba(255, 59, 48, 1);" {{ $rate->is_default ? 'disabled' : '' }}>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <details class="mt-3">
                            <summary class="text-xs cursor-pointer" style="color: rgba(235, 235, 245, 0.6);">Edit details</summary>
                            <form method="POST" action="{{ route('settings.financial.tax-rates.update', $rate) }}" class="mt-3 grid grid-cols-1 md:grid-cols-4 gap-3">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" class="input-apple md:col-span-2" value="{{ old('name', $rate->name) }}" required>
                                <input type="number" step="0.01" name="rate" class="input-apple" value="{{ old('rate', $rate->rate) }}" required>
                                <input type="text" name="description" class="input-apple" value="{{ old('description', $rate->description) }}">
                                <div class="md:col-span-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <label class="flex items-center space-x-1">
                                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $rate->is_active) ? 'checked' : '' }}>
                                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Active</span>
                                        </label>
                                        <label class="flex items-center space-x-1">
                                            <input type="checkbox" name="is_default" value="1" {{ old('is_default', $rate->is_default) ? 'checked' : '' }}>
                                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Default</span>
                                        </label>
                                        <input type="number" name="sort_order" class="input-apple w-24" value="{{ old('sort_order', $rate->sort_order) }}">
                                    </div>
                                    <button type="submit" class="px-3 py-2 rounded-apple text-xs font-medium" style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                                        <i class="fas fa-save mr-1"></i>Update
                                    </button>
                                </div>
                            </form>
                        </details>
                    </div>
                @empty
                    <div class="text-center py-6" style="color: rgba(235, 235, 245, 0.6);">
                        Belum ada tarif pajak.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
