@extends('layouts.admin')

@section('title', 'Detail Tagihan ' . $invoice->invoice_number)

@section('content')
    <x-ui.page-header :title="'Tagihan ' . $invoice->invoice_number" :subtitle="$invoice->tenant?->full_name">
        <x-ui.button :href="route('admin.invoices.index')" variant="secondary">
            <x-icon name="chevron-right" :size="16" class="rotate-180" />
            Kembali
        </x-ui.button>
        @if ($invoice->status !== 'paid')
            <form method="POST" action="{{ route('admin.invoices.send-reminder', $invoice) }}" class="inline">
                @csrf
                <x-ui.button type="submit" variant="secondary">
                    <x-icon name="bell" :size="16" />
                    Kirim Reminder
                </x-ui.button>
            </form>
        @endif
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Detail Tagihan --}}
        <x-ui.card title="Detail Tagihan" class="lg:col-span-2">
            <div class="flex items-start justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">No. Invoice</p>
                    <p class="mt-1 font-mono text-lg font-bold text-slate-800">{{ $invoice->invoice_number }}</p>
                </div>
                <x-ui.badge :status="$invoice->status" />
            </div>

            <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-500">Penghuni</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $invoice->tenant?->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Kamar</dt>
                    <dd class="mt-1 font-medium text-slate-800">
                        {{ $invoice->tenant?->room?->room_number }}
                        · {{ $invoice->tenant?->room?->property?->name }}
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Email</dt>
                    <dd class="mt-1 text-slate-800">{{ $invoice->tenant?->user?->email }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">No. HP</dt>
                    <dd class="mt-1 text-slate-800">{{ $invoice->tenant?->phone }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Periode</dt>
                    <dd class="mt-1 font-medium text-slate-800">
                        {{ \Carbon\Carbon::create()->month($invoice->month)->translatedFormat('F') }} {{ $invoice->year }}
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Jatuh Tempo</dt>
                    <dd class="mt-1 font-medium text-slate-800">
                        {{ $invoice->due_date->format('d F Y') }}
                        @if ($invoice->status === 'overdue')
                            <span class="ml-2 text-xs text-rose-500">({{ $invoice->due_date->diffForHumans() }})</span>
                        @endif
                    </dd>
                </div>
                @if ($invoice->paid_at)
                    <div>
                        <dt class="text-slate-500">Dibayar Pada</dt>
                        <dd class="mt-1 font-medium text-emerald-600">{{ $invoice->paid_at->format('d F Y H:i') }}</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs uppercase tracking-wide text-slate-500">Jumlah Tagihan</p>
                <p class="mt-1 text-3xl font-bold text-indigo-600">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</p>
            </div>
        </x-ui.card>

        {{-- Ringkasan Pembayaran --}}
        <x-ui.card title="Pembayaran">
            @if ($invoice->payments->isEmpty())
                <div class="py-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 mb-3">
                        <x-icon name="credit-card" :size="24" class="text-slate-400" />
                    </div>
                    <p class="text-sm text-slate-500">Belum ada pembayaran</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($invoice->payments as $pay)
                        <div class="rounded-lg border border-slate-200 p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-slate-500">{{ $pay->payment_date->format('d M Y') }}</span>
                                <x-ui.badge :status="$pay->status" />
                            </div>
                            <p class="text-lg font-bold text-slate-800">Rp {{ number_format($pay->amount, 0, ',', '.') }}</p>
                            @if ($pay->notes)
                                <p class="mt-1 text-xs text-slate-500">{{ $pay->notes }}</p>
                            @endif
                            @if ($pay->proof)
                                <a href="{{ asset('storage/' . $pay->proof) }}" target="_blank"
                                   class="mt-2 inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline">
                                    <x-icon name="eye" :size="12" />
                                    Lihat Bukti
                                </a>
                            @endif
                            @if ($pay->verifier)
                                <p class="mt-2 text-xs text-slate-500">
                                    Diverifikasi oleh {{ $pay->verifier->name }}
                                    @if ($pay->verified_at)
                                        · {{ $pay->verified_at->format('d M Y') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection