<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class MeetingMinute extends Model
{
    protected $fillable = [
        'membership_category_id',
        'title',
        'description',
        'meeting_date',
        'location',
        'file_path',
        'is_published',
        'views',
        'expires_at',
    ];

    protected $casts = [
        'meeting_date' => 'date',
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
}
