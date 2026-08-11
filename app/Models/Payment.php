<?php

namespace App\Models;

use App\Presenters\StatusPresenter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_VOIDED = 'voided';

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

        // Provider transaction id(s)
        'transaction_id',

        // M-Pesa identifiers (from STK callback)
        'mpesa_checkout_request_id',
        'mpesa_merchant_request_id',
        'mpesa_receipt_number',

        // NOTE: keep transaction_reference unique; never overwrite on retries.
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class)->withDefault();
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function statusBadgeClass(): string
    {
        return StatusPresenter::paymentStatusBadge($this->status);
    }
}
