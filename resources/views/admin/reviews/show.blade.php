@extends('layouts.admin')

@section('title', 'Detail Ulasan')

@section('content')
    <x-ui.page-header title="Detail Ulasan" :subtitle="$review->display_name">
        <x-ui.button :href="route('admin.reviews.index')" variant="secondary">
            <x-icon name="chevron-right" :size="16" class="rotate-180" />
            Kembali
        </x-ui.button>
        <x-ui.confirm-button :action="route('admin.reviews.destroy', $review)"
            label="Hapus" variant="danger" size="md"
            message="Hapus ulasan ini? Tidak bisa dibatalkan." />
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Detail Ulasan --}}
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card title="Ulasan">
                <div class="flex items-start justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-base font-semibold text-slate-600">
                            {{ $review->initial }}
                        </span>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $review->display_name }}</p>
                            <p class="text-xs text-slate-500">{{ $review->user?->email }}</p>
                        </div>
                    </div>
                    <x-ui.badge :status="$review->status" />
                </div>

                {{-- Rating Utama --}}
                <div class="text-center mb-6">
                    <p class="text-5xl font-bold text-amber-500">{{ $review->rating }}</p>
                    <div class="mt-2 flex justify-center gap-1 text-amber-500">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Rating keseluruhan</p>
                </div>

                {{-- Kategori Rating --}}
                @php
                    $categories = [
                        'rating_cleanliness'  => 'Kebersihan',
                        'rating_security'     => 'Keamanan',
                        'rating_facilities'   => 'Fasilitas',
                        'rating_price'        => 'Harga',
                        'rating_friendliness' => 'Keramahan',
                    ];
                    $hasCategory = collect($categories)->keys()->some(fn ($k) => $review->$k !== null);
                @endphp

                @if ($hasCategory)
                    <div class="mb-6">
                        <p class="text-xs uppercase tracking-wide text-slate-500 mb-3">Rating Kategori</p>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($categories as $field => $label)
                                @if ($review->$field)
                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <p class="text-xs text-slate-500">{{ $label }}</p>
                                        <p class="mt-1 text-lg font-bold text-amber-600">{{ $review->$field }}<span class="text-xs text-slate-400">/5</span></p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Komentar --}}
                <div class="pt-6 border-t border-slate-100">
                    <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Komentar</p>
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-700 leading-relaxed">{{ $review->comment }}</p>
                </div>

                @if ($review->is_anonymous)
                    <div class="mt-4 rounded-lg bg-slate-100 p-3 text-xs text-slate-600">
                        👤 Dikirim sebagai <strong>anonim</strong>
                    </div>
                @endif

                <div class="mt-4 flex flex-wrap gap-4 text-xs text-slate-500">
                    <span>Dikirim: {{ $review->created_at->format('d F Y H:i') }}</span>
                    @if ($review->approved_at)
                        <span>Diproses: {{ $review->approved_at->format('d F Y H:i') }}</span>
                    @endif
                    @if ($review->approvedBy)
                        <span>Oleh: {{ $review->approvedBy->name }}</span>
                    @endif
                </div>

                @if ($review->admin_notes)
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Catatan Admin</p>
                        <p class="rounded-lg bg-indigo-50 p-4 text-sm text-indigo-900">{{ $review->admin_notes }}</p>
                    </div>
                @endif
            </x-ui.card>
        </div>

        {{-- Panel Aksi --}}
        <div class="space-y-6">
            @if ($review->status === 'pending')
                {{-- Approve --}}
                <x-ui.card title="Setujui Ulasan">
                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="approve_notes" class="mb-1 block text-sm font-medium text-slate-700">Catatan (opsional)</label>
                            <textarea id="approve_notes" name="admin_notes" rows="2"
                                      class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                      placeholder="Contoh: Ulasan bagus, langsung publish.">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition"
                                onclick="return confirm('Setujui ulasan ini? Akan tampil di landing page.')">
                            <x-icon name="check" :size="16" />
                            Setujui & Tampilkan
                        </button>
                    </form>
                </x-ui.card>

                {{-- Reject --}}
                <x-ui.card title="Tolak Ulasan">
                    <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="reject_notes" class="mb-1 block text-sm font-medium text-slate-700">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="reject_notes" name="admin_notes" rows="2" required
                                      class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                      placeholder="Contoh: Terlalu pendek / mengandung kata kasar.">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 transition"
                                onclick="return confirm('Tolak ulasan ini?')">
                            <x-icon name="x" :size="16" />
                            Tolak
                        </button>
                    </form>
                </x-ui.card>
            @else
                <x-ui.card title="Status">
                    <div class="py-4 text-center">
                        <x-ui.badge :status="$review->status" />
                        <p class="mt-3 text-sm text-slate-500">
                            @if ($review->status === 'approved')
                                Ulasan ini <strong>tampil</strong> di landing page.
                            @else
                                Ulasan ini <strong>tidak tampil</strong> di landing page.
                            @endif
                        </p>
                    </div>
                </x-ui.card>
            @endif

            {{-- Info Penghuni --}}
            <x-ui.card title="Penghuni">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-slate-500">Nama</dt>
                        <dd class="font-medium text-slate-800">{{ $review->display_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Email</dt>
                        <dd class="text-slate-800 text-xs">{{ $review->user?->email }}</dd>
                    </div>
                    @if ($review->user?->tenant)
                        <div>
                            <dt class="text-slate-500">Kamar</dt>
                            <dd class="font-medium text-slate-800">{{ $review->user->tenant->room?->room_number ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Status Sewa</dt>
                            <dd><x-ui.badge :status="$review->user->tenant->status" /></dd>
                        </div>
                    @endif
                </dl>
            </x-ui.card>
        </div>
    </div>
@endsection