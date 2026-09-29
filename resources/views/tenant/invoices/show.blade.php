@extends('layouts.tenant')

@section('title', 'Detail Tagihan')

@section('content')
    <x-ui.page-header title="Detail Tagihan" :subtitle="$invoice->invoice_number">
        <x-ui.button :href="route('tenant.invoices.index')" variant="secondary">
            <x-icon name="chevron-right" :size="16" class="rotate-180" />
            Kembali
        </x-ui.button>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Kolom Kiri --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Tagihan --}}
            <x-ui.card title="Informasi Tagihan">
                <div class="flex items-start justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Jumlah Tagihan</p>
                        <p class="mt-1 text-3xl font-bold text-indigo-600">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</p>
                    </div>
                    <x-ui.badge :status="$invoice->status" />
                </div>

                <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-500">No. Invoice</dt>
                        <dd class="mt-1 font-mono text-sm text-slate-800">{{ $invoice->invoice_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Periode</dt>
                        <dd class="mt-1 font-medium text-slate-800">{{ sprintf('%02d/%d', $invoice->month, $invoice->year) }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Kamar</dt>
                        <dd class="mt-1 font-medium text-slate-800">{{ $invoice->tenant->room->room_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Jatuh Tempo</dt>
                        <dd class="mt-1 font-medium {{ $invoice->status === 'overdue' ? 'text-rose-600' : 'text-slate-800' }}">
                            {{ $invoice->due_date->format('d F Y') }}
                        </dd>
                    </div>
                    @if ($invoice->paid_at)
                        <div class="sm:col-span-2">
                            <dt class="text-slate-500">Dibayar Pada</dt>
                            <dd class="mt-1 font-medium text-emerald-600">{{ $invoice->paid_at->format('d F Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </x-ui.card>

            {{-- Form Upload Bukti Transfer --}}
            @if (in_array($invoice->status, ['unpaid', 'overdue']))
                <x-ui.card title="Upload Bukti Transfer">
                    <form method="POST" action="{{ route('tenant.invoices.pay', $invoice) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="alert-circle" :size="20" class="mt-0.5 shrink-0 text-amber-600" />
                                <div class="text-sm">
                                    <p class="font-medium text-amber-800">Petunjuk Transfer</p>
                                    <p class="mt-1 text-amber-700">Pilih rekening tujuan di kanan, transfer, lalu upload bukti transfer di form ini.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="amount" class="mb-1 block text-sm font-medium text-slate-700">
                                    Jumlah Transfer <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500 font-medium">Rp</span>
                                    <input type="number" id="amount" name="amount" required min="1" step="any"
                                           value="{{ old('amount', (int) $invoice->amount) }}"
                                           class="w-full pl-10 rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <p class="mt-1 text-xs text-slate-500">Jumlah sesuai tagihan: <strong>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</strong></p>
                                @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="payment_date" class="mb-1 block text-sm font-medium text-slate-700">
                                    Tanggal Transfer <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="payment_date" name="payment_date" required
                                       value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                       max="{{ now()->format('Y-m-d') }}"
                                       class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @error('payment_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="proof" class="mb-1 block text-sm font-medium text-slate-700">
                                Bukti Transfer <span class="text-red-500">*</span>
                            </label>
                            <input type="file" id="proof" name="proof" required accept="image/*"
                                   class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100">
                            <p class="mt-1 text-xs text-slate-500">Format JPG/PNG/WEBP, maks 2 MB.</p>
                            @error('proof') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="notes" class="mb-1 block text-sm font-medium text-slate-700">Catatan (opsional)</label>
                            <textarea id="notes" name="notes" rows="2"
                                      class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                      placeholder="Contoh: Transfer via BCA atas nama sendiri">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition"
                                onclick="return confirm('Upload bukti transfer ini? Pastikan data sudah benar.')">
                            <x-icon name="check" :size="16" />
                            Kirim Bukti Transfer
                        </button>
                    </form>
                </x-ui.card>
            @elseif ($invoice->status === 'pending')
                <x-ui.card title="Status Pembayaran">
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <div class="flex items-start gap-3">
                            <x-icon name="clock" :size="20" class="mt-0.5 shrink-0 text-amber-600" />
                            <div class="text-sm">
                                <p class="font-medium text-amber-800">Menunggu Verifikasi Admin</p>
                                <p class="mt-1 text-amber-700">Bukti transfer Anda sudah dikirim. Admin akan memverifikasi dalam 1-2 hari kerja.</p>
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            @elseif ($invoice->status === 'paid')
                <x-ui.card title="Status Pembayaran">
                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                        <div class="flex items-start gap-3">
                            <x-icon name="check" :size="20" class="mt-0.5 shrink-0 text-emerald-600" />
                            <div class="text-sm">
                                <p class="font-medium text-emerald-800">Pembayaran Lunas ✅</p>
                                <p class="mt-1 text-emerald-700">Terima kasih! Pembayaran Anda sudah diverifikasi oleh admin.</p>
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            @endif

            {{-- Riwayat Pembayaran --}}
            @if ($invoice->payments->isNotEmpty())
                <x-ui.card title="Riwayat Pembayaran">
                    <div class="space-y-3">
                        @foreach ($invoice->payments as $pay)
                            <div class="rounded-lg border border-slate-200 p-4">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div>
                                        <p class="font-semibold text-slate-800">Rp {{ number_format($pay->amount, 0, ',', '.') }}</p>
                                        <p class="text-xs text-slate-500">{{ $pay->payment_date->format('d F Y') }}</p>
                                    </div>
                                    <x-ui.badge :status="$pay->status" />
                                </div>
                                @if ($pay->notes)
                                    <p class="text-sm text-slate-600 mt-2">{{ $pay->notes }}</p>
                                @endif
                                @if ($pay->proof)
                                    <a href="{{ asset('storage/' . $pay->proof) }}" target="_blank"
                                       class="mt-2 inline-flex items-center gap-1 text-xs text-emerald-600 hover:underline">
                                        <x-icon name="eye" :size="12" />
                                        Lihat Bukti Transfer
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif
        </div>

        {{-- Kolom Kanan: Rekening Bank (bisa dipilih) --}}
        <x-ui.card title="Rekening Tujuan Transfer">
            @if ($bankAccounts->isEmpty())
                <p class="py-6 text-center text-sm text-slate-400">Belum ada rekening.</p>
            @else
                <div class="space-y-3">
                    @foreach ($bankAccounts as $bank)
                        <label class="block cursor-pointer">
                            <input type="radio" name="bank_account_id" value="{{ $bank->id }}"
                                   class="peer sr-only"
                                   @checked($loop->first)>
                            <div class="rounded-lg border-2 border-slate-200 p-4 transition peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:border-emerald-300">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-bold text-sm">
                                        {{ strtoupper(substr($bank->bank_name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-slate-800">{{ $bank->bank_name }}</p>
                                            @if ($bank->is_primary)
                                                <span class="rounded bg-emerald-200 px-1.5 py-0.5 text-[10px] font-medium text-emerald-800">Utama</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="hidden peer-checked:block text-emerald-600">
                                        <x-icon name="check" :size="20" />
                                    </div>
                                </div>
                                <p class="font-mono text-lg font-bold text-slate-800">{{ $bank->account_number }}</p>
                                <p class="text-xs text-slate-500">a/n {{ $bank->account_holder }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-slate-500">Klik salah satu rekening untuk memilih tujuan transfer.</p>
            @endif
        </x-ui.card>
    </div>
@endsection