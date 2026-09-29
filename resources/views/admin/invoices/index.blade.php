@extends('layouts.admin')

@section('title', 'Tagihan')

@section('content')
    <x-ui.page-header title="Tagihan" subtitle="Kelola semua tagihan penghuni">
        <x-ui.button :href="route('admin.invoices.export', request()->query())" variant="secondary">
            <x-icon name="file-text" :size="16" />
            Export PDF
        </x-ui.button>
        <x-ui.button :href="route('admin.invoices.create')">
            <x-icon name="plus" :size="16" />
            Buat Tagihan
        </x-ui.button>
    </x-ui.page-header>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Tagihan Bulan Ini</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                    <x-icon name="receipt" :size="16" class="text-indigo-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['total_this_month'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Rp {{ number_format($stats['amount_this_month'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Lunas</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50">
                    <x-icon name="check" :size="16" class="text-emerald-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $stats['total_paid'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Rp {{ number_format($stats['amount_paid'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Menunggu Verifikasi</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50">
                    <x-icon name="clock" :size="16" class="text-amber-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-amber-600">{{ $stats['total_pending'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Terlambat</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50">
                    <x-icon name="alert-circle" :size="16" class="text-rose-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-rose-600">{{ $stats['total_overdue'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Rp {{ number_format($stats['amount_overdue'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filter & Actions --}}
    <x-ui.card class="mt-6 mb-4">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <x-ui.input name="search" label="Cari" :value="request('search')" placeholder="No. invoice, nama, kamar" />
            <x-ui.select name="month" label="Bulan" :options="$months" :value="request('month')" placeholder="Semua bulan" />
            <x-ui.select name="year" label="Tahun" :options="collect($years)->mapWithKeys(fn($y) => [$y => $y])->all()" :value="request('year')" placeholder="Semua tahun" />
            <x-ui.select name="status" label="Status" :options="$statusLabels" :value="request('status')" placeholder="Semua status" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit">Terapkan</x-ui.button>
                <x-ui.button :href="route('admin.invoices.index')" variant="secondary">Reset</x-ui.button>
            </div>
        </form>

        {{-- Generate massal --}}
        <div class="mt-4 border-t border-slate-100 pt-4">
            <form method="POST" action="{{ route('admin.invoices.generate') }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Generate Massal</label>
                    <p class="text-xs text-slate-500">Buat tagihan otomatis untuk semua penghuni aktif</p>
                </div>
                <select name="month" required class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach ($months as $num => $name)
                        <option value="{{ $num }}" @selected($num == now()->month)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="year" required class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach ($years as $y)
                        <option value="{{ $y }}" @selected($y == now()->year)>{{ $y }}</option>
                    @endforeach
                </select>
                <x-ui.button type="submit" variant="secondary">
                    <x-icon name="refresh-cw" :size="16" />
                    Generate
                </x-ui.button>
            </form>
        </div>
    </x-ui.card>

    {{-- Tabel --}}
    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">No. Invoice</th>
                        <th class="px-4 py-3">Penghuni</th>
                        <th class="px-4 py-3">Kamar</th>
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
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $inv->tenant?->full_name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $inv->tenant?->room?->room_number }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ sprintf('%02d/%d', $inv->month, $inv->year) }}</td>
                            <td class="px-4 py-3 font-medium">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $inv->due_date->format('d/m/Y') }}
                                @if ($inv->status === 'overdue')
                                    <span class="ml-1 text-xs text-rose-500">({{ $inv->due_date->diffForHumans() }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><x-ui.badge :status="$inv->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <x-ui.button :href="route('admin.invoices.show', $inv)" variant="ghost" size="link">Detail</x-ui.button>
                                    @if ($inv->status !== 'paid')
                                        <form method="POST" action="{{ route('admin.invoices.send-reminder', $inv) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-sm text-amber-600 hover:text-amber-800 hover:underline">Reminder</button>
                                        </form>
                                    @endif
                                    @if (! $inv->payments()->exists())
                                        <x-ui.confirm-button :action="route('admin.invoices.destroy', $inv)"
                                            message="Hapus tagihan {{ $inv->invoice_number }}? Tidak bisa dibatalkan." />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                        <x-icon name="receipt" :size="24" class="text-slate-400" />
                                    </div>
                                    <p class="text-slate-500">Belum ada tagihan.</p>
                                    <x-ui.button :href="route('admin.invoices.create')">
                                        <x-icon name="plus" :size="16" />
                                        Buat Tagihan Pertama
                                    </x-ui.button>
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