@props([
    'columns' => [],            // [['key' => 'name', 'label' => 'Name', 'sortable' => true, 'align' => 'left'], ...]
    'rows' => [],               // Collection or array of objects
    'variant' => 'default',     // default | compact
    'striped' => false,
    'hoverable' => true,
    'sortable' => false,
    'sortField' => null,
    'sortDirection' => 'asc',
    'emptyMessage' => 'Tidak ada data',
    'showHeader' => true,
    'class' => '',
    'wrapperClass' => '',
])

@php
    $tableClasses = 'w-full text-left border-collapse';

    $variantClasses = [
        'default' => [
            'th' => 'px-4 py-3 text-xs font-semibold uppercase tracking-wider',
            'td' => 'px-4 py-4',
        ],
        'compact' => [
            'th' => 'px-3 py-2 text-xs font-semibold uppercase tracking-wider',
            'td' => 'px-3 py-3',
        ],
    ];

    $stripedClass = $striped ? 'even:bg-gray-50 dark:even:bg-gray-800/50' : '';
    $hoverableClass = $hoverable ? 'hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors duration-150' : '';
@endphp

<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 {{ $wrapperClass }}">
    <table class="{{ $tableClasses }} {{ $class }}">
        @if($showHeader && count($columns) > 0)
            <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    @foreach($columns as $column)
                        <th
                            scope="col"
                            class="{{ $variantClasses[$variant]['th'] }} text-{{ $column['align'] ?? 'left' }} {{ $column['class'] ?? '' }}"
                        >
                            @if($sortable && ($column['sortable'] ?? true))
                                <button
                                    type="button"
                                    @click="$dispatch('sort', { field: '{{ $column['key'] }}' })"
                                    class="group inline-flex items-center gap-1 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-150"
                                >
                                    <span>{{ $column['label'] }}</span>
                                    <span class="flex flex-col shrink-0">
                                        <svg class="w-2.5 h-2.5 {{ $sortField === $column['key'] && $sortDirection === 'asc' ? 'text-[var(--color-primary)]' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"/>
                                        </svg>
                                        <svg class="w-2.5 h-2.5 -mt-1 {{ $sortField === $column['key'] && $sortDirection === 'desc' ? 'text-[var(--color-primary)]' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    </span>
                                </button>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">{{ $column['label'] }}</span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
            @forelse($rows as $row)
                <tr class="{{ $stripedClass }} {{ $hoverableClass }}">
                    @foreach($columns as $column)
                        <td class="{{ $variantClasses[$variant]['td'] }} text-{{ $column['align'] ?? 'left' }} {{ $column['cellClass'] ?? '' }}">
                            {{-- Check for scoped slot: cell-{key} --}}
                            @php
                                $cellSlotName = 'cell-' . $column['key'];
                            @endphp

                            @if(isset(${$cellSlotName}))
                                {{ ${$cellSlotName}($row) }}
                            @elseif(isset($column['slot']))
                                {{ ${$column['slot']}($row) }}
                            @elseif(isset($column['component']))
                                <x-dynamic-component
                                    :component="$column['component']"
                                    :row="$row"
                                    :value="data_get($row, $column['key'])"
                                />
                            @else
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ data_get($row, $column['key']) }}
                                </span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td
                        colspan="{{ count($columns) }}"
                        class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
                    >
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endforelse
        </tbody>

        @isset($tfoot)
            <tfoot class="bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                <tr>
                    @foreach($columns as $column)
                        <td class="{{ $variantClasses[$variant]['td'] }} text-{{ $column['align'] ?? 'left' }}">
                            @php
                                $footerSlotName = 'footer-' . $column['key'];
                            @endphp
                            @if(isset(${$footerSlotName}))
                                {{ ${$footerSlotName}($rows) }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tfoot>
        @endisset
    </table>
</div>
