<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    protected $fillable = [
        'property_id',
        'room_number',
        'type',
        'price',
        'size',
        'facilities',
        'status',
        'description',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            // Kolom TEXT berisi JSON string -> otomatis jadi array di PHP
            'facilities' => 'array',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /** Semua penghuni yang pernah/sedang menempati kamar ini. */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /** Penghuni yang saat ini aktif (status = active). */
    public function activeTenant(): HasOne
    {
        return $this->hasOne(Tenant::class)->where('status', 'active');
    }

    /** Semua tagihan kamar ini, lewat tabel tenants. */
    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(Invoice::class, Tenant::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
}
