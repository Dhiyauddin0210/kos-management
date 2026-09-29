@extends('layouts.admin')

@section('title', 'Tambah Penghuni')

@section('content')
    <x-ui.page-header title="Tambah Penghuni" subtitle="Membuat akun login penghuni sekaligus menempatkannya di kamar" />

    <x-ui.card>
        <form method="POST" action="{{ route('admin.tenants.store') }}">
            @csrf
            @include('admin.tenants._form')

            <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                <x-ui.button type="submit">Simpan</x-ui.button>
                <x-ui.button :href="route('admin.tenants.index')" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
