@extends('layouts.tenant')

@section('title', 'Detail Laporan')

@section('content')
    <x-ui.page-header :title="$maintenance->title" subtitle="Detail laporan kerusakan">
        <x-ui.button :href="route('tenant.maintenances.index')" variant="secondary">
            <x-icon name="chevron-right" :size="16" class="rotate-180" />
            Kembali
        </x-ui.button>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Detail Laporan --}}
        <x-ui.card title="Informasi Laporan" class="lg:col-span-2">
            <div class="flex items-start justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge :status="$maintenance->status" />
                    <x-ui.badge :status="$maintenance->priority" />
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Dilaporkan</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ $maintenance->created_at->format('d F Y H:i') }}</p>
                </div>
            </div>

            <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-500">Kamar</dt>
                    <dd class="mt-1 font-medium text-slate-800">
                        {{ $maintenance->room?->room_number }}
                        · {{ $maintenance->room?->property?->name }}
                    </dd>
                </div>
                @if ($maintenance->resolved_at)
                    <div>
                        <dt class="text-slate-500">Diselesaikan</dt>
                        <dd class="mt-1 font-medium text-emerald-600">{{ $maintenance->resolved_at->format('d F Y H:i') }}</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Deskripsi</p>
                <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-700 leading-relaxed">{{ $maintenance->description }}</p>
            </div>

            @if ($maintenance->admin_notes)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Catatan Admin</p>
                    <p class="rounded-lg bg-emerald-50 p-4 text-sm text-emerald-900">{{ $maintenance->admin_notes }}</p>
                </div>
            @endif
        </x-ui.card>

        {{-- Foto --}}
        <x-ui.card title="Foto Kerusakan">
            @if ($maintenance->photo)
                <div x-data="{ open: false }">
                    <div class="relative overflow-hidden rounded-lg border border-slate-200 group">
                        <img src="{{ asset('storage/' . $maintenance->photo) }}"
                             alt="Foto Kerusakan"
                             class="w-full h-auto object-cover cursor-zoom-in transition group-hover:scale-105"
                             @click="open = true">
                        <button type="button"
                                class="absolute bottom-2 right-2 rounded-lg bg-slate-900/80 px-3 py-1.5 text-xs font-medium text-white backdrop-blur"
                                @click="open = true">
                            <x-icon name="eye" :size="14" class="inline" />
                            Perbesar
                        </button>
                    </div>

                    <div x-show="open" x-cloak @click="open = false"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/90 p-4">
                        <div @click.stop class="max-h-[90vh] max-w-4xl overflow-auto">
                            <img src="{{ asset('storage/' . $maintenance->photo) }}" alt="Foto Kerusakan" class="max-w-full h-auto rounded-lg">
                        </div>
                        <button type="button" @click="open = false"
                                class="absolute top-4 right-4 rounded-full bg-white/10 p-2 text-white hover:bg-white/20">
                            <x-icon name="x" :size="24" />
                        </button>
                    </div>
                </div>
            @else
                <div class="py-8 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 mb-3">
                        <x-icon name="file-text" :size="24" class="text-slate-400" />
                    </div>
                    <p class="text-sm text-slate-500">Tidak ada foto</p>
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection