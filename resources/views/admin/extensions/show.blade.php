@extends('layouts.admin')

@section('title', 'Detail Perpanjangan')

@section('content')
    <x-ui.page-header title="Detail Perpanjangan" :subtitle="$extension->tenant?->full_name">
        <x-ui.button :href="route('admin.extensions.index')" variant="secondary">
            <x-icon name="chevron-right" :size="16" class="rotate-180" />
            Kembali
        </x-ui.button>
        <x-ui.confirm-button :action="route('admin.extensions.destroy', $extension)"
            label="Hapus" variant="danger" size="md"
            message="Hapus pengajuan ini? Tidak bisa dibatalkan." />
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Detail Pengajuan --}}
        <x-ui.card title="Detail Pengajuan" class="lg:col-span-2">
            <div class="flex items-start justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge :status="$extension->status" />
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Diajukan</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ $extension->created_at->format('d F Y H:i') }}</p>
                </div>
            </div>

            <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-500">Penghuni</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $extension->tenant?->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Kamar</dt>
                    <dd class="mt-1 font-medium text-slate-800">
                        {{ $extension->tenant?->room?->room_number }}
                        · {{ $extension->tenant?->room?->property?->name }}
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Email</dt>
                    <dd class="mt-1 text-slate-800">{{ $extension->tenant?->user?->email }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">No. HP</dt>
                    <dd class="mt-1 text-slate-800">{{ $extension->tenant?->phone }}</dd>
                </div>
            </dl>

            {{-- Perbandingan tanggal --}}
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs uppercase tracking-wide text-slate-500 mb-3">Perpanjangan</p>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-slate-50 p-4">
                        <p class="text-xs text-slate-500">Berakhir Saat Ini</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $extension->current_end_date->format('d F Y') }}</p>
                    </div>
                    <div class="rounded-lg bg-indigo-50 p-4">
                        <p class="text-xs text-indigo-600">Diajukan Sampai</p>
                        <p class="mt-1 font-semibold text-indigo-700">{{ $extension->requested_end_date->format('d F Y') }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-4">
                        <p class="text-xs text-emerald-600">Durasi</p>
                        <p class="mt-1 font-semibold text-emerald-700">{{ $extension->duration_months }} bulan</p>
                    </div>
                </div>
                <div class="mt-4 rounded-lg bg-amber-50 p-4">
                    <p class="text-xs text-amber-600">Biaya Tambahan</p>
                    <p class="mt-1 text-2xl font-bold text-amber-700">Rp {{ number_format($extension->additional_cost, 0, ',', '.') }}</p>
                </div>
            </div>

            @if ($extension->notes)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Catatan Penghuni</p>
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-700">{{ $extension->notes }}</p>
                </div>
            @endif

            @if ($extension->admin_notes)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Catatan Admin</p>
                    <p class="rounded-lg bg-indigo-50 p-4 text-sm text-indigo-900">{{ $extension->admin_notes }}</p>
                </div>
            @endif

            @if ($extension->approved_at)
                <div class="mt-4 text-xs text-slate-500">
                    Diproses: {{ $extension->approved_at->format('d F Y H:i') }}
                </div>
            @endif
        </x-ui.card>

        {{-- Panel Aksi --}}
        <div class="space-y-6">
            @if ($extension->status === 'pending')
                {{-- Approve --}}
                <x-ui.card title="Setujui Perpanjangan">
                    <form method="POST" action="{{ route('admin.extensions.approve', $extension) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="approve_notes" class="mb-1 block text-sm font-medium text-slate-700">Catatan (opsional)</label>
                            <textarea id="approve_notes" name="admin_notes" rows="3"
                                      class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Contoh: Disetujui, harga tetap.">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition"
                                onclick="return confirm('Setujui perpanjangan ini?\n\nTanggal selesai sewa akan diperbarui ke {{ $extension->requested_end_date->format('d F Y') }} dan invoice tambahan akan dibuat.')">
                            <x-icon name="check" :size="16" />
                            Setujui
                        </button>
                    </form>
                </x-ui.card>

                {{-- Reject --}}
                <x-ui.card title="Tolak Perpanjangan">
                    <form method="POST" action="{{ route('admin.extensions.reject', $extension) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="reject_notes" class="mb-1 block text-sm font-medium text-slate-700">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="reject_notes" name="admin_notes" rows="3" required
                                      class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Contoh: Kamar sudah dibooking penghuni lain.">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 transition"
                                onclick="return confirm('Tolak perpanjangan ini?')">
                            <x-icon name="x" :size="16" />
                            Tolak
                        </button>
                    </form>
                </x-ui.card>
            @else
                <x-ui.card title="Status">
                    <div class="py-4 text-center">
                        <x-ui.badge :status="$extension->status" />
                        <p class="mt-3 text-sm text-slate-500">
                            Pengajuan ini sudah
                            {{ $extension->status === 'approved' ? 'disetujui' : 'ditolak' }}.
                        </p>
                    </div>
                </x-ui.card>
            @endif

            {{-- Info Tenant --}}
            <x-ui.card title="Penghuni">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-slate-500">Nama</dt>
                        <dd class="font-medium text-slate-800">{{ $extension->tenant?->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Kamar</dt>
                        <dd class="font-medium text-slate-800">{{ $extension->tenant?->room?->room_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">HP</dt>
                        <dd class="text-slate-800">{{ $extension->tenant?->phone }}</dd>
                    </div>
                </dl>
                @if ($extension->tenant)
                    <x-ui.button :href="route('admin.tenants.show', $extension->tenant)" variant="secondary" size="sm" class="mt-4 w-full justify-center">
                        Lihat Penghuni
                    </x-ui.button>
                @endif
            </x-ui.card>
        </div>
    </div>
@endsection