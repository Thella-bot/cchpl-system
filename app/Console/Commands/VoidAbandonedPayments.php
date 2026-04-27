<?php
namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Payment;
use Illuminate\Console\Command;

/**
 * Void pending payment records that have no proof uploaded after 48 hours.
 *
 * When a member generates a reference at step 1 of the payment flow,
 * a Payment record is immediately created with status='pending'.
 * If they close the browser before uploading proof, that record sits
 * forever and prevents the system from working cleanly.
 *
 * This command marks those records as 'voided' so the member can
 * start a fresh payment on their next visit.
 *
 * Schedule: daily at 02:00.
 * Run manually: php artisan payments:void-abandoned
 */
class VoidAbandonedPayments extends Command
{
    protected $signature   = 'payments:void-abandoned';
    protected $description = 'Void pending payments with no proof uploaded after 48 hours.';

    public function handle(): int
    {
        $abandoned = Payment::where('status', 'pending')
            ->whereNull('proof_file')
            ->where('created_at', '<', now()->subHours(48))
            ->get();

        if ($abandoned->isEmpty()) {
            $this->info('No abandoned payments to void.');
            return 0;
        }

        foreach ($abandoned as $payment) {
            $payment->update([
                'status'             => 'voided',
                'verification_notes' => 'Automatically voided — no proof uploaded within 48 hours.',
            ]);

            AuditLog::create([
                'user_id'        => null,
                'action'         => 'payment.auto_voided',
                'auditable_type' => Payment::class,
                'auditable_id'   => $payment->id,
                'old_values'     => ['status' => 'pending'],
                'new_values'     => ['status' => 'voided'],
                'meta'           => [
                    'reference' => $payment->transaction_reference,
                    'created'   => $payment->created_at->toIso8601String(),
                    'command'   => 'payments:void-abandoned',
                ],
            ]);

            $this->line("Voided: {$payment->transaction_reference}");
        }

        $this->info("Voided {$abandoned->count()} abandoned payment(s).");
        return 0;
    }
}
