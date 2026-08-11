<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Event extends Model
{
    protected $fillable = [
        'membership_category_id',
        'title',
        'description',
        'event_date',
        'location',
        'venue',
        'image_path',
        'capacity',
        'spots_taken',
        'registration_deadline',
        'price',
        'currency',
        'is_published',
        'views',
        'expires_at',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'expires_at' => 'date',
        'registration_deadline' => 'date',
        'price' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MembershipCategory::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function getSpotsRemainingAttribute(): ?int
    {
        if (! $this->capacity) {
            return null;
        }

        return max(0, $this->capacity - $this->spots_taken);
    }

    public function getIsSoldOutAttribute(): bool
    {
        return $this->capacity !== null && $this->spots_taken >= $this->capacity;
    }
}
