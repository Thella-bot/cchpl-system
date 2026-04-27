<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resignation extends Model
{
    const STATUS_PENDING      = 'pending';
    const STATUS_ACKNOWLEDGED = 'acknowledged';
    const STATUS_CANCELLED    = 'cancelled';

    const REASON_CODES = [
        'career_change'       => 'Career change / relocation',
        'financial'           => 'Financial constraints',
        'insufficient_value'  => 'Not receiving sufficient value',
        'personal'            => 'Personal reasons',
        'dissatisfied'        => 'Dissatisfied with CCHPL',
        'other'               => 'Other',
    ];

    protected $fillable = [
        'user_id',
        'membership_id',
        'status',
        'effective_date',
        'reason_code',
        'reason_notes',
        'balance_outstanding',
        'acknowledged_by',
        'acknowledged_at',
        'acknowledgement_notes',
    ];

    protected $casts = [
        'effective_date'   => 'date',
        'acknowledged_at'  => 'datetime',
        'balance_outstanding' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function reasonLabel(): string
    {
        return self::REASON_CODES[$this->reason_code] ?? ucfirst(str_replace('_', ' ', $this->reason_code ?? 'Not specified'));
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
