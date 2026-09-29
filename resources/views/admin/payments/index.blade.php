@extends('layouts.admin')

@section('title', 'Pembayaran')

@section('content')
    <x-ui.page-header title="Pembayaran" subtitle="Verifikasi pembayaran dari penghuni" />

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-amber-700">Menunggu Verifikasi</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white">
                    <x-icon name="clock" :size="16" class="text-amber-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-amber-700">{{ $stats['pending_count'] }}</p>
            <p class="mt-1 text-xs text-amber-600">Rp {{ number_format($stats['pending_amount'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Terverifikasi</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50">
                    <x-icon name="check" :size="16" class="text-emerald-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $stats['verified_count'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Rp {{ number_format($stats['verified_amount'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Ditolak</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50">
                    <x-icon name="x" :size="16" class="text-rose-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-rose-600">{{ $stats['rejected_count'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Transaksi</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                    <x-icon name="credit-card" :size="16" class="text-indigo-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-indigo-600">
                {{ $stats['pending_count'] + $stats['verified_count'] + $stats['rejected_count'] }}
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <x-ui.card class="mt-6 mb-4">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="grid gap-3 sm:grid-cols-3">
            <x-ui.input name="search" label="Cari" :value="request('search')" placeholder="No. invoice atau nama penghuni" />
            <x-ui.select name="status" label="Status" :options="$statusLabels" :value="request('status')" placeholder="Semua status" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit">Terapkan</x-ui.button>
                <x-ui.button :href="route('admin.payments.index')" variant="secondary">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Tabel --}}
    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Tgl Bayar</th>
                        <th class="px-4 py-3">No. Invoice</th>
                        <th class="px-4 py-3">Penghuni</th>
                        <th class="px-4 py-3">Kamar</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $pay)
                        <tr class="hover:bg-slate-50 transition {{ $pay->status === 'pending' ? 'bg-amber-50/40' : '' }}">
                            <td class="px-4 py-3 text-slate-600">{{ $pay->payment_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $pay->invoice?->invoice_number }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $pay->invoice?->tenant?->full_name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $pay->invoice?->tenant?->room?->room_number }}</td>
                            <td class="px-4 py-3 font-medium">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3"><x-ui.badge :status="$pay->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <x-ui.button :href="route('admin.payments.show', $pay)" variant="ghost" size="link">Detail</x-ui.button>
                                    @if ($pay->status === 'pending')
                                        <x-ui.button :href="route('admin.payments.show', $pay)" variant="ghost" size="link" class="!text-emerald-600 hover:!text-emerald-800">Verifikasi</x-ui.button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                        <x-icon name="credit-card" :size="24" class="text-slate-400" />
                                    </div>
                                    <p class="text-slate-500">Belum ada pembayaran.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div class="mt-4">{{ $payments->links() }}</div>
@endsection