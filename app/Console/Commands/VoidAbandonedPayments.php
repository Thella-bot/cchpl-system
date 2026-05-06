<?php
namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Payment;
use Illuminate\Console\Command;

class VoidAbandonedPayments extends Command
{
    protected $signature   = 'payments:void-abandoned';
    protected $description = 'Void pending payments with no proof uploaded after 48 hours.';

public function handle(): int
    {
        $abandonedIds = Payment::where('status', 'pending')
            ->whereNull('proof_file')
            ->where('created_at', '<', now()->subHours(48))
            ->pluck('id');

        if ($abandonedIds->isEmpty()) {
            $this->info('No abandoned payments to void.');
            return 0;
        }

        $count = Payment::whereIn('id', $abandonedIds)->update([
            'status'             => 'voided',
            'verification_notes' => 'Automatically voided — no proof uploaded within 48 hours.',
        ]);

        AuditLog::create([
            'user_id'        => null,
            'action'         => 'payment.bulk_auto_voided',
            'auditable_type' => Payment::class,
            'auditable_id'   => null,
            'old_values'     => ['status' => 'pending'],
            'new_values'     => ['status' => 'voided'],
            'meta'           => [
                'voided_ids' => $abandonedIds->toArray(),
                'count'      => $count,
                'command'    => 'payments:void-abandoned',
            ],
        ]);

        $this->info("Voided {$count} abandoned payment(s).");
        return 0;
    }
}