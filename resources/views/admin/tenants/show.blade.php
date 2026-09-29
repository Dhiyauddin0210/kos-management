@extends('layouts.admin')

@section('title', $tenant->full_name)

@section('content')
    <x-ui.page-header :title="$tenant->full_name" subtitle="Detail penghuni">
        <x-ui.button :href="route('admin.tenants.index')" variant="secondary">Kembali</x-ui.button>
        <x-ui.button :href="route('admin.tenants.edit', $tenant)">Edit</x-ui.button>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-ui.card title="Data Penghuni" class="lg:col-span-2">
            <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-500">Nama Lengkap</dt><dd class="font-medium text-slate-800">{{ $tenant->full_name }}</dd></div>
                <div><dt class="text-slate-500">Status</dt><dd><x-ui.badge :status="$tenant->status" /></dd></div>
                <div><dt class="text-slate-500">Email</dt><dd class="font-medium text-slate-800">{{ $tenant->user?->email }}</dd></div>
                <div><dt class="text-slate-500">No. HP</dt><dd class="font-medium text-slate-800">{{ $tenant->phone }}</dd></div>
                <div><dt class="text-slate-500">No. KTP</dt><dd class="font-mono text-slate-800">{{ $tenant->ktp_number }}</dd></div>
                <div><dt class="text-slate-500">Alamat Asal</dt><dd class="text-slate-800">{{ $tenant->address ?: '-' }}</dd></div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Sewa">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-500">Kamar</dt>
                    <dd class="font-medium text-slate-800">
                        <a href="{{ route('admin.rooms.show', $tenant->room) }}" class="text-indigo-600 hover:underline">{{ $tenant->room->room_number }}</a>
                        · {{ $tenant->room->property?->name }}
                    </dd>
                </div>
                <div><dt class="text-slate-500">Harga</dt><dd class="font-medium text-slate-800">Rp {{ number_format($tenant->room->price, 0, ',', '.') }} / bulan</dd></div>
                <div><dt class="text-slate-500">Mulai</dt><dd class="font-medium text-slate-800">{{ $tenant->start_date->format('d/m/Y') }}</dd></div>
                <div><dt class="text-slate-500">Selesai</dt><dd class="font-medium text-slate-800">{{ $tenant->end_date->format('d/m/Y') }} ({{ $tenant->duration_months }} bulan)</dd></div>
            </dl>
        </x-ui.card>
    </div>

    {{-- 3 tab: Tagihan, Maintenance, Perpanjangan --}}
    <x-ui.card class="mt-6" flush>
        <div x-data="{ tab: 'invoices' }">
            <div class="flex gap-6 overflow-x-auto border-b border-slate-200 px-5 text-sm">
                @foreach ([
                    'invoices'     => 'Tagihan (' . $tenant->invoices->count() . ')',
                    'maintenances' => 'Maintenance (' . $tenant->maintenances->count() . ')',
                    'extensions'   => 'Perpanjangan (' . $tenant->extensions->count() . ')',
                ] as $key => $label)
                    <button type="button" @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            class="whitespace-nowrap border-b-2 py-4 font-medium">{{ $label }}</button>
                @endforeach
            </div>

            {{-- Tab Tagihan --}}
            <div x-show="tab === 'invoices'" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr><th class="px-4 py-3">No. Invoice</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3">Jumlah</th><th class="px-4 py-3">Jatuh Tempo</th><th class="px-4 py-3">Dibayar</th><th class="px-4 py-3">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tenant->invoices as $inv)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">{{ $inv->invoice_number }}</td>
                                <td class="px-4 py-3">{{ sprintf('%02d/%d', $inv->month, $inv->year) }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $inv->due_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $inv->paid_at?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3"><x-ui.badge :status="$inv->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada tagihan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab Maintenance --}}
            <div x-show="tab === 'maintenances'" x-cloak class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr><th class="px-4 py-3">Judul</th><th class="px-4 py-3">Prioritas</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Dilaporkan</th><th class="px-4 py-3">Catatan Admin</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tenant->maintenances as $m)
                            <tr>
                                <td class="px-4 py-3"><p class="font-medium text-slate-800">{{ $m->title }}</p><p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($m->description, 70) }}</p></td>
                                <td class="px-4 py-3"><x-ui.badge :status="$m->priority" /></td>
                                <td class="px-4 py-3"><x-ui.badge :status="$m->status" /></td>
                                <td class="px-4 py-3">{{ $m->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $m->admin_notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada laporan kerusakan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab Perpanjangan --}}
            <div x-show="tab === 'extensions'" x-cloak class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr><th class="px-4 py-3">Berakhir Saat Ini</th><th class="px-4 py-3">Diajukan Sampai</th><th class="px-4 py-3">Durasi</th><th class="px-4 py-3">Biaya Tambahan</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Catatan</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tenant->extensions as $ext)
                            <tr>
                                <td class="px-4 py-3">{{ $ext->current_end_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $ext->requested_end_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $ext->duration_months }} bulan</td>
                                <td class="px-4 py-3">Rp {{ number_format($ext->additional_cost, 0, ',', '.') }}</td>
                                <td class="px-4 py-3"><x-ui.badge :status="$ext->status" /></td>
                                <td class="px-4 py-3 text-slate-600">{{ $ext->admin_notes ?: ($ext->notes ?: '-') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada pengajuan perpanjangan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-ui.card>
@endsection
