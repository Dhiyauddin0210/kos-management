@extends('layouts.tenant')

@section('title', 'Ajukan Perpanjangan')

@section('content')
    <x-ui.page-header title="Ajukan Perpanjangan" subtitle="Perpanjang kontrak sewa kamar Anda" />

    @if ($hasPending)
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <div class="flex items-start gap-3">
                <x-icon name="alert-circle" :size="20" class="mt-0.5 shrink-0 text-amber-600" />
                <div class="text-sm">
                    <p class="font-medium text-amber-800">Anda masih punya pengajuan yang menunggu</p>
                    <p class="mt-1 text-amber-700">Tunggu admin memproses pengajuan sebelumnya sebelum mengajukan yang baru.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-ui.card>
                <form method="POST" action="{{ route('tenant.extensions.store') }}"
                      x-data="{
                        duration: {{ old('duration_months', 12) }},
                        pricePerMonth: {{ (int) $tenant->room->price }},
                        get total() { return this.duration * this.pricePerMonth }
                      }">
                    @csrf

                    {{-- Info Kamar --}}
                    <div class="mb-6 rounded-lg bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500 mb-3">Info Kamar</p>
                        <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-slate-500">Kamar</dt>
                                <dd class="font-medium text-slate-800">{{ $tenant->room->room_number }} · {{ $tenant->room->type }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Harga per Bulan</dt>
                                <dd class="font-medium text-slate-800">Rp {{ number_format($tenant->room->price, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Kontrak Saat Ini Berakhir</dt>
                                <dd class="font-medium text-slate-800">{{ $tenant->end_date->format('d F Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Durasi --}}
                    <div class="mb-5">
                        <label for="duration_months" class="mb-1 block text-sm font-medium text-slate-700">
                            Durasi Perpanjangan <span class="text-red-500">*</span>
                        </label>
                        <select id="duration_months" name="duration_months" required x-model.number="duration"
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ([1, 3, 6, 12, 24] as $m)
                                <option value="{{ $m }}">{{ $m }} bulan</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Minimal 1 bulan, maksimal 24 bulan.</p>
                        @error('duration_months') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Preview --}}
                    <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-emerald-600 font-semibold mb-2">Preview Perpanjangan</p>
                        <dl class="grid gap-2 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-slate-500">Tanggal Selesai Baru</dt>
                                <dd class="font-medium text-emerald-700" x-text="new Date(new Date('{{ $tenant->end_date->format('Y-m-d') }}').setMonth(new Date('{{ $tenant->end_date->format('Y-m-d') }}').getMonth() + parseInt(duration))).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Total Biaya</dt>
                                <dd class="font-bold text-emerald-700" x-text="'Rp ' + total.toLocaleString('id-ID')"></dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-5">
                        <label for="notes" class="mb-1 block text-sm font-medium text-slate-700">Catatan (opsional)</label>
                        <textarea id="notes" name="notes" rows="3" maxlength="500"
                                  placeholder="Contoh: Saya ingin perpanjang 12 bulan lagi, masih nyaman di sini."
                                  class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes') }}</textarea>
                        @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3 border-t border-slate-100 pt-5">
                        <button type="submit" @disabled($hasPending)
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                onclick="return confirm('Ajukan perpanjangan ini? Tunggu persetujuan admin.')">
                            <x-icon name="check" :size="16" />
                            Ajukan Perpanjangan
                        </button>
                        <x-ui.button :href="route('tenant.extensions.index')" variant="secondary">Batal</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>

        {{-- Info Tambahan --}}
        <div>
            <x-ui.card title="Cara Kerja">
                <ol class="space-y-3 text-sm text-slate-600">
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">1</span>
                        <span>Pilih durasi perpanjangan yang diinginkan.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">2</span>
                        <span>Ajukan, tunggu admin memproses.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">3</span>
                        <span>Kalau disetujui, invoice tambahan akan dibuat.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">4</span>
                        <span>Bayar invoice lewat halaman Tagihan Saya.</span>
                    </li>
                </ol>
            </x-ui.card>
        </div>
    </div>
@endsection