@extends('layouts.admin')

@section('title', 'Detail Lead')

@section('content')
    <x-ui.page-header :title="$lead->name" subtitle="Detail lead / calon penghuni">
        <x-ui.button :href="route('admin.leads.index')" variant="secondary">
            <x-icon name="chevron-right" :size="16" class="rotate-180" />
            Kembali
        </x-ui.button>
        <x-ui.confirm-button :action="route('admin.leads.destroy', $lead)"
            label="Hapus Lead" variant="danger" size="md"
            message="Hapus lead dari {{ $lead->name }}? Tidak bisa dibatalkan." />
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Info Lead --}}
        <x-ui.card title="Informasi Lead" class="lg:col-span-2">
            <div class="flex items-start justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                    <div class="mt-1"><x-ui.badge :status="$lead->status" /></div>
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Masuk</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ $lead->created_at->format('d F Y H:i') }}</p>
                </div>
            </div>

            <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-500">Nama</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $lead->name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">No. HP / WA</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $lead->phone }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Email</dt>
                    <dd class="mt-1 text-slate-800">{{ $lead->email ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Sumber</dt>
                    <dd class="mt-1 text-slate-800">{{ $sourceLabels[$lead->source] ?? $lead->source }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Kamar Diminati</dt>
                    <dd class="mt-1 font-medium text-slate-800">
                        @if ($lead->room)
                            {{ $lead->room->room_number }}
                            · {{ $lead->room->property?->name }}
                            <span class="ml-1 text-xs text-slate-500">(Rp {{ number_format($lead->room->price, 0, ',', '.') }}/bln)</span>
                        @else
                            <span class="text-slate-400">Belum spesifik</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Property</dt>
                    <dd class="mt-1 text-slate-800">{{ $lead->property?->name ?: '-' }}</dd>
                </div>
                @if ($lead->message)
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500">Pesan</dt>
                        <dd class="mt-1 rounded-lg bg-slate-50 p-3 text-slate-700">{{ $lead->message }}</dd>
                    </div>
                @endif
            </dl>
        </x-ui.card>

        {{-- Panel Aksi --}}
        <div class="space-y-6">
            {{-- Follow-up --}}
            <x-ui.card title="Follow-up">
                <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                        <select id="status" name="status" required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach ($statusLabels as $key => $label)
                                <option value="{{ $key }}" @selected(old('status', $lead->status) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="follow_up_notes" class="mb-1 block text-sm font-medium text-slate-700">Catatan Follow-up</label>
                        <textarea id="follow_up_notes" name="follow_up_notes" rows="3"
                                  class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Contoh: Sudah dihubungi via WA, akan survei Sabtu.">{{ old('follow_up_notes', $lead->follow_up_notes) }}</textarea>
                        @error('follow_up_notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <x-ui.button type="submit" class="w-full justify-center">
                        <x-icon name="check" :size="16" />
                        Update
                    </x-ui.button>
                </form>
            </x-ui.card>

            {{-- Aksi Cepat --}}
            <x-ui.card title="Aksi Cepat">
                <div class="space-y-2">
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $lead->phone) }}"
                       target="_blank"
                       class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition">
                        <x-icon name="inbox" :size="16" />
                        Chat via WhatsApp
                    </a>
                    <a href="tel:{{ $lead->phone }}"
                       class="flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <x-icon name="user" :size="16" />
                        Telepon
                    </a>
                    <a href="{{ route('admin.leads.convert', $lead) }}"
                       class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
                        <x-icon name="check" :size="16" />
                        Convert ke Penghuni
                    </a>
                </div>
            </x-ui.card>
        </div>
    </div>
@endsection