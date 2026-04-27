<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MembershipDocument extends Model
{
    // ── Status constants ──────────────────────────────────────────────────
    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';

    protected $fillable = ["membership_id", "document_type", "file_path", "original_name", "status"];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}
