<?php

namespace App\Console\Commands;

use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SystemHealthCheckCommand extends Command
{
    protected $signature = 'system:health-check';

    protected $description = 'Run system health checks and alert on issues.';

    public function handle(): int
    {
        $this->info('Running CCHPL System Health Check...');
        $warnings = [];
        $errors = [];

        // Check database connection
        try {
            \DB::connection()->getPdo();
            $this->line('✅ Database connection: OK');
        } catch (\Exception $e) {
            $errors[] = 'Database connection failed: '.$e->getMessage();
        }

        // Check disk space
        $diskFree = disk_free_space(storage_path());
        $diskTotal = disk_total_space(storage_path());
        $diskUsagePercent = (1 - ($diskFree / $diskTotal)) * 100;

        if ($diskUsagePercent > 90) {
            $errors[] = "Critical disk usage: {$diskUsagePercent}%";
        } elseif ($diskUsagePercent > 75) {
            $warnings[] = "High disk usage: {$diskUsagePercent}%";
        } else {
            $this->line('✅ Disk space: '.number_format($diskUsagePercent, 1).'% used');
        }

        // Pending items check
        $pendingPayments = Payment::where('status', 'pending')->count();
        if ($pendingPayments > 20) {
            $warnings[] = "High number of pending payments: {$pendingPayments}";
        } else {
            $this->line("✅ Pending payments: {$pendingPayments}");
        }

        $pendingApplications = Membership::where('status', 'pending')->count();
        $this->line("✅ Pending membership applications: {$pendingApplications}");

        $todayLogins = User::whereDate('last_login_at', today())->count();
        $this->line("✅ Today's active users: {$todayLogins}");

        // Storage directory permissions
        if (! is_writable(storage_path('app/public'))) {
            $errors[] = 'Public storage directory not writable';
        } else {
            $this->line('✅ Storage permissions: OK');
        }

        // Report results
        if (! empty($errors)) {
            $this->error('❌ Errors found:');
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
            Log::error('System health check failed', ['errors' => $errors, 'warnings' => $warnings]);

            return 1;
        }

        if (! empty($warnings)) {
            $this->warn('⚠️ Warnings found:');
            foreach ($warnings as $warning) {
                $this->warn("  - {$warning}");
            }
            Log::warning('System health check warnings', ['warnings' => $warnings]);

            return 0;
        }

        $this->info('🎉 All systems healthy!');
        Log::info('System health check passed');

        return 0;
    }
}
