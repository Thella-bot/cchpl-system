<?php
namespace App\Services;

use App\Models\Payment;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Verify a payment and update membership status.
     *
     * Expiry is always aligned to 31 March per Bylaws 1.3.
     * If a current membership still has time left, we extend from its
     * existing expiry date; otherwise we start from today.
     */
    public static function verifyPayment(Payment $payment, bool $approved = true): bool
    {
        if ($approved) {
            return DB::transaction(function () use ($payment) {
                // Generate receipt number with a lock to prevent race conditions
                $receiptNumber = self::generateReceiptNumber(true);

                $payment->update([
                    'status'               => 'verified',
                    'verified_at'          => now(),
                    'verification_notes'   => 'Payment verified by administrator',
                    'receipt_number'       => $receiptNumber,
                ]);

                $membership = $payment->membership;
                if ($membership) {
                    $newExpiry = self::nextMarchExpiry($membership->expiry_date);

                    $membership->update([
                        'status'      => 'approved',
                        'expiry_date' => $newExpiry,
                    ]);
                }

                return true;
            });
        }

        $payment->update([
            'status'             => 'rejected',
            'verification_notes' => 'Payment proof rejected - invalid or unclear',
        ]);

        return false;
    }

    /**
     * Calculate next 31 March expiry date, aligned to the financial year.
     *
     * Rules (Bylaws 1.3 — fees due by 31 March annually):
     *   - If the existing expiry is in the future, extend from that date.
     *   - Otherwise extend from today.
     *   - Always land on 31 March of the appropriate year.
     */
    public static function nextMarchExpiry(?Carbon $currentExpiry = null): Carbon
    {
        $base = ($currentExpiry && $currentExpiry->isFuture())
            ? $currentExpiry->copy()
            : now();

        // Move one year forward from the base, then snap to 31 March of that year.
        $year = (int) $base->addYear()->format('Y');

        // If we've already passed 31 March of that year, go to the next year.
        $candidate = Carbon::create($year, 3, 31, 0, 0, 0);
        if ($candidate->isPast()) {
            $candidate = Carbon::create($year + 1, 3, 31, 0, 0, 0);
        }

        return $candidate;
    }

    /**
     * Generate a unique, sequential receipt number per financial year.
     *
     * Format: RCPT-YYYY-NNNN  e.g. RCPT-2025-0042
     *
     * The financial year used is the one that ends on 31 March.
     * Payments made Apr–Dec belong to the year starting that calendar year.
     * Payments made Jan–Mar belong to the year starting the previous calendar year.
     */
    public static function generateReceiptNumber(bool $lock = false): string
    {
        $now = now();
        // Financial year label: the year in which 1 April falls.
        $fyYear = $now->month >= 4 ? $now->year : $now->year - 1;

        $prefix = "RCPT-{$fyYear}-";

        // Count existing verified payments in this financial year and increment.
        $startOfFY = Carbon::create($fyYear, 4, 1, 0, 0, 0);
        $endOfFY   = Carbon::create($fyYear + 1, 3, 31, 23, 59, 59);

        $query = Payment::where('status', 'verified')
            ->whereBetween('verified_at', [$startOfFY, $endOfFY]);

        if ($lock) {
            $query->lockForUpdate();
        }

        $count = $query->count();

        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $sequence;
    }

    /**
     * Generate a unique payment transaction reference.
     * Format: CCHPL-YYYYMMDD-XXXX
     */
    public static function generateReference(): string
    {
        do {
            $ref = 'CCHPL-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Payment::where('transaction_reference', $ref)->exists());

        return $ref;
    }

    /**
     * Get human-readable payment instructions for a provider.
     */
    public static function getPaymentInstructions(string $provider, string $amount, string $reference): string
    {
        if ($provider === 'mpesa') {
            return "Pay M{$amount} to:\nM-Pesa Shortcode: " . config('payments.mpesa_shortcode', 'CONTACT_SUPPORT')
                . "\nReference: {$reference}";
        }

        return "Pay M{$amount} to:\nEcoCash Merchant: " . config('payments.ecocash_merchant', 'CONTACT_SUPPORT')
            . "\nReference: {$reference}";
    }

    /**
     * Calculate late payment penalty (Bylaws 1.3 — 10% of annual fee).
     */
    public static function calculatePenalty(float $annualFee): float
    {
        return round($annualFee * 0.10, 2);
    }

    /**
     * Determine if a membership is overdue for suspension.
     * Bylaws 1.3: non-payment for 6+ months leads to suspension.
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