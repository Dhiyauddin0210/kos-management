@extends('layouts.tenant')

@section('title', 'Tagihan Saya')

@section('content')
    <x-ui.page-header title="Tagihan Saya" subtitle="Semua tagihan sewa kamar Anda" />

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Tagihan</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                    <x-icon name="receipt" :size="16" class="text-indigo-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['total_invoices'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Lunas</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50">
                    <x-icon name="check" :size="16" class="text-emerald-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $stats['total_paid'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Belum Lunas</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50">
                    <x-icon name="clock" :size="16" class="text-amber-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-amber-600">{{ $stats['total_unpaid'] }}</p>
        </div>

        <div class="rounded-xl border {{ $stats['total_due'] > 0 ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-white' }} p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide {{ $stats['total_due'] > 0 ? 'text-rose-700' : 'text-slate-500' }}">Total Tunggakan</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $stats['total_due'] > 0 ? 'bg-white' : 'bg-slate-100' }}">
                    <x-icon name="wallet" :size="16" class="{{ $stats['total_due'] > 0 ? 'text-rose-600' : 'text-slate-600' }}" />
                </div>
            </div>
            <p class="mt-2 text-lg font-bold {{ $stats['total_due'] > 0 ? 'text-rose-700' : 'text-slate-800' }}">
                Rp {{ number_format($stats['total_due'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <x-ui.card class="mt-6 mb-4">
        <form method="GET" action="{{ route('tenant.invoices.index') }}" class="grid gap-3 sm:grid-cols-3">
            <x-ui.select name="status" label="Status" :options="$statusLabels" :value="request('status')" placeholder="Semua status" />
            <x-ui.select name="year" label="Tahun" :options="collect($years)->mapWithKeys(fn($y) => [$y => $y])->all()" :value="request('year')" placeholder="Semua tahun" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit">Terapkan</x-ui.button>
                <x-ui.button :href="route('tenant.invoices.index')" variant="secondary">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Tabel --}}
    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
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
                    @forelse ($invoices as $inv)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 font-mono text-xs">{{ $inv->invoice_number }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ sprintf('%02d/%d', $inv->month, $inv->year) }}</td>
                            <td class="px-4 py-3 font-medium">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $inv->due_date->format('d/m/Y') }}
                                @if ($inv->status === 'overdue')
                                    <span class="ml-1 text-xs text-rose-500">({{ $inv->due_date->diffForHumans() }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><x-ui.badge :status="$inv->status" /></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('tenant.invoices.show', $inv) }}"
                                   class="text-sm text-emerald-600 hover:text-emerald-800 hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                        <x-icon name="receipt" :size="24" class="text-slate-400" />
                                    </div>
                                    <p class="text-slate-500">Belum ada tagihan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div class="mt-4">{{ $invoices->links() }}</div>
@endsection