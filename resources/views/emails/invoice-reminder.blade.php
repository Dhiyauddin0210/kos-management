<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #333; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .header { background: #f59e0b; color: white; padding: 24px; text-align: center; }
        .content { padding: 24px; }
        .info { background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; }
        .label { color: #64748b; }
        .value { font-weight: bold; color: #1e293b; }
        .amount { font-size: 28px; font-weight: bold; color: #f59e0b; text-align: center; margin: 20px 0; }
        .btn { display: inline-block; background: #f59e0b; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin: 16px 0; }
        .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0;">⏰ Reminder Tagihan</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $invoice->tenant->full_name }}</strong>,</p>
            <p>Ini reminder bahwa tagihan Anda <strong style="color:#f59e0b;">belum dibayar</strong>.</p>

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

            <p style="text-align:center;">
                <a href="{{ url('/dashboard') }}" class="btn">Bayar Sekarang</a>
            </p>

            <p style="font-size:13px;color:#64748b;">Jika sudah membayar, abaikan email ini.</p>
        </div>
        <div class="footer">
            Email ini dikirim otomatis oleh Sistem Kos Adin.
        </div>
    </div>
</body>
</html>