@props(['type' => 'info'])

@php
    $styles = [
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error'   => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info'    => 'border-indigo-200 bg-indigo-50 text-indigo-800',
    ];
@endphp

<div x-data="{ show: true }" x-show="show" role="alert"
     {{ $attributes->merge(['class' => 'flex items-start justify-between gap-3 rounded-lg border px-4 py-3 text-sm ' . ($styles[$type] ?? $styles['info'])]) }}>
    <div class="break-words">{{ $slot }}</div>
    <button type="button" @click="show = false" class="text-lg leading-none opacity-60 hover:opacity-100" aria-label="Tutup">&times;</button>
</div>
