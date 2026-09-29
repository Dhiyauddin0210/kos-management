<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'month',
        'year',
        'amount',
        'due_date',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Generate nomor invoice berformat INV-YYYYMM-XXXX.
     * XXXX = nomor urut 4 digit yang di-reset setiap bulan-tahun periode tagihan.
     * Contoh: INV-202609-0001
     */
    public static function generateNumber(int $year, int $month): string
    {
        $prefix = sprintf('INV-%04d%02d-', $year, $month);

        $last = static::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** Pembayaran yang sudah diverifikasi admin. */
    public function verifiedPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->where('status', 'verified');
    }
}
