<?php
namespace App\Console;

use App\Console\Commands\MarkExpiredMemberships;
use App\Console\Commands\SendMembershipExpiryReminders;
use App\Console\Commands\SuspendOverdueMembers;
use App\Console\Commands\VoidAbandonedPayments;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * Add this single cron entry to the server (runs every minute,
     * Laravel decides which commands to actually execute):
     *
     *   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
     */
    protected function schedule(Schedule $schedule): void
    {
        // Mark memberships as 'expired' once their expiry_date passes.
        // Runs just after midnight every day so it catches the 31 March
        // financial year-end as soon as it occurs.
        $schedule->command(MarkExpiredMemberships::class)
            ->dailyAt('00:05')
            ->timezone('Africa/Maseru')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/mark-expired.log'));

        // Suspend members who have not paid for 6+ months (Bylaws 1.3).
        // Runs at 01:00 to avoid conflicting with the expiry command.
        $schedule->command(SuspendOverdueMembers::class)
            ->dailyAt('01:00')
            ->timezone('Africa/Maseru')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/suspend-overdue.log'));

        // Void pending payment records that have no proof after 48 hours.
        // Runs at 02:00 — well after the membership commands.
        $schedule->command(VoidAbandonedPayments::class)
            ->dailyAt('02:00')
            ->timezone('Africa/Maseru')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/void-abandoned.log'));

        // Send 30-day renewal reminder emails every Monday at 08:00.
        $schedule->command(SendMembershipExpiryReminders::class)
            ->weeklyOn(1, '08:00')
            ->timezone('Africa/Maseru')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/expiry-reminders.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
