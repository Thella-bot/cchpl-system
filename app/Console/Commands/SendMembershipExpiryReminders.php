<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendMembershipExpiryReminders extends Command
{
    protected $signature = 'membership:send-expiry-reminders';

    protected $description = 'Send renewal reminder emails to members expiring within 30 days.';

    public function handle(): int
    {
        $this->info('Delegating to memberships:check-renewals...');

        return $this->call('memberships:check-renewals');
    }
}
