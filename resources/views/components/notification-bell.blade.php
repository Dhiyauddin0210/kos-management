@php
    use App\Models\Invoice;
    use App\Models\Lead;
    use App\Models\Maintenance;
    use App\Models\Payment;

    $pendingPayments = Payment::where('status', 'pending')->count();
    $overdueInvoices = Invoice::where('status', 'overdue')->count();
    $newLeads        = Lead::where('status', 'new')->count();
    $openMaints      = Maintenance::whereIn('status', ['reported', 'in_progress'])->count();

    $total = $pendingPayments + $overdueInvoices + $newLeads + $openMaints;

    $notifications = collect();

    if ($pendingPayments > 0) {
        $notifications->push([
            'icon'  => 'credit-card',
            'color' => 'amber',
            'title' => "{$pendingPayments} pembayaran menunggu verifikasi",
            'url'   => route('admin.payments.index', ['status' => 'pending']),
        ]);
    }
    if ($overdueInvoices > 0) {
        $notifications->push([
            'icon'  => 'alert-circle',
            'color' => 'rose',
            'title' => "{$overdueInvoices} tagihan terlambat",
            'url'   => route('admin.invoices.index', ['status' => 'overdue']),
        ]);
    }
    if ($newLeads > 0) {
        $notifications->push([
            'icon'  => 'inbox',
            'color' => 'blue',
            'title' => "{$newLeads} lead baru masuk",
            'url'   => route('admin.leads.index', ['status' => 'new']),
        ]);
    }
    if ($openMaints > 0) {
        $notifications->push([
            'icon'  => 'wrench',
            'color' => 'indigo',
            'title' => "{$openMaints} laporan maintenance belum selesai",
            'url'   => route('admin.maintenances.index'),
        ]);
    }

    $colors = [
        'amber'  => 'bg-amber-100 text-amber-600',
        'rose'   => 'bg-rose-100 text-rose-600',
        'blue'   => 'bg-blue-100 text-blue-600',
        'indigo' => 'bg-indigo-100 text-indigo-600',
    ];
@endphp

<div class="relative" x-data="{ open: false }">
    {{-- Trigger Button --}}
    <button type="button" @click="open = !open"
            class="relative rounded-lg p-2 text-slate-600 hover:bg-slate-100 transition">
        <x-icon name="bell" :size="20" />
        @if ($total > 0)
            <span class="absolute top-1 right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">
                {{ $total > 9 ? '9+' : $total }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-cloak @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="absolute right-0 mt-2 w-80 origin-top-right rounded-xl border border-slate-200 bg-white shadow-lg z-50">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <h3 class="font-semibold text-slate-800">Notifikasi</h3>
            @if ($total > 0)
                <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-600">{{ $total }}</span>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse ($notifications as $notif)
                <a href="{{ $notif['url'] }}"
                   class="flex items-start gap-3 border-b border-slate-50 px-4 py-3 hover:bg-slate-50 transition">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $colors[$notif['color']] ?? 'bg-slate-100' }}">
                        <x-icon :name="$notif['icon']" :size="16" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-700 leading-snug">{{ $notif['title'] }}</p>
                    </div>
                    <x-icon name="chevron-right" :size="16" class="mt-1.5 shrink-0 text-slate-400" />
                </a>
            @empty
                <div class="px-4 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                        <x-icon name="bell" :size="22" class="text-slate-400" />
                    </div>
                    <p class="text-sm text-slate-500">Tidak ada notifikasi</p>
                    <p class="mt-1 text-xs text-slate-400">Semua udah beres! 🎉</p>
                </div>
            @endforelse
        </div>
    </div>
</div>