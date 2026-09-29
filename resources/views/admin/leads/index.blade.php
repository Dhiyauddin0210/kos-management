@extends('layouts.admin')

@section('title', 'Leads')

@section('content')
    <x-ui.page-header title="Leads" subtitle="Calon penghuni dari QR code, website, dan manual" />

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Leads</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                    <x-icon name="inbox" :size="16" class="text-indigo-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-blue-700">Baru</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white">
                    <x-icon name="alert-circle" :size="16" class="text-blue-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-blue-700">{{ $stats['new'] }}</p>
            <p class="mt-1 text-xs text-blue-600">Perlu di-follow up</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Dihubungi</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50">
                    <x-icon name="clock" :size="16" class="text-amber-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-amber-600">{{ $stats['contacted'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Ditutup</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100">
                    <x-icon name="check" :size="16" class="text-slate-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-slate-600">{{ $stats['closed'] }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <x-ui.card class="mt-6 mb-4">
        <form method="GET" action="{{ route('admin.leads.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.input name="search" label="Cari" :value="request('search')" placeholder="Nama, HP, email, atau kamar" />
            <x-ui.select name="status" label="Status" :options="$statusLabels" :value="request('status')" placeholder="Semua status" />
            <x-ui.select name="source" label="Sumber" :options="$sourceLabels" :value="request('source')" placeholder="Semua sumber" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit">Terapkan</x-ui.button>
                <x-ui.button :href="route('admin.leads.index')" variant="secondary">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Tabel --}}
    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kontak</th>
                        <th class="px-4 py-3">Kamar Diminati</th>
                        <th class="px-4 py-3">Sumber</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Masuk</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($leads as $lead)
                        <tr class="hover:bg-slate-50 transition {{ $lead->status === 'new' ? 'bg-blue-50/40' : '' }}">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800">{{ $lead->name }}</p>
                                @if ($lead->email)
                                    <p class="text-xs text-slate-500">{{ $lead->email }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $lead->phone) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 hover:underline">
                                    <x-icon name="inbox" :size="14" />
                                    {{ $lead->phone }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                @if ($lead->room)
                                    <span class="font-medium text-slate-800">{{ $lead->room->room_number }}</span>
                                    <span class="text-xs"> · {{ $lead->room->property?->name }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                    {{ $sourceLabels[$lead->source] ?? $lead->source }}
                                </span>
                            </td>
                            <td class="px-4 py-3"><x-ui.badge :status="$lead->status" /></td>
                            <td class="px-4 py-3 text-slate-500">{{ $lead->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <x-ui.button :href="route('admin.leads.show', $lead)" variant="ghost" size="link">Detail</x-ui.button>
                                    <x-ui.confirm-button :action="route('admin.leads.destroy', $lead)"
                                        message="Hapus lead dari {{ $lead->name }}? Tidak bisa dibatalkan." />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                        <x-icon name="inbox" :size="24" class="text-slate-400" />
                                    </div>
                                    <p class="text-slate-500">Belum ada leads.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div class="mt-4">{{ $leads->links() }}</div>
@endsection