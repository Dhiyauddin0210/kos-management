@extends('layouts.admin')

@section('title', 'Ulasan')

@section('content')
    <x-ui.page-header title="Ulasan" subtitle="Moderasi ulasan dari penghuni" />

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Ulasan</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                    <x-icon name="star" :size="16" class="text-indigo-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-amber-700">Menunggu</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white">
                    <x-icon name="clock" :size="16" class="text-amber-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-amber-700">{{ $stats['pending'] }}</p>
            <p class="mt-1 text-xs text-amber-600">Perlu moderasi</p>
        </div>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-emerald-700">Disetujui</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white">
                    <x-icon name="check" :size="16" class="text-emerald-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-emerald-700">{{ $stats['approved'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Rating Rata-rata</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50">
                    <x-icon name="star" :size="16" class="text-amber-600" />
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold text-amber-600">{{ number_format($stats['average'], 1) }}<span class="text-sm text-slate-400">/5</span></p>
        </div>
    </div>

    {{-- Filter --}}
    <x-ui.card class="mt-6 mb-4">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.input name="search" label="Cari" :value="request('search')" placeholder="Komentar atau nama penghuni" />
            <x-ui.select name="status" label="Status" :options="$statusLabels" :value="request('status')" placeholder="Semua status" />
            <x-ui.select name="rating" label="Rating" :value="request('rating')" placeholder="Semua rating"
                         :options="[5 => '5 Bintang', 4 => '4 Bintang', 3 => '3 Bintang', 2 => '2 Bintang', 1 => '1 Bintang']" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit">Terapkan</x-ui.button>
                <x-ui.button :href="route('admin.reviews.index')" variant="secondary">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Tabel --}}
    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Penghuni</th>
                        <th class="px-4 py-3">Rating</th>
                        <th class="px-4 py-3">Komentar</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-slate-50 transition {{ $review->status === 'pending' ? 'bg-amber-50/40' : '' }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">
                                        {{ $review->initial }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $review->display_name }}</p>
                                        @if ($review->is_anonymous)
                                            <p class="text-xs text-slate-400">Anonim</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-0.5 text-amber-500">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    @endfor
                                    <span class="ml-1 text-xs font-semibold text-slate-600">{{ $review->rating }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 max-w-xs">
                                <p class="line-clamp-2">{{ $review->comment }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $review->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3"><x-ui.badge :status="$review->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <x-ui.button :href="route('admin.reviews.show', $review)" variant="ghost" size="link">Detail</x-ui.button>
                                    <x-ui.confirm-button :action="route('admin.reviews.destroy', $review)"
                                        message="Hapus ulasan dari {{ $review->display_name }}? Tidak bisa dibatalkan." />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                        <x-icon name="star" :size="24" class="text-slate-400" />
                                    </div>
                                    <p class="text-slate-500">Belum ada ulasan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div class="mt-4">{{ $reviews->links() }}</div>
@endsection