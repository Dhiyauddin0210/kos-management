@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')
    {{-- Welcome --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Halo, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-sm text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Info Kontrak --}}
    <div class="mb-6 rounded-xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30">
                    <x-icon name="bed-double" :size="26" class="text-white" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-emerald-700 font-medium">Kamar Anda</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $tenant->room->room_number }}</p>
                    <p class="text-sm text-slate-600">{{ $tenant->room->property->name }} · {{ $tenant->room->type }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs uppercase tracking-wide text-emerald-700 font-medium">Sisa Kontrak</p>
                <p class="text-2xl font-bold {{ $daysLeft < 30 ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ $daysLeft > 0 ? $daysLeft . ' hari' : 'Berakhir' }}
                </p>
                <p class="text-sm text-slate-600">s/d {{ $tenant->end_date->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Tagihan Bulan Ini --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Tagihan Bulan Ini</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                    <x-icon name="receipt" :size="16" class="text-indigo-600" />
                </div>
            </div>
            @if ($currentInvoice)
                <p class="mt-2 text-lg font-bold text-slate-800">Rp {{ number_format($currentInvoice->amount, 0, ',', '.') }}</p>
                <div class="mt-1"><x-ui.badge :status="$currentInvoice->status" /></div>
            @else
                <p class="mt-2 text-sm text-slate-400">Belum ada tagihan</p>
            @endif
        </div>

        {{-- Total Tunggakan --}}
        <div class="rounded-xl border {{ $totalDue > 0 ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-white' }} p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide {{ $totalDue > 0 ? 'text-rose-700' : 'text-slate-500' }}">Total Tunggakan</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $totalDue > 0 ? 'bg-white' : 'bg-slate-100' }}">
                    <x-icon name="wallet" :size="16" class="{{ $totalDue > 0 ? 'text-rose-600' : 'text-slate-600' }}" />
                </div>
            </div>
            <p class="mt-2 text-lg font-bold {{ $totalDue > 0 ? 'text-rose-700' : 'text-slate-800' }}">
                Rp {{ number_format($totalDue, 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs {{ $totalDue > 0 ? 'text-rose-600' : 'text-slate-500' }}">
                {{ $unpaidInvoices->count() }} tagihan belum lunas
            </p>
        </div>

        {{-- Status Sewa --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Status Sewa</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50">
                    <x-icon name="check" :size="16" class="text-emerald-600" />
                </div>
            </div>
            <p class="mt-2 text-lg font-bold text-emerald-600">Aktif</p>
            <p class="mt-1 text-xs text-slate-500">Sejak {{ $tenant->start_date->format('d M Y') }}</p>
        </div>

        {{-- Harga Sewa --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Harga Sewa</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50">
                    <x-icon name="percent" :size="16" class="text-amber-600" />
                </div>
            </div>
            <p class="mt-2 text-lg font-bold text-slate-800">Rp {{ number_format($tenant->room->price, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-500">per bulan</p>
        </div>
    </div>

    {{-- Quick Action --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <a href="{{ route('tenant.invoices.index') }}"
           class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-indigo-100 group-hover:bg-indigo-600 transition">
                <x-icon name="receipt" :size="22" class="text-indigo-600 group-hover:text-white transition" />
            </div>
            <div>
                <p class="font-semibold text-slate-800">Bayar Tagihan</p>
                <p class="text-xs text-slate-500">Upload bukti transfer</p>
            </div>
        </a>

        <a href="{{ route('tenant.payments.index') }}"
           class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-emerald-100 group-hover:bg-emerald-600 transition">
                <x-icon name="credit-card" :size="22" class="text-emerald-600 group-hover:text-white transition" />
            </div>
            <div>
                <p class="font-semibold text-slate-800">Riwayat Pembayaran</p>
                <p class="text-xs text-slate-500">Lihat semua transaksi</p>
            </div>
        </a>

        <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm opacity-60">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                <x-icon name="wrench" :size="22" class="text-slate-500" />
            </div>
            <div>
                <p class="font-semibold text-slate-800">Lapor Kerusakan</p>
                <p class="text-xs text-slate-500">Segera hadir</p>
            </div>
        </div>
    </div>

    {{-- Tagihan Terbaru --}}
    <x-ui.card title="Tagihan Terbaru" class="mt-6" flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3">No. Invoice</th>
                        <th class="px-4 py-3">Periode</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Jatuh Tempo</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentInvoices as $inv)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 font-mono text-xs">{{ $inv->invoice_number }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ sprintf('%02d/%d', $inv->month, $inv->year) }}</td>
                            <td class="px-4 py-3 font-medium">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $inv->due_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3"><x-ui.badge :status="$inv->status" /></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('tenant.invoices.show', $inv) }}"
                                   class="text-sm text-emerald-600 hover:text-emerald-800 hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada tagihan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    {{-- Pembayaran Terbaru --}}
    <x-ui.card title="Pembayaran Terbaru" class="mt-6" flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Tgl Bayar</th>
                        <th class="px-4 py-3">Invoice</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentPayments as $pay)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-600">{{ $pay->payment_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $pay->invoice->invoice_number }}</td>
                            <td class="px-4 py-3 font-medium">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3"><x-ui.badge :status="$pay->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection