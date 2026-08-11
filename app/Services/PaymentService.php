<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\Payment;
use App\Models\ReceiptSequence;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Verify a payment and update membership status accordingly.
     */
    public static function verifyPayment(Payment $payment, bool $approved = true): bool
    {
        if ($approved) {
            // Idempotency: never re-verify an already verified payment.
            if (! $payment->isPending()) {
                return true;
            }

            return DB::transaction(function () use ($payment) {
                // Generate a new receipt number
                $receiptNumber = self::generateReceiptNumber();

                // Update payment status and details
                $payment->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                    'verification_notes' => 'Payment verified by administrator',
                    'receipt_number' => $receiptNumber,
                ]);

                // Update membership status and expiry if applicable
                $membership = $payment->membership;
                if ($membership instanceof Membership) {
                    $newExpiry = self::nextMarchExpiry($membership->expiry_date);

                    $membership->update([
                        'status' => 'approved',
                        'expiry_date' => $newExpiry,
                    ]);
                }

                return true;
            });
        }

        // Idempotency: only flip to rejected from pending.
        if (! $payment->isPending()) {
            return false;
        }

        // Mark payment as rejected
        $payment->update([
            'status' => 'rejected',
            'verification_notes' => 'Payment proof rejected - invalid or unclear',
        ]);

        return false;
    }

    /**
     * Calculate the next March 31 expiry date for a membership.
     *
     * A fresh membership (no current expiry) expires at the next billing March,
     * which is at least ~1 year ahead. When given a future expiry, the following
     * March is used so renewals extend the membership by one cycle.
     */
    public static function nextMarchExpiry(?Carbon $currentExpiry = null): Carbon
    {
        $base = ($currentExpiry && $currentExpiry->isFuture())
            ? $currentExpiry->copy()
            : now();

        $year = (int) $base->addYear()->format('Y');

        $candidate = Carbon::create($year, 3, 31, 0, 0, 0);
        if ($candidate->isPast()) {
            $candidate = Carbon::create($year + 1, 3, 31, 0, 0, 0);
        }

        return $candidate;
    }

    /**
     * Generate a new receipt number for the current financial year.
     */
    public static function generateReceiptNumber(): string
    {
        $now = now();
        $fyYear = $now->month >= 4 ? $now->year : $now->year - 1;
        $prefix = "RCPT-{$fyYear}-";

        return DB::transaction(function () use ($fyYear, $prefix) {
            $sequence = ReceiptSequence::lockForUpdate()->firstOrCreate(
                ['financial_year' => (string) $fyYear],
                ['last_sequence' => 0]
            );

            $sequence->increment('last_sequence');

            $sequenceNumber = str_pad((string) $sequence->last_sequence, 4, '0', STR_PAD_LEFT);

            return $prefix.$sequenceNumber;
        });
    }

    /**
     * Generate a unique payment reference.
     */
    public static function generateReference(): string
    {
        do {
            $ref = 'CCHPL-'.now()->format('Ymd').'-'.str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Payment::where('transaction_reference', $ref)->exists());

        return $ref;
    }

    /**
     * Get payment instructions for a provider.
     */
    public static function getPaymentInstructions(string $provider, string $amount, string $reference): string
    {
        if ($provider === 'mpesa') {
            return "Pay M{$amount} to:\nM-Pesa Shortcode: ".config('payments.mpesa_shortcode', 'CONTACT_SUPPORT')
                ."\nReference: {$reference}";
        }

        return "Pay M{$amount} to:\nEcoCash Merchant: ".config('payments.ecocash_merchant', 'CONTACT_SUPPORT')
            ."\nReference: {$reference}";
    }

    /**
     * Calculate penalty amount.
     */
    public static function calculatePenalty(float $annualFee): float
    {
        return round($annualFee * 0.10, 2);
    }

    /**
     * Check if membership is overdue for suspension.
     */
    public static function isOverdueForSuspension(Membership $membership): bool
    {
        if ($membership->status !== 'approved') {
            return false;
        }

        return $membership->expiry_date
            && $membership->expiry_date->isPast()
            && $membership->expiry_date->diffInMonths(now()) >= 6;
    }
}
