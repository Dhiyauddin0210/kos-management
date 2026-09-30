@extends('layouts.tenant')

@section('title', 'Ulasan Saya')

@section('content')
    <x-ui.page-header title="Ulasan Kos" subtitle="Bagikan pengalaman Anda tinggal di {{ \App\Models\Setting::get('kos_name', 'Kos Adin') }}" />

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Kiri: Form / View Ulasan --}}
        <div class="lg:col-span-2 space-y-6">
            @if ($review)
                {{-- Ulasan udah ada --}}
                <x-ui.card title="Ulasan Anda">
                    <div class="flex items-start justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                            <div class="mt-2"><x-ui.badge :status="$review->status" /></div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Rating Anda</p>
                            <div class="mt-2 flex gap-0.5 justify-end text-amber-500">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- Kategori Rating --}}
                    @php
                        $categories = [
                            'rating_cleanliness'  => ['Kebersihan', 'sparkle'],
                            'rating_security'     => ['Keamanan', 'shield'],
                            'rating_facilities'   => ['Fasilitas', 'wrench'],
                            'rating_price'        => ['Harga', 'wallet'],
                            'rating_friendliness' => ['Keramahan', 'heart'],
                        ];
                    @endphp

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($categories as $field => [$label, $icon])
                            @if ($review->$field)
                                <div class="rounded-lg bg-slate-50 p-3">
                                    <p class="text-xs text-slate-500">{{ $label }}</p>
                                    <p class="mt-1 text-lg font-bold text-amber-600">{{ $review->$field }}<span class="text-xs text-slate-400">/5</span></p>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Komentar --}}
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Komentar</p>
                        <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-700 leading-relaxed">{{ $review->comment }}</p>
                    </div>

                    @if ($review->is_anonymous)
                        <p class="mt-3 text-xs text-slate-400">* Dikirim sebagai anonim</p>
                    @endif

                    @if ($review->admin_notes && $review->status === 'rejected')
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Catatan Admin</p>
                            <p class="rounded-lg bg-rose-50 p-4 text-sm text-rose-700">{{ $review->admin_notes }}</p>
                        </div>
                    @endif

                    <div class="mt-4 text-xs text-slate-500">
                        Dikirim: {{ $review->created_at->format('d F Y H:i') }}
                        @if ($review->approved_at)
                            · Disetujui: {{ $review->approved_at->format('d F Y') }}
                        @endif
                    </div>

                    {{-- Tombol Edit (kalau status rejected/pending) --}}
                    @if (in_array($review->status, ['pending', 'rejected']))
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-xs uppercase tracking-wide text-slate-500 mb-3">Ubah Ulasan</p>
                            <form method="POST" action="{{ route('tenant.reviews.update', $review) }}" class="space-y-4">
                                @csrf
                                @method('PUT')
                                @include('tenant.reviews._form', ['review' => $review])
                                <div class="flex gap-2">
                                    <x-ui.button type="submit">
                                        <x-icon name="check" :size="16" />
                                        Simpan Perubahan
                                    </x-ui.button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                            <p class="text-sm text-emerald-800">✅ Ulasan Anda sudah disetujui dan tampil di landing page.</p>
                        </div>
                    @endif
                </x-ui.card>

            @else
                {{-- Belum ada ulasan — form baru --}}
                <x-ui.card title="Beri Ulasan">
                    <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <x-icon name="alert-circle" :size="20" class="mt-0.5 shrink-0 text-indigo-600" />
                            <div class="text-sm">
                                <p class="font-medium text-indigo-800">Bagikan pengalaman Anda</p>
                                <p class="mt-1 text-indigo-700">Ulasan Anda akan dimoderasi admin sebelum tampil di landing page.</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('tenant.reviews.store') }}" class="space-y-4">
                        @csrf
                        @include('tenant.reviews._form', ['review' => null])
                        <x-ui.button type="submit">
                            <x-icon name="check" :size="16" />
                            Kirim Ulasan
                        </x-ui.button>
                    </form>
                </x-ui.card>
            @endif
        </div>

        {{-- Kanan: Info --}}
        <div class="space-y-6">
            <x-ui.card title="Rating Kos">
                <div class="text-center py-4">
                    <p class="text-5xl font-bold text-amber-500">{{ number_format($averageRating, 1) }}</p>
                    <div class="mt-2 flex justify-center gap-0.5 text-amber-500">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="{{ $i <= round($averageRating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="mt-2 text-sm text-slate-500">dari {{ $totalReviews }} ulasan</p>
                </div>
            </x-ui.card>

            <x-ui.card title="Tips Menulis Ulasan">
                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex gap-2">
                        <span class="text-emerald-600">✓</span>
                        <span>Jujur & spesifik — sebutkan apa yang bagus & kurang</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="text-emerald-600">✓</span>
                        <span>Fokus pada fasilitas, kebersihan, dan pelayanan</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="text-emerald-600">✓</span>
                        <span>Hindari kata kasar atau SARA</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="text-emerald-600">✓</span>
                        <span>Minimal 10 karakter, maksimal 500</span>
                    </li>
                </ul>
            </x-ui.card>
        </div>
    </div>
@endsection