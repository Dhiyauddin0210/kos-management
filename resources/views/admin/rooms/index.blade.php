@extends('layouts.admin')

@section('title', 'Kamar')

@section('content')
    <x-ui.page-header title="Kamar" subtitle="Kelola semua kamar di seluruh properti">
        <x-ui.button :href="route('admin.rooms.create')">+ Tambah Kamar</x-ui.button>
    </x-ui.page-header>

    {{-- Filter & pencarian (GET, jadi bisa di-bookmark & pagination tetap membawa filter) --}}
    <x-ui.card class="mb-4">
        <form method="GET" action="{{ route('admin.rooms.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.input name="search" label="Cari nomor kamar" :value="request('search')" placeholder="mis. A01" />
            <x-ui.select name="property_id" label="Properti" :options="$properties" :value="request('property_id')" placeholder="Semua properti" />
            <x-ui.select name="status" label="Status" :options="$statusLabels" :value="request('status')" placeholder="Semua status" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit">Terapkan</x-ui.button>
                <x-ui.button :href="route('admin.rooms.index')" variant="secondary">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Foto</th>
                        <th class="px-4 py-3">Nomor</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Properti</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rooms as $room)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                @if ($room->photo)
                                    <img src="{{ asset('storage/' . $room->photo) }}" alt="Kamar {{ $room->room_number }}" class="h-12 w-12 rounded-lg object-cover">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-xl">🛏️</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $room->room_number }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $room->type }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $room->property?->name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($room->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3"><x-ui.badge :status="$room->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <x-ui.button :href="route('admin.rooms.show', $room)" variant="ghost" size="link">Detail</x-ui.button>
                                    <x-ui.button :href="route('admin.rooms.edit', $room)" variant="ghost" size="link">Edit</x-ui.button>
                                    <x-ui.confirm-button :action="route('admin.rooms.destroy', $room)"
                                        message="Hapus kamar {{ $room->room_number }}? Data riwayat penghuni & tagihan kamar ini ikut terhapus." />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Tidak ada kamar yang cocok dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div class="mt-4">{{ $rooms->links() }}</div>
@endsection
