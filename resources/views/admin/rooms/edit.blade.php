@extends('layouts.admin')

@section('title', 'Edit Kamar')

@section('content')
    <x-ui.page-header title="Edit Kamar {{ $room->room_number }}" :subtitle="$room->property?->name" />

    <x-ui.card>
        <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.rooms._form')

            <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                <x-ui.button :href="route('admin.rooms.index')" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
