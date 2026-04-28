<?php
namespace App\Models;

use App\Services\MembershipService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    // ── Status constants ──────────────────────────────────────────────────
    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_EXPIRED   = 'expired';
    const STATUS_RESIGNED  = 'resigned';

    protected $fillable = [
        'user_id',
        'category_id',
        'status',
        'member_id',
        'expiry_date',
        'suspended_at',
        'rejection_reason',
    ];

    protected $casts = [
        'expiry_date'  => 'datetime',
        'suspended_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Unknown User',
            'email' => 'N/A',
        ]);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MembershipCategory::class)->withDefault([
            'name' => 'Uncategorized',
            'annual_fee' => 0,
        ]);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MembershipDocument::class);
    }

    // ── Status helpers ─────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === self::STATUS_APPROVED
            && $this->expiry_date
            && $this->expiry_date->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date
            && $this->expiry_date->isFuture()
            && $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function daysUntilExpiry(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        return (int) now()->diffInDays($this->expiry_date, false);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED  => 'bg-success',
            self::STATUS_PENDING   => 'bg-warning text-dark',
            self::STATUS_REJECTED  => 'bg-danger',
            self::STATUS_SUSPENDED => 'bg-secondary',
            self::STATUS_EXPIRED   => 'bg-dark',
            self::STATUS_RESIGNED  => 'bg-info text-dark',
            default                => 'bg-light text-dark',
        };
    }

    /**
     * Generate and persist the CCHPL member ID.
     * Called once when an application is approved.
     */
    public function generateMemberId(): string
    {
        return (new MembershipService())->generateMemberId($this);
    }

    /**
     * Whether the 10% late payment penalty applies (Bylaws 1.3).
     * Fees are due 31 March; penalty applies after that date.
     */
    public function isPenaltyApplicable(): bool
    {
        return (new MembershipService())->isPenaltyApplicable($this);
    }
}
