@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    {{-- Welcome Section --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Selamat datang, {{ Auth::user()->name }} 👋</h1>
            <p class="text-sm text-slate-500">{{ now()->translatedFormat('l, d F Y') }} · Ringkasan operasional Kos Adin</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-white border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition">
                <x-icon name="plus" :size="16" />
                Tambah Kamar
            </a>
            <a href="{{ route('admin.tenants.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
                <x-icon name="plus" :size="16" />
                Tambah Penghuni
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card
            label="Total Kamar"
            :value="$totalRooms"
            icon="building-2"
            color="indigo" />
        <x-ui.stat-card
            label="Kamar Terisi"
            :value="$occupiedRooms"
            icon="bed-double"
            color="emerald" />
        <x-ui.stat-card
            label="Kamar Kosong"
            :value="$availableRooms"
            icon="door-open"
            color="amber" />
        <x-ui.stat-card
            label="Pendapatan Bulan Ini"
            :value="'Rp ' . number_format($revenueThisMonth, 0, ',', '.')"
            icon="wallet"
            color="violet" />
    </div>

    {{-- SPARKLINE MINI: 6 Bulan Terakhir --}}
    <div class="mt-6">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Tren Pendapatan · 6 Bulan Terakhir</h2>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($chartLabels as $i => $label)
                @php
                    $value = $chartData[$i] ?? 0;
                    $prev = $i > 0 ? ($chartData[$i - 1] ?? 0) : $value;
                    $diff = $prev > 0 ? (($value - $prev) / $prev) * 100 : 0;
                    $isUp = $diff >= 0;
                @endphp
                <div class="group rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $label }}</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">Rp {{ number_format($value / 1000, 0, ',', '.') }}k</p>
                    <div class="mt-1 flex items-center gap-1">
                        @if ($isUp)
                            <x-icon name="trending-up" :size="12" class="text-emerald-500" />
                            <span class="text-[10px] font-semibold text-emerald-600">+{{ number_format(abs($diff), 1) }}%</span>
                        @else
                            <x-icon name="trending-down" :size="12" class="text-rose-500" />
                            <span class="text-[10px] font-semibold text-rose-600">-{{ number_format(abs($diff), 1) }}%</span>
                        @endif
                    </div>
                    <div class="mt-2 h-8">
                        <canvas id="spark{{ $i }}" class="w-full h-full"></canvas>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Chart Utama: Mixed Bar + Line --}}
    <x-ui.card title="Pendapatan Bulanan" class="mt-6">
        <div class="h-72"><canvas id="mainChart"></canvas></div>
    </x-ui.card>

    {{-- Grid: Top Kamar + Ringkasan --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        {{-- Top 5 Kamar --}}
        <x-ui.card title="Top 5 Kamar Terlaris" class="lg:col-span-1">
            <div class="space-y-4">
                @forelse ($topRooms as $room)
                    @php
                        $maxRevenue = ($topRooms[0]->total_revenue ?? 1) ?: 1;
                        $percent = ($room->total_revenue / $maxRevenue) * 100;
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700">Kamar {{ $room->room_number }}</span>
                            <span class="text-xs font-semibold text-slate-600">Rp {{ number_format($room->total_revenue, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 transition-all" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-slate-400">Belum ada data.</p>
                @endforelse
            </div>
        </x-ui.card>

        {{-- Ringkasan Cepat --}}
        <x-ui.card title="Ringkasan Cepat" class="lg:col-span-2">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-lg bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">Tagihan Pending</p>
                    <p class="mt-1 text-2xl font-bold text-amber-600">{{ $pendingPayments->count() }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">Leads Baru</p>
                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $latestLeads->where('status', 'new')->count() }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">Okupansi</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600">
                        {{ $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0 }}%
                    </p>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">Rata-rata/bln</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        Rp {{ number_format(array_sum($chartData) / max(count(array_filter($chartData)), 1), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Tabel: Tagihan Terbaru + Pembayaran Pending --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-ui.card title="Tagihan Terbaru" flush>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2.5">No. Invoice</th>
                            <th class="px-4 py-2.5">Penghuni</th>
                            <th class="px-4 py-2.5">Jumlah</th>
                            <th class="px-4 py-2.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestInvoices as $inv)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-2.5 font-mono text-xs">{{ $inv->invoice_number }}</td>
                                <td class="px-4 py-2.5">
                                    {{ $inv->tenant?->full_name }}
                                    <span class="text-xs text-slate-400">({{ $inv->tenant?->room?->room_number }})</span>
                                </td>
                                <td class="px-4 py-2.5 font-medium">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5"><x-ui.badge :status="$inv->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Belum ada tagihan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card title="Pembayaran Menunggu Verifikasi" flush>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2.5">Penghuni</th>
                            <th class="px-4 py-2.5">Invoice</th>
                            <th class="px-4 py-2.5">Jumlah</th>
                            <th class="px-4 py-2.5">Tgl Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pendingPayments as $pay)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-2.5">{{ $pay->invoice?->tenant?->full_name }}</td>
                                <td class="px-4 py-2.5 font-mono text-xs">{{ $pay->invoice?->invoice_number }}</td>
                                <td class="px-4 py-2.5 font-medium">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5">{{ $pay->payment_date->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Tidak ada pembayaran pending.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>

    {{-- Leads Terbaru --}}
    <x-ui.card title="Leads Terbaru" class="mt-6" flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5">Nama</th>
                        <th class="px-4 py-2.5">No. HP</th>
                        <th class="px-4 py-2.5">Kamar</th>
                        <th class="px-4 py-2.5">Sumber</th>
                        <th class="px-4 py-2.5">Status</th>
                        <th class="px-4 py-2.5">Masuk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($latestLeads as $lead)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-2.5 font-medium text-slate-800">{{ $lead->name }}</td>
                            <td class="px-4 py-2.5">{{ $lead->phone }}</td>
                            <td class="px-4 py-2.5">{{ $lead->room?->room_number ?? '-' }}</td>
                            <td class="px-4 py-2.5 uppercase text-xs text-slate-500">{{ $lead->source }}</td>
                            <td class="px-4 py-2.5"><x-ui.badge :status="$lead->status" /></td>
                            <td class="px-4 py-2.5 text-slate-500">{{ $lead->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada leads.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        // ================= SPARKLINES (6 mini chart) =================
        const sparkData = @json($chartData);
        const sparkLabels = @json($chartLabels);

        sparkData.forEach((value, i) => {
            const ctx = document.getElementById('spark' + i);
            if (!ctx) return;

            const prev = i > 0 ? sparkData[i - 1] : value;
            const isUp = value >= prev;
            const color = isUp ? '#10b981' : '#f43f5e';

            const miniData = Array.from({ length: 7 }, (_, k) => {
                const factor = 1 + (Math.sin(k + i) * 0.08);
                return value * factor;
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: miniData.map((_, k) => k),
                    datasets: [{
                        data: miniData,
                        borderColor: color,
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: {
                        x: { display: false },
                        y: { display: false }
                    }
                }
            });
        });

        // ================= CHART UTAMA (Mixed Bar + Line) =================
        const mainCtx = document.getElementById('mainChart');
        const gradient = mainCtx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.9)');
        gradient.addColorStop(1, 'rgba(139, 92, 246, 0.4)');

        const avg = sparkData.reduce((a, b) => a + b, 0) / sparkData.length;
        const avgLine = sparkData.map(() => avg);

        new Chart(mainCtx, {
            type: 'bar',
            data: {
                labels: sparkLabels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Pendapatan',
                        data: sparkData,
                        backgroundColor: gradient,
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 32,
                    },
                    {
                        type: 'line',
                        label: 'Rata-rata',
                        data: avgLine,
                        borderColor: '#f59e0b',
                        borderWidth: 2,
                        borderDash: [6, 4],
                        pointRadius: 0,
                        fill: false,
                        tension: 0,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: { boxWidth: 12, boxHeight: 12, font: { size: 12 }, usePointStyle: true, pointStyle: 'circle' }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (ctx) => ' ' + ctx.dataset.label + ': Rp ' + Number(ctx.parsed.y).toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 12 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { size: 12 },
                            callback: (v) => 'Rp ' + (v / 1000) + 'k'
                        }
                    }
                }
            }
        });
    </script>
@endpush