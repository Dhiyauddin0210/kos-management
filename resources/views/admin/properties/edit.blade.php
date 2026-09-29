@extends('layouts.admin')

@section('title', 'Edit Properti')

@section('content')
    <x-ui.page-header title="Edit Properti" :subtitle="$property->name" />

    <x-ui.card>
        <form method="POST" action="{{ route('admin.properties.update', $property) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.properties._form')

            <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                <x-ui.button :href="route('admin.properties.index')" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
