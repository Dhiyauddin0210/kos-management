@extends('layouts.admin')

@section('title', $property->name)

@section('content')
    <x-ui.page-header :title="$property->name" subtitle="Detail properti dan daftar kamar">
        <x-ui.button :href="route('admin.properties.edit', $property)" variant="secondary">Edit</x-ui.button>
        <x-ui.button :href="route('admin.rooms.create', ['property_id' => $property->id])">+ Tambah Kamar</x-ui.button>
    </x-ui.page-header>

    <x-ui.card>
        <div class="flex flex-col gap-5 sm:flex-row">
            @if ($property->photo)
                <img src="{{ asset('storage/' . $property->photo) }}" alt="{{ $property->name }}" class="h-40 w-full rounded-lg object-cover sm:w-56">
            @else
                <div class="flex h-40 w-full items-center justify-center rounded-lg bg-slate-100 text-5xl sm:w-56">🏢</div>
            @endif

            <dl class="grid flex-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-500">Alamat</dt><dd class="font-medium text-slate-800">{{ $property->address }}</dd></div>
                <div><dt class="text-slate-500">Kota</dt><dd class="font-medium text-slate-800">{{ $property->city }}</dd></div>
                <div><dt class="text-slate-500">Telepon</dt><dd class="font-medium text-slate-800">{{ $property->phone ?: '-' }}</dd></div>
                <div><dt class="text-slate-500">Status</dt><dd><x-ui.badge :status="$property->is_active ? 'active' : 'inactive'" /></dd></div>
                <div class="sm:col-span-2"><dt class="text-slate-500">Deskripsi</dt><dd class="text-slate-700">{{ $property->description ?: '-' }}</dd></div>
            </dl>
        </div>
    </x-ui.card>

    <h2 class="mb-3 mt-8 text-lg font-semibold text-slate-800">Kamar ({{ $property->rooms->count() }})</h2>

    @if ($property->rooms->isEmpty())
        <x-ui.card><p class="py-6 text-center text-slate-400">Belum ada kamar di properti ini.</p></x-ui.card>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($property->rooms as $room)
                <a href="{{ route('admin.rooms.show', $room) }}"
                   class="block overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-indigo-300 hover:shadow">
                    @if ($room->photo)
                        <img src="{{ asset('storage/' . $room->photo) }}" alt="Kamar {{ $room->room_number }}" class="h-28 w-full object-cover">
                    @else
                        <div class="flex h-28 w-full items-center justify-center bg-slate-100 text-3xl">🛏️</div>
                    @endif
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-slate-800">{{ $room->room_number }}</span>
                            <x-ui.badge :status="$room->status" />
                        </div>
                        <p class="text-sm text-slate-500">{{ $room->type }}{{ $room->size ? ' · ' . $room->size . ' m' : '' }}</p>
                        <p class="mt-1 font-semibold text-indigo-600">Rp {{ number_format($room->price, 0, ',', '.') }}<span class="text-xs font-normal text-slate-400">/bln</span></p>
                        <p class="mt-2 truncate text-xs text-slate-500">{{ $room->activeTenant?->full_name ?? 'Belum ada penghuni' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
