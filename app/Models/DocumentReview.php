<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentReview extends Model
{
    // ── Document types ──────────────────────────────────────────────────
    const TYPE_AGM_NOTICE    = 'agm_notice';
    const TYPE_EC_MINUTES    = 'ec_minutes';
    const TYPE_CERTIFICATE   = 'certificate';
    const TYPE_RECEIPT       = 'receipt';
    const TYPE_WELCOME_PACK  = 'welcome_pack';

    // ── Status constants ──────────────────────────────────────────────────
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_APPROVED       = 'approved';
    const STATUS_SENT           = 'sent';
    const STATUS_CANCELLED      = 'cancelled';

    // ── Recipient types ──────────────────────────────────────────────────
    const RECIPIENT_ALL_PAID_UP = 'all_paid_up';
    const RECIPIENT_EC_MEMBERS  = 'ec_members';

    protected $fillable = [
        'type',
        'status',
        'recipient_type',
        'recipient_name',
        'data',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'reviewer_notes',
        'sent_by',
        'sent_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'data'           => 'array',
        'reviewed_at'    => 'datetime',
        'sent_at'        => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault(['name' => 'Unknown']);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by')->withDefault(['name' => 'Unknown']);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by')->withDefault(['name' => 'Unknown']);
    }

    // ── Status helpers ──────────────────────────────────────────────────────

    public function isPendingReview(): bool
    {
        return $this->status === self::STATUS_PENDING_REVIEW;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    // ── Type label for display ─────────────────────────────────────────────

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_AGM_NOTICE   => 'AGM Notice',
            self::TYPE_EC_MINUTES   => 'EC Minutes',
            self::TYPE_CERTIFICATE  => 'Membership Certificate',
            self::TYPE_RECEIPT      => 'Official Receipt',
            self::TYPE_WELCOME_PACK => 'Welcome Pack',
            default                 => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}

