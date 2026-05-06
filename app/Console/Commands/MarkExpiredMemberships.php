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
        $expiredIds = Membership::where('status', 'approved')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->startOfDay())
            ->pluck('id');

        if ($expiredIds->isEmpty()) {
            $this->info('No memberships to mark as expired.');
            return 0;
        }

        $count = Membership::whereIn('id', $expiredIds)->update(['status' => 'expired']);

        AuditLog::create([
            'user_id'        => null, 
            'action'         => 'membership.bulk_auto_expired',
            'auditable_type' => Membership::class,
            'auditable_id'   => null,
            'old_values'     => ['status' => 'approved'],
            'new_values'     => ['status' => 'expired'],
            'meta'           => [
                'expired_ids' => $expiredIds->toArray(),
                'count'       => $count,
                'command'     => 'membership:mark-expired',
            ],
        ]);

        $this->info("Marked {$count} membership(s) as expired.");
        return 0;
    }
}