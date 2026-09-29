{{-- Jika ada href -> render <a>, kalau tidak -> <button> --}}
@props(['href' => null, 'variant' => 'primary', 'size' => 'md', 'type' => 'button'])

@php
    $variants = [
        'primary'      => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
        'secondary'    => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:ring-indigo-500',
        'danger'       => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'ghost'        => 'text-indigo-600 hover:text-indigo-800 hover:underline',
        'ghost-danger' => 'text-red-600 hover:text-red-800 hover:underline',
    ];
    $sizes = [
        'sm'   => 'px-3 py-1.5 text-xs',
        'md'   => 'px-4 py-2 text-sm',
        'link' => 'text-sm',
    ];
    $classes = 'inline-flex items-center justify-center gap-1 rounded-lg font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2 '
        . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
