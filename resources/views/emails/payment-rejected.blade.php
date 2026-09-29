<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #333; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .header { background: #ef4444; color: white; padding: 24px; text-align: center; }
        .content { padding: 24px; }
        .info { background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; }
        .label { color: #64748b; }
        .value { font-weight: bold; color: #1e293b; }
        .notes { background: #fef2f2; border-radius: 8px; padding: 16px; margin: 16px 0; color: #991b1b; }
        .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0;">❌ Pembayaran Ditolak</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $payment->invoice->tenant->full_name }}</strong>,</p>
            <p>Mohon maaf, pembayaran Anda <strong style="color:#ef4444;">ditolak</strong> oleh admin.</p>

            <div class="info">
                <div class="info-row">
                    <span class="label">No. Invoice</span>
                    <span class="value">{{ $payment->invoice->invoice_number }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Jumlah</span>
                    <span class="value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="notes">
                <strong>Alasan Penolakan:</strong><br>
                {{ $payment->notes ?: 'Tidak ada catatan dari admin.' }}
            </div>

            <p>Silakan periksa kembali bukti transfer Anda atau hubungi admin untuk informasi lebih lanjut.</p>
        </div>
        <div class="footer">
            Email ini dikirim otomatis oleh Sistem Kos Adin.
        </div>
    </div>
</body>
</html>