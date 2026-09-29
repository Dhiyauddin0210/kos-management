<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Extension extends Model
{
    protected $fillable = [
        'tenant_id',
        'current_end_date',
        'requested_end_date',
        'duration_months',
        'additional_cost',
        'status',
        'notes',
        'admin_notes',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'current_end_date' => 'date',
            'requested_end_date' => 'date',
            'duration_months' => 'integer',
            'additional_cost' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
