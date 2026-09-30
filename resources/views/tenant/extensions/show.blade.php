@extends('layouts.tenant')

@section('title', 'Detail Perpanjangan')

@section('content')
    <x-ui.page-header title="Detail Perpanjangan" subtitle="Pengajuan perpanjangan sewa">
        <x-ui.button :href="route('tenant.extensions.index')" variant="secondary">
            <x-icon name="chevron-right" :size="16" class="rotate-180" />
            Kembali
        </x-ui.button>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Detail --}}
        <x-ui.card title="Detail Pengajuan" class="lg:col-span-2">
            <div class="flex items-start justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge :status="$extension->status" />
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Diajukan</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ $extension->created_at->format('d F Y H:i') }}</p>
                </div>
            </div>

            {{-- Perbandingan tanggal --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">Berakhir Saat Ini</p>
                    <p class="mt-1 font-semibold text-slate-800">{{ $extension->current_end_date->format('d F Y') }}</p>
                </div>
                <div class="rounded-lg bg-emerald-50 p-4">
                    <p class="text-xs text-emerald-600">Diajukan Sampai</p>
                    <p class="mt-1 font-semibold text-emerald-700">{{ $extension->requested_end_date->format('d F Y') }}</p>
                </div>
                <div class="rounded-lg bg-indigo-50 p-4">
                    <p class="text-xs text-indigo-600">Durasi</p>
                    <p class="mt-1 font-semibold text-indigo-700">{{ $extension->duration_months }} bulan</p>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-amber-50 p-4">
                <p class="text-xs text-amber-600 uppercase tracking-wide">Biaya Tambahan</p>
                <p class="mt-1 text-2xl font-bold text-amber-700">Rp {{ number_format($extension->additional_cost, 0, ',', '.') }}</p>
            </div>

            @if ($extension->notes)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Catatan Anda</p>
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-700">{{ $extension->notes }}</p>
                </div>
            @endif

            @if ($extension->admin_notes)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Catatan Admin</p>
                    <p class="rounded-lg bg-emerald-50 p-4 text-sm text-emerald-900">{{ $extension->admin_notes }}</p>
                </div>
            @endif

            @if ($extension->approved_at)
                <div class="mt-4 text-xs text-slate-500">
                    Diproses: {{ $extension->approved_at->format('d F Y H:i') }}
                </div>
            @endif
        </x-ui.card>

        {{-- Status --}}
        <x-ui.card title="Status">
            <div class="py-4 text-center">
                <x-ui.badge :status="$extension->status" />
                <p class="mt-3 text-sm text-slate-500">
                    @if ($extension->status === 'pending')
                        Menunggu persetujuan admin.
                    @elseif ($extension->status === 'approved')
                        Disetujui! Kontrak sewa sudah diperpanjang.
                    @else
                        Pengajuan ditolak.
                    @endif
                </p>
            </div>
        </x-ui.card>
    </div>
@endsection