@extends('layouts.tenant')

@section('title', 'Laporan Kerusakan')

@section('content')
    <x-ui.page-header title="Laporan Kerusakan" subtitle="Riwayat laporan maintenance kamar Anda">
        <x-ui.button :href="route('tenant.maintenances.create')">
            <x-icon name="plus" :size="16" />
            Buat Laporan Baru
        </x-ui.button>
    </x-ui.page-header>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Laporan</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                    <x-icon name="wrench" :size="16" class="text-indigo-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-amber-700">Dilaporkan</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white">
                    <x-icon name="alert-circle" :size="16" class="text-amber-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-amber-700">{{ $stats['reported'] }}</p>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-blue-700">Diproses</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white">
                    <x-icon name="clock" :size="16" class="text-blue-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-blue-700">{{ $stats['in_progress'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Selesai</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50">
                    <x-icon name="check" :size="16" class="text-emerald-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $stats['resolved'] }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <x-ui.card class="mt-6 mb-4">
        <form method="GET" action="{{ route('tenant.maintenances.index') }}" class="grid gap-3 sm:grid-cols-3">
            <x-ui.select name="status" label="Status" :options="$statusLabels" :value="request('status')" placeholder="Semua status" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit">Terapkan</x-ui.button>
                <x-ui.button :href="route('tenant.maintenances.index')" variant="secondary">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Tabel --}}
    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Prioritas</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($maintenances as $m)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800">{{ $m->title }}</p>
                                <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($m->description, 60) }}</p>
                            </td>
                            <td class="px-4 py-3"><x-ui.badge :status="$m->priority" /></td>
                            <td class="px-4 py-3"><x-ui.badge :status="$m->status" /></td>
                            <td class="px-4 py-3 text-slate-500">{{ $m->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('tenant.maintenances.show', $m) }}"
                                   class="text-sm text-emerald-600 hover:text-emerald-800 hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                        <x-icon name="wrench" :size="24" class="text-slate-400" />
                                    </div>
                                    <p class="text-slate-500">Belum ada laporan kerusakan.</p>
                                    <x-ui.button :href="route('tenant.maintenances.create')">
                                        <x-icon name="plus" :size="16" />
                                        Buat Laporan Pertama
                                    </x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div class="mt-4">{{ $maintenances->links() }}</div>
@endsection