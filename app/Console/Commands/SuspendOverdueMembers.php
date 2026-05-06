<?php
namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Membership;
use App\Notifications\SuspensionNotification;
use App\Services\PaymentService;
use Illuminate\Console\Command;

class SuspendOverdueMembers extends Command
{
    protected $signature   = 'membership:suspend-overdue';
    protected $description = 'Suspend members who have not renewed for 6+ months (Bylaws 1.3).';

public function handle(): int
    {
        $overdueMemberships = Membership::where('status', 'approved')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->subMonths(6))
            ->with('user')
            ->get();

        if ($overdueMemberships->isEmpty()) {
            $this->info('No members to suspend.');
            return 0;
        }

        $overdueIds = $overdueMemberships->pluck('id');

        $count = Membership::whereIn('id', $overdueIds)->update([
            'status'       => 'suspended',
            'suspended_at' => now(),
        ]);

        AuditLog::create([
            'user_id'        => null,
            'action'         => 'membership.bulk_auto_suspended',
            'auditable_type' => Membership::class,
            'auditable_id'   => null,
            'old_values'     => ['status' => 'approved'],
            'new_values'     => ['status' => 'suspended'],
            'meta'           => [
                'reason'        => 'Non-payment for 6+ months (Bylaws 1.3)',
                'suspended_ids' => $overdueIds->toArray(),
                'count'         => $count,
                'command'       => 'membership:suspend-overdue',
            ],
        ]);

        foreach ($overdueMemberships as $membership) {
            if ($membership->user) {
                $membership->user->notify(new SuspensionNotification($membership));
                $memberId = $membership->member_id ?: $membership->id;
                $this->line("Notified and suspended: {$membership->user->name} ({$memberId})");
            }
        }

        $this->info("Suspended and notified {$count} member(s).");
        return 0;
    }
}