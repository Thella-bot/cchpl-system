<?php
namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Membership;
use Illuminate\Console\Command;

class MarkExpiredMemberships extends Command
{
    protected $signature   = 'membership:mark-expired';
    protected $description = 'Mark approved memberships whose expiry date has passed as expired.';

public function handle(): int
    {

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
                'user_id'        => null, 
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