@extends('layouts.admin')

@section('title', 'Edit Penghuni')

@section('content')
    <x-ui.page-header title="Edit Penghuni" :subtitle="$tenant->full_name">
        {{-- Form checkout terpisah dari form edit (form tidak boleh bersarang) --}}
        @if ($tenant->status === 'active')
            <x-ui.confirm-button :action="route('admin.tenants.checkout', $tenant)" method="POST"
                label="Checkout" variant="danger" size="md"
                message="Checkout {{ $tenant->full_name }}? Status jadi nonaktif, tanggal selesai = hari ini, dan kamar kembali tersedia." />
        @endif
        <x-ui.button :href="route('admin.tenants.show', $tenant)" variant="secondary">Kembali</x-ui.button>
    </x-ui.page-header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}">
            @csrf
            @method('PUT')
            @include('admin.tenants._form')

            <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                <x-ui.button :href="route('admin.tenants.show', $tenant)" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
