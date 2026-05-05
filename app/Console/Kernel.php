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

protected function schedule(Schedule $schedule): void
    {

$schedule->command(MarkExpiredMemberships::class)
            ->dailyAt('00:05')
            ->timezone('Africa/Maseru')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/mark-expired.log'));

$schedule->command(SuspendOverdueMembers::class)
            ->dailyAt('01:00')
            ->timezone('Africa/Maseru')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/suspend-overdue.log'));

$schedule->command(VoidAbandonedPayments::class)
            ->dailyAt('02:00')
            ->timezone('Africa/Maseru')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/void-abandoned.log'));

$schedule->command(SendMembershipExpiryReminders::class)
            ->weeklyOn(1, '08:00')
            ->timezone('Africa/Maseru')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/expiry-reminders.log'));
    }

protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
