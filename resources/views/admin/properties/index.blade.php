@extends('layouts.admin')

@section('title', 'Properti')

@section('content')
    <x-ui.page-header title="Properti" subtitle="Kelola semua properti kos">
        <x-ui.button :href="route('admin.properties.create')">+ Tambah Properti</x-ui.button>
    </x-ui.page-header>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Foto</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3">Kota</th>
                        <th class="px-4 py-3">Kamar</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($properties as $property)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                @if ($property->photo)
                                    <img src="{{ asset('storage/' . $property->photo) }}" alt="{{ $property->name }}" class="h-12 w-12 rounded-lg object-cover">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-xl">🏢</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $property->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $property->address }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $property->city }}</td>
                            <td class="px-4 py-3">{{ $property->rooms_count }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge :status="$property->is_active ? 'active' : 'inactive'" />
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <x-ui.button :href="route('admin.properties.show', $property)" variant="ghost" size="link">Detail</x-ui.button>
                                    <x-ui.button :href="route('admin.properties.edit', $property)" variant="ghost" size="link">Edit</x-ui.button>
                                    <x-ui.confirm-button :action="route('admin.properties.destroy', $property)"
                                        message="Hapus properti {{ $property->name }}? Semua kamar di dalamnya (beserta data penghuni & tagihan) ikut terhapus permanen." />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Belum ada properti. Klik "Tambah Properti" untuk memulai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div class="mt-4">{{ $properties->links() }}</div>
@endsection
