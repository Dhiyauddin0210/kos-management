@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
    <x-ui.page-header title="Pengaturan" subtitle="Konfigurasi Kos Adin" />

    <div x-data="{ tab: '{{ request('tab', 'info') }}' }">
        {{-- Tab Navigation --}}
        <div class="mb-6 border-b border-slate-200">
            <nav class="flex gap-6 overflow-x-auto">
                @foreach ([
                    'info'    => 'Info Kos',
                    'billing' => 'Tagihan',
                    'qr'      => 'QR Code',
                    'bank'    => 'Rekening Bank',
                ] as $key => $label)
                    <button type="button" @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            class="whitespace-nowrap border-b-2 py-4 text-sm font-medium transition">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- ==================== TAB 1: INFO KOS ==================== --}}
        <div x-show="tab === 'info'" x-cloak>
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="_tab" value="info">
                <input type="hidden" name="default_due_date" value="{{ $settings['default_due_date'] ?? 10 }}">
                <input type="hidden" name="reminder_days_before" value="{{ $settings['reminder_days_before'] ?? 3 }}">

                <x-ui.card title="Informasi Kos">
                    <div class="grid gap-5 md:grid-cols-2">
                        <x-ui.input name="kos_name" label="Nama Kos" :value="$settings['kos_name']" required placeholder="Contoh: Kos Adin" />
                        <x-ui.input name="kos_phone" label="No. Telepon" :value="$settings['kos_phone']" placeholder="021-1234567" />

                        <x-ui.input name="kos_tagline" label="Tagline" :value="$settings['kos_tagline']" placeholder="Hunian nyaman di pusat kota" class="md:col-span-2" />

                        <x-ui.input name="kos_address" label="Alamat Lengkap" :value="$settings['kos_address']" placeholder="Jl. Contoh No. 123, Kota" class="md:col-span-2" />

                        <x-ui.input name="kos_whatsapp" label="No. WhatsApp" :value="$settings['kos_whatsapp']" placeholder="6281234567890" hint="Format internasional tanpa + dan spasi." />

                        <x-ui.input name="kos_email" type="email" label="Email" :value="$settings['kos_email']" placeholder="info@kosadin.com" />

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Logo Kos</label>
                            <div class="flex items-center gap-4" x-data="{ preview: @js($settings['kos_logo'] ? asset('storage/' . $settings['kos_logo']) : null) }">
                                <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                                    <template x-if="preview">
                                        <img :src="preview" alt="Preview Logo" class="h-full w-full object-contain p-1">
                                    </template>
                                    <span x-show="!preview" class="text-center text-xs text-slate-400">Belum ada logo</span>
                                </div>
                                <input type="file" name="kos_logo" accept="image/*"
                                       @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f) }"
                                       class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Format: JPG, PNG, WEBP, SVG. Maksimal 1 MB.</p>
                            @error('kos_logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                        <x-ui.button type="submit">
                            <x-icon name="check" :size="16" />
                            Simpan Pengaturan
                        </x-ui.button>
                    </div>
                </x-ui.card>
            </form>
        </div>

        {{-- ==================== TAB 2: TAGIHAN ==================== --}}
        <div x-show="tab === 'billing'" x-cloak>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_tab" value="billing">
                <input type="hidden" name="kos_name" value="{{ $settings['kos_name'] }}">

                <x-ui.card title="Pengaturan Tagihan">
                    <div class="grid gap-5 md:grid-cols-2">
                        <x-ui.input name="default_due_date" type="number" label="Tanggal Jatuh Tempo Default"
                                    :value="$settings['default_due_date']" min="1" max="28" required
                                    hint="Tanggal berapa setiap bulan tagihan jatuh tempo (1-28)." />

                        <x-ui.input name="reminder_days_before" type="number" label="Reminder Sebelum Jatuh Tempo"
                                    :value="$settings['reminder_days_before']" min="1" max="30" required
                                    hint="Berapa hari sebelum jatuh tempo reminder dikirim." />
                    </div>

                    <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                        <x-ui.button type="submit">
                            <x-icon name="check" :size="16" />
                            Simpan Pengaturan
                        </x-ui.button>
                    </div>
                </x-ui.card>
            </form>
        </div>

        {{-- ==================== TAB 3: QR CODE ==================== --}}
        <div x-show="tab === 'qr'" x-cloak>
            <div class="grid gap-6 lg:grid-cols-2">
                <x-ui.card title="URL Tujuan QR">
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_tab" value="qr">
                        <input type="hidden" name="kos_name" value="{{ $settings['kos_name'] }}">
                        <input type="hidden" name="default_due_date" value="{{ $settings['default_due_date'] ?? 10 }}">
                        <input type="hidden" name="reminder_days_before" value="{{ $settings['reminder_days_before'] ?? 3 }}">

                        <div>
                            <label for="qr_base_url" class="mb-1 block text-sm font-medium text-slate-700">URL Tujuan QR</label>
                            <input type="text" id="qr_base_url" name="qr_base_url"
                                   value="{{ old('qr_base_url', $settings['qr_base_url']) }}"
                                   placeholder="https://kosadin.com/kos"
                                   class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-slate-500">URL ini yang akan di-encode ke QR code.</p>
                            @error('qr_base_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <x-ui.button type="submit">
                                <x-icon name="check" :size="16" />
                                Simpan URL
                            </x-ui.button>
                        </div>
                    </form>
                </x-ui.card>

                <x-ui.card title="Preview QR Code">
                    <div class="flex flex-col items-center gap-4">
                        <div class="rounded-lg border border-slate-200 bg-white p-4">
                            <img src="{{ route('admin.settings.qr-preview') }}?url={{ urlencode($settings['qr_base_url']) }}&size=300&t={{ now()->timestamp }}"
                                 alt="QR Code Preview"
                                 class="h-64 w-64 bg-white">
                        </div>
                        <p class="break-all text-center text-xs text-slate-500">{{ $settings['qr_base_url'] }}</p>
                        <a href="{{ route('admin.settings.qr-code') }}?url={{ urlencode($settings['qr_base_url']) }}&size=600"
                           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
                            <x-icon name="file-text" :size="16" />
                            Download QR (SVG)
                        </a>
                    </div>
                </x-ui.card>
            </div>
        </div>

        {{-- ==================== TAB 4: REKENING BANK ==================== --}}
        <div x-show="tab === 'bank'" x-cloak>
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <x-ui.card title="Daftar Rekening Bank" flush>
                        @if ($bankAccounts->isEmpty())
                            <div class="py-12 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 mb-3">
                                    <x-icon name="credit-card" :size="24" class="text-slate-400" />
                                </div>
                                <p class="text-slate-500">Belum ada rekening.</p>
                            </div>
                        @else
                            <div class="divide-y divide-slate-100">
                                @foreach ($bankAccounts as $bank)
                                    <div class="flex items-center justify-between gap-4 p-4">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-white font-bold">
                                                {{ strtoupper(substr($bank->bank_name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <p class="font-semibold text-slate-800">{{ $bank->bank_name }}</p>
                                                    @if ($bank->is_primary)
                                                        <span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">Utama</span>
                                                    @endif
                                                </div>
                                                <p class="font-mono text-sm text-slate-600">{{ $bank->account_number }}</p>
                                                <p class="text-xs text-slate-500">a/n {{ $bank->account_holder }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button"
                                                    @click="$dispatch('open-edit-bank', {{ $bank->toJson() }})"
                                                    class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">
                                                Edit
                                            </button>
                                            <x-ui.confirm-button :action="route('admin.bank-accounts.destroy', $bank)"
                                                message="Hapus rekening {{ $bank->bank_name }} - {{ $bank->account_number }}?" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </x-ui.card>
                </div>

                <div>
                    <x-ui.card title="Tambah Rekening">
                        <form method="POST" action="{{ route('admin.bank-accounts.store') }}" class="space-y-4">
                            @csrf
                            <x-ui.input name="bank_name" label="Nama Bank" required placeholder="BCA / Mandiri / BNI" />
                            <x-ui.input name="account_number" label="No. Rekening" required placeholder="1234567890" />
                            <x-ui.input name="account_holder" label="Atas Nama" required placeholder="Nama pemilik rekening" />

                            <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                                <input type="hidden" name="is_primary" value="0">
                                <input type="checkbox" name="is_primary" value="1"
                                       class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Jadikan rekening utama
                            </label>

                            <x-ui.button type="submit" class="w-full justify-center">
                                <x-icon name="plus" :size="16" />
                                Tambah Rekening
                            </x-ui.button>
                        </form>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Rekening --}}
    <div x-data="{ open: false, bank: {} }"
         @open-edit-bank.window="open = true; bank = $event.detail"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
        <div @click.away="open = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800">Edit Rekening</h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600">
                    <x-icon name="x" :size="20" />
                </button>
            </div>
            <form method="POST" :action="'/admin/bank-accounts/' + bank.id" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Nama Bank</label>
                    <input type="text" name="bank_name" x-model="bank.bank_name" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">No. Rekening</label>
                    <input type="text" name="account_number" x-model="bank.account_number" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Atas Nama</label>
                    <input type="text" name="account_holder" x-model="bank.account_holder" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                    <input type="hidden" name="is_primary" value="0">
                    <input type="checkbox" name="is_primary" value="1" x-model="bank.is_primary"
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Rekening utama
                </label>
                <div class="flex gap-2 pt-2">
                    <x-ui.button type="submit" class="flex-1 justify-center">Simpan</x-ui.button>
                    <button type="button" @click="open = false"
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection