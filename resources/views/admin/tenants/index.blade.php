@extends('layouts.admin')

@section('title', 'Penghuni')

@section('content')
    <x-ui.page-header title="Penghuni" subtitle="Kelola data penghuni kos">
        <x-ui.button :href="route('admin.tenants.create')">+ Tambah Penghuni</x-ui.button>
    </x-ui.page-header>

    <x-ui.card class="mb-4">
        <form method="GET" action="{{ route('admin.tenants.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.input name="search" label="Cari" :value="request('search')" placeholder="Nama, KTP, atau no. kamar" />
            <x-ui.select name="status" label="Status" :value="request('status')" placeholder="Semua status"
                         :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" />
            <x-ui.select name="room_id" label="Kamar" :options="$roomFilterOptions" :value="request('room_id')" placeholder="Semua kamar" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit">Terapkan</x-ui.button>
                <x-ui.button :href="route('admin.tenants.index')" variant="secondary">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">No. KTP</th>
                        <th class="px-4 py-3">Kamar</th>
                        <th class="px-4 py-3">Tgl Masuk</th>
                        <th class="px-4 py-3">Tgl Keluar</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tenants as $tenant)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800">{{ $tenant->full_name }}</p>
                                <p class="text-xs text-slate-500">{{ $tenant->user?->email }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $tenant->ktp_number }}</td>
                            <td class="px-4 py-3">
                                <span class="font-semibold">{{ $tenant->room?->room_number }}</span>
                                <span class="text-xs text-slate-500">{{ $tenant->room?->property?->name }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $tenant->start_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $tenant->end_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3"><x-ui.badge :status="$tenant->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <x-ui.button :href="route('admin.tenants.show', $tenant)" variant="ghost" size="link">Detail</x-ui.button>
                                    <x-ui.button :href="route('admin.tenants.edit', $tenant)" variant="ghost" size="link">Edit</x-ui.button>
                                    <x-ui.confirm-button :action="route('admin.tenants.destroy', $tenant)"
                                        message="Hapus penghuni {{ $tenant->full_name }} beserta akunnya? Semua tagihan, pembayaran, maintenance, dan perpanjangannya ikut terhapus permanen." />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Tidak ada penghuni yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div class="mt-4">{{ $tenants->links() }}</div>
@endsection
