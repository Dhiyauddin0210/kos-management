@extends('layouts.admin')

@section('title', 'Kamar ' . $room->room_number)

@section('content')
    <x-ui.page-header title="Kamar {{ $room->room_number }}" :subtitle="$room->property?->name">
        <x-ui.button :href="route('admin.rooms.index')" variant="secondary">Kembali</x-ui.button>
        <x-ui.button :href="route('admin.rooms.edit', $room)">Edit</x-ui.button>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Detail kamar --}}
        <x-ui.card class="lg:col-span-2">
            <div class="flex flex-col gap-5 sm:flex-row">
                @if ($room->photo)
                    <img src="{{ asset('storage/' . $room->photo) }}" alt="Kamar {{ $room->room_number }}" class="h-44 w-full rounded-lg object-cover sm:w-60">
                @else
                    <div class="flex h-44 w-full items-center justify-center rounded-lg bg-slate-100 text-5xl sm:w-60">🛏️</div>
                @endif

                <dl class="grid flex-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Tipe</dt><dd class="font-medium text-slate-800">{{ $room->type }}</dd></div>
                    <div><dt class="text-slate-500">Status</dt><dd><x-ui.badge :status="$room->status" /></dd></div>
                    <div><dt class="text-slate-500">Harga</dt><dd class="font-semibold text-indigo-600">Rp {{ number_format($room->price, 0, ',', '.') }} / bulan</dd></div>
                    <div><dt class="text-slate-500">Ukuran</dt><dd class="font-medium text-slate-800">{{ $room->size ? $room->size . ' m' : '-' }}</dd></div>
                    <div class="sm:col-span-2">
                        <dt class="mb-1 text-slate-500">Fasilitas</dt>
                        <dd class="flex flex-wrap gap-1.5">
                            @forelse ($room->facilities ?? [] as $facility)
                                <x-ui.badge color="indigo">{{ $facility }}</x-ui.badge>
                            @empty
                                <span class="text-slate-400">-</span>
                            @endforelse
                        </dd>
                    </div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Deskripsi</dt><dd class="text-slate-700">{{ $room->description ?: '-' }}</dd></div>
                </dl>
            </div>
        </x-ui.card>

        {{-- Penghuni aktif --}}
        <x-ui.card title="Penghuni Aktif">
            @if ($room->activeTenant)
                @php $t = $room->activeTenant; @endphp
                <div class="space-y-2 text-sm">
                    <p class="text-lg font-semibold text-slate-800">{{ $t->full_name }}</p>
                    <p class="text-slate-600">📞 {{ $t->phone }}</p>
                    <p class="text-slate-600">✉️ {{ $t->user?->email }}</p>
                    <p class="text-slate-600">Masuk: {{ $t->start_date->format('d/m/Y') }}</p>
                    <p class="text-slate-600">Selesai: {{ $t->end_date->format('d/m/Y') }}</p>
                    <x-ui.button :href="route('admin.tenants.show', $t)" variant="secondary" size="sm" class="mt-2">Lihat Penghuni</x-ui.button>
                </div>
            @else
                <p class="py-4 text-center text-sm text-slate-400">Belum ada penghuni aktif.</p>
            @endif
        </x-ui.card>
    </div>

    {{-- Riwayat tagihan --}}
    <x-ui.card title="Riwayat Tagihan (12 terakhir)" class="mt-6" flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3">No. Invoice</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3">Penghuni</th>
                        <th class="px-4 py-3">Jumlah</th><th class="px-4 py-3">Jatuh Tempo</th><th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($invoices as $inv)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">{{ $inv->invoice_number }}</td>
                            <td class="px-4 py-3">{{ sprintf('%02d/%d', $inv->month, $inv->year) }}</td>
                            <td class="px-4 py-3">{{ $inv->tenant?->full_name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $inv->due_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3"><x-ui.badge :status="$inv->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada tagihan untuk kamar ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
