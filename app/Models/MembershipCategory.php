<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipCategory extends Model
{
    protected $fillable = [
        'name',
        'annual_fee',
        'voting_rights',
        'eligibility_criteria',
        'other_notes',
    ];

    protected $casts = [
        'annual_fee' => 'decimal:2',
        'voting_rights' => 'boolean',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'category_id');
    }
}
