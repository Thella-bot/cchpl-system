<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Send renewal reminder emails to members expiring within 30 days.
 *
 * This command delegates to CheckMembershipRenewals which covers
 * 30, 14, 7, and 1 day reminder windows.
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
        $this->info('Delegating to memberships:check-renewals...');
        return $this->call('memberships:check-renewals');
    }
}
