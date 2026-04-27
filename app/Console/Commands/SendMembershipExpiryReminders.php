<?php
namespace App\Console\Commands;

use App\Models\Membership;
use App\Notifications\MembershipExpiryReminder;
use Illuminate\Console\Command;

/**
 * Send renewal reminder emails to members expiring within 30 days.
 *
 * Schedule: weekly on Mondays.
 * Run manually: php artisan membership:send-expiry-reminders
 */
class SendMembershipExpiryReminders extends Command
{
    protected $signature   = 'membership:send-expiry-reminders';
    protected $description = 'Send renewal reminder emails to members expiring within 30 days.';

    public function handle(): int
    {
        $memberships = Membership::where('status', 'approved')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->whereDate('expiry_date', '>=', now())
            ->with('user')
            ->get();

        if ($memberships->isEmpty()) {
            $this->info('No expiry reminders to send.');
            return 0;
        }

        foreach ($memberships as $membership) {
            if ($membership->user) {
                $membership->user->notify(new MembershipExpiryReminder($membership));
                $this->line("Reminded: {$membership->user->name} — expires {$membership->expiry_date->format('d M Y')}");
            }
        }

        $this->info("Sent {$memberships->count()} expiry reminder(s).");
        return 0;
    }
}
