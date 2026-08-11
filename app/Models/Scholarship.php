<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Scholarship extends Model
{
    protected $fillable = [
        'membership_category_id',
        'title',
        'description',
        'provider',
        'eligibility',
        'benefit',
        'application_deadline',
        'application_url',
        'contact_email',
        'is_published',
        'views',
        'expires_at',
    ];

    protected $casts = [
        'application_deadline' => 'date',
        'expires_at' => 'date',
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

    public function getIsActiveAttribute(): bool
    {
        return $this->is_published
            && (!$this->application_deadline || $this->application_deadline->isFuture());
    }
}
