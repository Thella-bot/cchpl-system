<?php
namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Membership;
use Illuminate\Console\Command;

/**
 * Write 'expired' status to memberships whose expiry_date has passed.
 *
 * This keeps the DB status column accurate so queries like
 * Membership::where('status', 'expired') work correctly for reports.
 *
 * Schedule: daily at 00:05 (runs just after midnight so it catches
 * memberships that expired at end of 31 March financial year-end).
 *
 * Run manually: php artisan membership:mark-expired
 */
class MarkExpiredMemberships extends Command
{
    protected $signature   = 'membership:mark-expired';
    protected $description = 'Mark approved memberships whose expiry date has passed as expired.';

    public function handle(): int
    {
        // Only touch memberships that are still marked 'approved' but
        // have an expiry_date in the past.  Do NOT touch 'suspended' —
        // those have their own status and the penalty logic depends on it.
        $expired = Membership::where('status', 'approved')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->startOfDay())
            ->with('user', 'category')
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No memberships to mark as expired.');
            return 0;
        }

        foreach ($expired as $membership) {
            $oldStatus = $membership->status;
            $membership->update(['status' => 'expired']);

            AuditLog::create([
                'user_id'        => null, // system action
                'action'         => 'membership.auto_expired',
                'auditable_type' => Membership::class,
                'auditable_id'   => $membership->id,
                'old_values'     => ['status' => $oldStatus],
                'new_values'     => ['status' => 'expired'],
                'meta'           => [
                    'expired_date' => $membership->expiry_date->toDateString(),
                    'command'      => 'membership:mark-expired',
                ],
            ]);

            $memberId = $membership->member_id ? $membership->member_id : $membership->id;
            $this->line("Expired: {$membership->user->name} ({$memberId})");
        }

        $this->info("Marked {$expired->count()} membership(s) as expired.");
        return 0;
    }
}