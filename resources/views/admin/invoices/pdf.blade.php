<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tagihan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitle { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-weight: bold; }
        .text-right { text-align: right; }
        .footer { margin-top: 20px; text-align: right; font-weight: bold; font-size: 12px; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-unpaid { background: #f1f5f9; color: #475569; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <h1>Laporan Tagihan - Kos Adin</h1>
    <p class="subtitle">Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Penghuni</th>
                <th>Kamar</th>
                <th>Periode</th>
                <th class="text-right">Jumlah</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $inv)
                <tr>
                    <td>{{ $inv->invoice_number }}</td>
                    <td>{{ $inv->tenant?->full_name }}</td>
                    <td>{{ $inv->tenant?->room?->room_number }}</td>
                    <td>{{ sprintf('%02d/%d', $inv->month, $inv->year) }}</td>
                    <td class="text-right">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                    <td>{{ $inv->due_date->format('d/m/Y') }}</td>
                    <td><span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:#999;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total: Rp {{ number_format($total, 0, ',', '.') }}
    </div>
</body>
</html>