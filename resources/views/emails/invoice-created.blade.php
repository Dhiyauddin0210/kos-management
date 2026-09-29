<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #333; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .header { background: #4f46e5; color: white; padding: 24px; text-align: center; }
        .content { padding: 24px; }
        .info { background: #f1f5f9; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e2e8f0; }
        .info-row:last-child { border-bottom: none; }
        .label { color: #64748b; }
        .value { font-weight: bold; color: #1e293b; }
        .amount { font-size: 24px; font-weight: bold; color: #4f46e5; text-align: center; margin: 20px 0; }
        .btn { display: inline-block; background: #4f46e5; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin: 16px 0; }
        .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0;">Tagihan Baru</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $invoice->tenant->full_name }}</strong>,</p>
            <p>Tagihan baru telah dibuat untuk kamar <strong>{{ $invoice->tenant->room->room_number }}</strong>.</p>

            <div class="info">
                <div class="info-row">
                    <span class="label">No. Invoice</span>
                    <span class="value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Periode</span>
                    <span class="value">{{ sprintf('%02d/%d', $invoice->month, $invoice->year) }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Jatuh Tempo</span>
                    <span class="value">{{ $invoice->due_date->format('d F Y') }}</span>
                </div>
            </div>

            <div class="amount">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</div>

            <p>Mohon segera lakukan pembayaran sebelum tanggal jatuh tempo.</p>
            <p style="text-align:center;">
                <a href="{{ url('/dashboard') }}" class="btn">Lihat Detail Tagihan</a>
            </p>
        </div>
        <div class="footer">
            Email ini dikirim otomatis oleh Sistem Kos Adin.
        </div>
    </div>
</body>
</html>