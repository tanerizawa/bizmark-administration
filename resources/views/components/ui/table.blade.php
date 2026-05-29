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
    'cellRenderers' => [],      // ['column_key' => fn($row) => 'rendered HTML', ...]
    'footerRenderers' => [],    // ['footer-column_key' => fn($rows) => 'rendered HTML', ...]
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

    $stripedClass = $striped ? 'striped-row' : '';
    $hoverableClass = $hoverable ? 'hoverable-row' : '';
@endphp

<div class="overflow-x-auto rounded-none {{ $wrapperClass }}">
    <table class="{{ $tableClasses }} {{ $class }}">
        @if($showHeader && count($columns) > 0)
            <thead class="bg-[var(--dark-bg-tertiary)] border-b border-[var(--dark-separator)]">
                <tr>
                    @foreach($columns as $column)
                        <th
                            scope="col"
                            class="{{ $variantClasses[$variant]['th'] }} text-{{ $column['align'] ?? 'left' }} {{ $column['class'] ?? '' }} text-[var(--dark-text-secondary)] font-semibold tracking-[.06em]"
                        >
                            @if($sortable && ($column['sortable'] ?? true))
                                <button
                                    type="button"
                                    @click="$dispatch('sort', { field: '{{ $column['key'] }}' })"
                                    @mouseenter="$event.currentTarget.style.color='var(--dark-text-primary)'"
                                    @mouseleave="$event.currentTarget.style.color='var(--dark-text-secondary)'"
                                    class="inline-flex items-center gap-1 bg-transparent border-none cursor-pointer text-[var(--dark-text-secondary)] text-inherit font-inherit p-0"
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
                                <span class="text-[var(--dark-text-secondary)]">{{ $column['label'] }}</span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="bg-transparent table-body-rows">
            @forelse($rows as $row)
                <tr class="{{ $stripedClass }} {{ $hoverableClass }}">
                    @foreach($columns as $column)
                        <td class="{{ $variantClasses[$variant]['td'] }} text-{{ $column['align'] ?? 'left' }} {{ $column['cellClass'] ?? '' }}">
                            @php
                                $cellKey = $column['key'] ?? '';
                            @endphp

                            @if(isset($cellRenderers[$cellKey]) && is_callable($cellRenderers[$cellKey]))
                                {!! $cellRenderers[$cellKey]($row) !!}
                            @elseif(isset($column['render']) && is_callable($column['render']))
                                {!! $column['render']($row) !!}
                            @elseif(isset($column['component']))
                                <x-dynamic-component
                                    :component="$column['component']"
                                    :row="$row"
                                    :value="data_get($row, $column['key'])"
                                />
                            @elseif(isset($column['template']))
                                @php
                                    $templateContext = array_merge(
                                        ['row' => $row],
                                        $column['templateData'] ?? []
                                    );
                                @endphp
                                {!! \Illuminate\Support\Facades\Blade::render($column['template'], $templateContext) !!}
                            @else
                                <span class="text-[0.85rem] text-[var(--dark-text-primary)]">
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
                        class="py-12 px-4 text-center text-sm text-[var(--dark-text-secondary)]"
                    >
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endforelse
        </tbody>

        @isset($tfoot)
            <tfoot class="bg-[var(--dark-bg-tertiary)] border-t border-[var(--dark-separator)]">
                <tr>
                    @foreach($columns as $column)
                        <td class="{{ $variantClasses[$variant]['td'] }} text-{{ $column['align'] ?? 'left' }}">
                            @php
                                $footerSlotName = 'footer-' . $column['key'];
                            @endphp
                            @if(isset($footerRenderers[$footerSlotName]) && is_callable($footerRenderers[$footerSlotName]))
                                {{ $footerRenderers[$footerSlotName]($rows) }}
                            @elseif(isset(${$footerSlotName}))
                                {{ ${$footerSlotName}($rows) }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tfoot>
        @endisset
    </table>
</div>
@once
<style>
.table-body-rows tr { border-bottom: 1px solid var(--dark-separator); }
.table-body-rows tr:last-child { border-bottom: none; }
.table-body-rows tr.striped-row:nth-child(even) { background: color-mix(in srgb, var(--dark-bg-tertiary) 60%, transparent); }
.table-body-rows tr.hoverable-row:hover { background: color-mix(in srgb, var(--apple-blue) 5%, var(--dark-bg-tertiary)); cursor: default; }
</style>
@endonce
