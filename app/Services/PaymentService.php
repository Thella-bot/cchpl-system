<?php
namespace App\Services;

use App\Models\Payment;
use App\Models\Membership;
use App\Models\ReceiptSequence;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{

public static function verifyPayment(Payment $payment, bool $approved = true): bool
    {
        if ($approved) {
            return DB::transaction(function () use ($payment) {

$receiptNumber = self::generateReceiptNumber();

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

public static function generateReceiptNumber(): string
    {
        $now = now();

$fyYear = $now->month >= 4 ? $now->year : $now->year - 1;

$prefix = "RCPT-{$fyYear}-";

return DB::transaction(function () use ($fyYear, $prefix) {
            $sequence = ReceiptSequence::lockForUpdate()
                ->firstOrCreate(
                    ['financial_year' => (string) $fyYear],
                    ['last_sequence' => 0]
                );

$sequence->increment('last_sequence');

$sequenceNumber = str_pad($sequence->last_sequence, 4, '0', STR_PAD_LEFT);

return $prefix . $sequenceNumber;
        });
    }

public static function generateReference(): string
    {
        do {
            $ref = 'CCHPL-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Payment::where('transaction_reference', $ref)->exists());

return $ref;
    }

public static function getPaymentInstructions(string $provider, string $amount, string $reference): string
    {
        if ($provider === 'mpesa') {
            return "Pay M{$amount} to:\nM-Pesa Shortcode: " . config('payments.mpesa_shortcode', 'CONTACT_SUPPORT')
                . "\nReference: {$reference}";
        }

return "Pay M{$amount} to:\nEcoCash Merchant: " . config('payments.ecocash_merchant', 'CONTACT_SUPPORT')
            . "\nReference: {$reference}";
    }

public static function calculatePenalty(float $annualFee): float
    {
        return round($annualFee * 0.10, 2);
    }

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
