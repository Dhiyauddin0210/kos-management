@extends('layouts.admin')

@section('title', 'Detail Pembayaran')

@section('content')
    <x-ui.page-header title="Detail Pembayaran" :subtitle="$payment->invoice?->invoice_number">
        <x-ui.button :href="route('admin.payments.index')" variant="secondary">
            <x-icon name="chevron-right" :size="16" class="rotate-180" />
            Kembali
        </x-ui.button>
        @if ($payment->status === 'pending')
            <x-ui.confirm-button
                :action="route('admin.payments.reject', $payment)"
                label="Tolak"
                variant="danger"
                size="md"
                message="Tolak pembayaran ini? Invoice akan kembali ke status Belum Bayar." />
        @endif
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Detail Pembayaran --}}
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card title="Informasi Pembayaran">
                <div class="flex items-start justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Jumlah Dibayar</p>
                        <p class="mt-1 text-3xl font-bold text-indigo-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </div>
                    <x-ui.badge :status="$payment->status" />
                </div>

                <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-500">Tanggal Bayar</dt>
                        <dd class="mt-1 font-medium text-slate-800">{{ $payment->payment_date->format('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">No. Invoice</dt>
                        <dd class="mt-1 font-mono text-sm text-slate-800">{{ $payment->invoice?->invoice_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Penghuni</dt>
                        <dd class="mt-1 font-medium text-slate-800">{{ $payment->invoice?->tenant?->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Kamar</dt>
                        <dd class="mt-1 font-medium text-slate-800">
                            {{ $payment->invoice?->tenant?->room?->room_number }}
                            · {{ $payment->invoice?->tenant?->room?->property?->name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Email</dt>
                        <dd class="mt-1 text-slate-800">{{ $payment->invoice?->tenant?->user?->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">No. HP</dt>
                        <dd class="mt-1 text-slate-800">{{ $payment->invoice?->tenant?->phone }}</dd>
                    </div>
                    @if ($payment->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-slate-500">Catatan</dt>
                            <dd class="mt-1 text-slate-700">{{ $payment->notes }}</dd>
                        </div>
                    @endif
                    @if ($payment->verifier)
                        <div class="sm:col-span-2 mt-2 pt-4 border-t border-slate-100">
                            <dt class="text-slate-500">Diproses Oleh</dt>
                            <dd class="mt-1 text-slate-800">
                                {{ $payment->verifier->name }}
                                @if ($payment->verified_at)
                                    · {{ $payment->verified_at->format('d F Y H:i') }}
                                @endif
                            </dd>
                        </div>
                    @endif
                </dl>

                {{-- Tombol Aksi --}}
                @if ($payment->status === 'pending')
                    <div class="mt-6 pt-6 border-t border-slate-100 space-y-4">
                        <div>
                            <label for="verify_notes" class="mb-1 block text-sm font-medium text-slate-700">
                                Catatan (opsional untuk verifikasi)
                            </label>
                            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" id="verifyForm">
                                @csrf
                                <textarea id="verify_notes" name="notes" rows="2"
                                          class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Contoh: Transfer via BCA, sudah sesuai.">{{ old('notes') }}</textarea>
                                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </form>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button type="submit" form="verifyForm"
                                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition"
                                    onclick="return confirm('Verifikasi pembayaran ini? Invoice akan ditandai Lunas.')">
                                <x-icon name="check" :size="16" />
                                Verifikasi & Tandai Lunas
                            </button>
                            <x-ui.confirm-button
                                :action="route('admin.payments.reject', $payment)"
                                label="Tolak Pembayaran"
                                variant="danger"
                                size="md"
                                message="Tolak pembayaran ini? Invoice akan kembali ke status Belum Bayar." />
                        </div>
                    </div>
                @endif
            </x-ui.card>
        </div>

        {{-- Bukti Transfer --}}
        <x-ui.card title="Bukti Transfer">
            @if ($payment->proof)
                <div x-data="{ open: false }">
                    <div class="relative overflow-hidden rounded-lg border border-slate-200 group">
                        <img src="{{ asset('storage/' . $payment->proof) }}"
                             alt="Bukti Transfer"
                             class="w-full h-auto object-cover cursor-zoom-in transition group-hover:scale-105"
                             @click="open = true">
                        <button type="button"
                                class="absolute bottom-2 right-2 rounded-lg bg-slate-900/80 px-3 py-1.5 text-xs font-medium text-white backdrop-blur"
                                @click="open = true">
                            <x-icon name="eye" :size="14" class="inline" />
                            Perbesar
                        </button>
                    </div>

                    {{-- Modal Zoom --}}
                    <div x-show="open" x-cloak @click="open = false"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/90 p-4">
                        <div @click.stop class="max-h-[90vh] max-w-4xl overflow-auto">
                            <img src="{{ asset('storage/' . $payment->proof) }}" alt="Bukti Transfer" class="max-w-full h-auto rounded-lg">
                        </div>
                        <button type="button" @click="open = false"
                                class="absolute top-4 right-4 rounded-full bg-white/10 p-2 text-white hover:bg-white/20">
                            <x-icon name="x" :size="24" />
                        </button>
                    </div>
                </div>
            @else
                <div class="py-8 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 mb-3">
                        <x-icon name="file-text" :size="24" class="text-slate-400" />
                    </div>
                    <p class="text-sm text-slate-500">Belum ada bukti transfer</p>
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection