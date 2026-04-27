<?php

namespace App\Console\Commands;

use App\Models\Membership;
use App\Notifications\MembershipExpiredNotification;
use App\Notifications\MembershipRenewalReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckMembershipRenewals extends Command
{
    protected $signature = 'memberships:check-renewals';
    protected $description = 'Check for expiring and expired memberships and send notifications.';

    public function handle()
    {
        $this->info('Checking for expiring and expired memberships...');

        $this->handleExpiringMemberships();
        $this->handleExpiredMemberships();

        $this->info('Done.');
        return 0;
    }

    /**
     * Find members whose memberships are expiring soon and send reminders.
     */
    private function handleExpiringMemberships()
    {
        $reminderDates = [30, 14, 7, 1]; // Send reminders 30, 14, 7, and 1 day(s) before expiry.

        foreach ($reminderDates as $days) {
            $targetDate = now()->addDays($days)->toDateString();
            $this->line("Checking for memberships expiring in {$days} day(s) on {$targetDate}...");

            $expiringMemberships = Membership::where('status', 'approved')
                ->whereDate('expiry_date', $targetDate)
                ->with(['user', 'category'])
                ->get();

            if ($expiringMemberships->isEmpty()) {
                continue;
            }

            $this->line("Found " . $expiringMemberships->count() . " membership(s) expiring in {$days} day(s). Sending reminders...");

            foreach ($expiringMemberships as $membership) {
                if ($membership->user) {
                    $membership->user->notify(new MembershipRenewalReminderNotification($membership, $days));
                    Log::info("Sent renewal reminder to user_id {$membership->user->id} for membership #{$membership->id}.");
                }
            }
        }
    }

    /**
     * Find memberships that have expired, update their status, and notify the user.
     */
    private function handleExpiredMemberships()
    {
        $this->line('Checking for memberships that have expired...');
        $expiredMemberships = Membership::where('status', 'approved')
            ->where('expiry_date', '<', now()->startOfDay())
            ->with(['user', 'category'])
            ->get();

        if ($expiredMemberships->isNotEmpty()) {
            $this->line("Found " . $expiredMemberships->count() . " expired membership(s). Updating status and notifying users...");
            foreach ($expiredMemberships as $membership) {
                $membership->update(['status' => 'expired']);
                $membership->user?->notify(new MembershipExpiredNotification($membership));
                Log::info("Membership #{$membership->id} for user_id {$membership->user?->id} has been marked as expired.");
            }
        }
    }
}