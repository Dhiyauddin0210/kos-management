@extends('layouts.admin')

@section('title', 'Tambah Properti')

@section('content')
    <x-ui.page-header title="Tambah Properti" subtitle="Isi data properti kos baru" />

    <x-ui.card>
        <form method="POST" action="{{ route('admin.properties.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.properties._form')

            <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                <x-ui.button type="submit">Simpan</x-ui.button>
                <x-ui.button :href="route('admin.properties.index')" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
