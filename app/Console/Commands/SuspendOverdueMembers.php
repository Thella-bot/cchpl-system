<?php
namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Membership;
use App\Notifications\SuspensionNotification;
use App\Services\PaymentService;
use Illuminate\Console\Command;

/**
 * Suspend members who have not paid for 6+ months (Bylaws 1.3).
 *
 * Schedule: daily at midnight.
 * Run manually: php artisan membership:suspend-overdue
 */
class SuspendOverdueMembers extends Command
{
    protected $signature   = 'membership:suspend-overdue';
    protected $description = 'Suspend members who have not renewed for 6+ months (Bylaws 1.3).';

    public function handle(): int
    {
        $overdue = Membership::where('status', 'approved')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->subMonths(6))
            ->with('user', 'category')
            ->get();

        if ($overdue->isEmpty()) {
            $this->info('No members to suspend.');
            return 0;
        }

        foreach ($overdue as $membership) {
            $oldValues = $membership->only(['status', 'suspended_at']);

            $membership->update([
                'status'       => 'suspended',
                'suspended_at' => now(),
            ]);

            AuditLog::create([
                'user_id'        => null, // system action
                'action'         => 'membership.auto_suspended',
                'auditable_type' => Membership::class,
                'auditable_id'   => $membership->id,
                'old_values'     => $oldValues,
                'new_values'     => $membership->only(['status', 'suspended_at']),
                'meta'           => [
                    'reason'       => 'Non-payment for 6+ months (Bylaws 1.3)',
                    'expired_date' => $membership->expiry_date->toDateString(),
                    'command'      => 'membership:suspend-overdue',
                ],
            ]);

            if ($membership->user) {
                $membership->user->notify(new SuspensionNotification($membership));
            }

            $memberId = $membership->member_id ? $membership->member_id : $membership->id;
            $this->line("Suspended: {$membership->user->name} ({$memberId})");
        }

        $this->info("Suspended {$overdue->count()} member(s).");
        return 0;
    }
}