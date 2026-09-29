@extends('layouts.tenant')

@section('title', 'Pembayaran Saya')

@section('content')
    <x-ui.page-header title="Pembayaran Saya" subtitle="Riwayat semua pembayaran Anda" />

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Pembayaran</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                    <x-icon name="credit-card" :size="16" class="text-indigo-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-amber-700">Menunggu</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white">
                    <x-icon name="clock" :size="16" class="text-amber-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-amber-700">{{ $stats['pending'] }}</p>
        </div>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-emerald-700">Terverifikasi</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white">
                    <x-icon name="check" :size="16" class="text-emerald-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-emerald-700">{{ $stats['verified'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Ditolak</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50">
                    <x-icon name="x" :size="16" class="text-rose-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-rose-600">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <x-ui.card class="mt-6 mb-4">
        <form method="GET" action="{{ route('tenant.payments.index') }}" class="grid gap-3 sm:grid-cols-3">
            <x-ui.select name="status" label="Status" :options="$statusLabels" :value="request('status')" placeholder="Semua status" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit">Terapkan</x-ui.button>
                <x-ui.button :href="route('tenant.payments.index')" variant="secondary">Reset</x-ui.button>
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
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Bukti</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $pay)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-600">{{ $pay->payment_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                @if ($pay->invoice)
                                    <a href="{{ route('tenant.invoices.show', $pay->invoice) }}"
                                       class="font-mono text-xs text-emerald-600 hover:underline">
                                        {{ $pay->invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @if ($pay->proof)
                                    <a href="{{ asset('storage/' . $pay->proof) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:underline">
                                        <x-icon name="eye" :size="14" />
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><x-ui.badge :status="$pay->status" /></td>
                            <td class="px-4 py-3 text-slate-600 text-xs">
                                {{ $pay->notes ?: '-' }}
                                @if ($pay->status === 'rejected' && $pay->notes)
                                    <p class="mt-1 text-rose-600">Alasan: {{ $pay->notes }}</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
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