<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'property_id',
        'rating',
        'rating_cleanliness',
        'rating_security',
        'rating_facilities',
        'rating_price',
        'rating_friendliness',
        'comment',
        'is_anonymous',
        'status',
        'admin_notes',
        'approved_at',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'rating'              => 'integer',
            'rating_cleanliness'  => 'integer',
            'rating_security'     => 'integer',
            'rating_facilities'   => 'integer',
            'rating_price'        => 'integer',
            'rating_friendliness' => 'integer',
            'is_anonymous'        => 'boolean',
            'approved_at'         => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Penghuni Anonim';
        }

        $name = $this->user?->name ?? 'Penghuni';
        $parts = explode(' ', $name);

        if (count($parts) > 1) {
            return $parts[0] . ' ' . strtoupper(substr(end($parts), 0, 1)) . '.';
        }

        return $name;
    }

    public function getInitialAttribute(): string
    {
        return strtoupper(substr($this->display_name, 0, 1));
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public static function averageRating(): float
    {
        return round(static::approved()->avg('rating') ?? 0, 1);
    }

    public static function totalApproved(): int
    {
        return static::approved()->count();
    }
}