<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    // ── Status constants ──────────────────────────────────────────────────
    const STATUS_PENDING  = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';
    const STATUS_VOIDED   = 'voided'; // abandoned references, no proof uploaded

    protected $fillable = [
        'membership_id',
        'amount',
        'provider',
        'purpose',
        'transaction_reference',
        'proof_file',
        'status',
        'verification_notes',
        'verified_at',
        'receipt_number',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'amount'      => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class)->withDefault();
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
