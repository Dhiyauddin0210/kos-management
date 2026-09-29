@extends('layouts.admin')

@section('title', 'Tambah Kamar')

@section('content')
    <x-ui.page-header title="Tambah Kamar" subtitle="Isi data kamar baru" />

    <x-ui.card>
        <form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.rooms._form')

            <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                <x-ui.button type="submit">Simpan</x-ui.button>
                <x-ui.button :href="route('admin.rooms.index')" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
