@props([
    'label',
    'value',
    'icon' => 'layout-dashboard',
    'color' => 'indigo',
    'trend' => null,
])

@php
    $colors = [
        'indigo'  => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-600',  'gradient' => 'from-indigo-500 to-indigo-700'],
        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'gradient' => 'from-emerald-500 to-emerald-700'],
        'green'   => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'gradient' => 'from-emerald-500 to-emerald-700'],
        'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-600',   'gradient' => 'from-amber-500 to-amber-700'],
        'violet'  => ['bg' => 'bg-violet-50',  'text' => 'text-violet-600',  'gradient' => 'from-violet-500 to-violet-700'],
        'rose'    => ['bg' => 'bg-rose-50',    'text' => 'text-rose-600',    'gradient' => 'from-rose-500 to-rose-700'],
        'slate'   => ['bg' => 'bg-slate-100',  'text' => 'text-slate-600',   'gradient' => 'from-slate-500 to-slate-700'],
    ];
    $c = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</p>
            <p class="mt-2 truncate text-2xl font-bold text-slate-800">{{ $value }}</p>

            @if ($trend !== null)
                <div class="mt-2 flex items-center gap-1">
                    @if ($trend >= 0)
                        <x-icon name="trending-up" :size="14" class="text-emerald-500" />
                        <span class="text-xs font-semibold text-emerald-600">+{{ number_format($trend, 1) }}%</span>
                    @else
                        <x-icon name="trending-down" :size="14" class="text-rose-500" />
                        <span class="text-xs font-semibold text-rose-600">{{ number_format($trend, 1) }}%</span>
                    @endif
                    <span class="text-xs text-slate-400">vs bulan lalu</span>
                </div>
            @endif
        </div>

        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $c['gradient'] }} shadow-lg shadow-slate-200/50">
            <x-icon :name="$icon" :size="20" class="text-white" />
        </div>
    </div>

    {{-- Decorative circle --}}
    <div class="absolute -right-6 -bottom-6 h-20 w-20 rounded-full {{ $c['bg'] }} opacity-50 transition-transform duration-300 group-hover:scale-125"></div>
</div>