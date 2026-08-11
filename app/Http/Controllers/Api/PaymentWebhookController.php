<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle M-Pesa Daraja API STK Push webhook callback
     * Official format: https://developer.safaricom.co.ke/docs#stkpush-query
     */
    public function handleMpesa(Request $request)
    {
        try {
            $payload = $request->all();

            // Extract M-Pesa's specific callback structure
            $stkCallback = $payload['Body']['stkCallback'] ?? null;
            if (! $stkCallback) {
                Log::warning('Invalid M-Pesa webhook - missing stkCallback', $payload);

                return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid payload'], Response::HTTP_OK);
            }

            $resultCode = $stkCallback['ResultCode'] ?? 1; // 0 = success, other = fail
            $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
            $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;

            // Extract transaction metadata from callback
            $metadata = $stkCallback['CallbackMetadata']['Item'] ?? [];
            $reference = null;
            $amount = null;
            $mpesaReceipt = null;

            foreach ($metadata as $item) {
                match ($item['Key']) {
                    'AccountReference' => $reference = $item['Value'],
                    'Amount' => $amount = $item['Value'],
                    'MpesaReceiptNumber' => $mpesaReceipt = $item['Value'],
                    default => null
                };
            }

            Log::info('M-Pesa webhook received', [
                'reference' => $reference,
                'result_code' => $resultCode,
                'receipt' => $mpesaReceipt,
                'checkout_id' => $checkoutRequestId,
            ]);

            // Find matching payment record
            $payment = Payment::where('transaction_reference', $reference)
                ->where('status', 'pending')
                ->first();

            if (! $payment) {
                Log::warning('M-Pesa webhook for unknown payment', compact('reference'));

                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted'], Response::HTTP_OK);
            }

            // Payment successful
            if ($resultCode === 0 && $amount >= $payment->amount) {
                // Idempotency: only run verification logic once.
                if ($payment->isPending()) {
                    PaymentService::verifyPayment($payment, true);
                }

                // Store M-Pesa callback identifiers (do not overwrite verified_at/status on retries)
                $payment->update([
                    'mpesa_checkout_request_id' => $checkoutRequestId,
                    'mpesa_merchant_request_id' => $merchantRequestId,
                    'mpesa_receipt_number' => $mpesaReceipt,
                ]);

                Log::info('M-Pesa payment processed', [
                    'payment_id' => $payment->id,
                    'reference' => $reference,
                    'receipt' => $mpesaReceipt,
                    'checkout_id' => $checkoutRequestId,
                    'merchant_request_id' => $merchantRequestId,
                ]);
            }
            // Payment failed/cancelled
            elseif ($resultCode !== 0) {
                // Idempotency: only reject if it's still pending.
                if ($payment->isPending()) {
                    $payment->update([
                        'status' => 'rejected',
                        'verification_notes' => 'M-Pesa failed: '.($stkCallback['ResultDesc'] ?? 'Unknown error'),
                    ]);
                }

                // Still persist callback identifiers for troubleshooting
                $payment->update([
                    'mpesa_checkout_request_id' => $checkoutRequestId,
                    'mpesa_merchant_request_id' => $merchantRequestId,
                ]);

                Log::warning('M-Pesa payment rejected/failed', [
                    'payment_id' => $payment->id,
                    'reference' => $reference,
                    'checkout_id' => $checkoutRequestId,
                    'reason' => $stkCallback['ResultDesc'],
                ]);
            }

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Processed'], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('M-Pesa webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Error'], Response::HTTP_OK);
        }
    }

    /**
     * Handle EcoCash Lesotho payment webhook
     * Official format from Econet Lesotho API documentation
     */
    public function handleEcoCash(Request $request)
    {
        try {
            $payload = $request->all();

            // EcoCash webhook structure
            $reference = $payload['reference'] ?? null;
            $status = $payload['status'] ?? null;
            $amount = $payload['amount'] ?? null;
            $transactionId = $payload['transaction_id'] ?? null;
            $failureReason = $payload['failure_reason'] ?? null;

            Log::info('EcoCash webhook received', compact('reference', 'status', 'transactionId'));

            if (! $reference) {
                Log::warning('Invalid EcoCash webhook - missing reference', $payload);

                return response()->json(['status' => 'error'], Response::HTTP_OK);
            }

            // Find matching payment
            $payment = Payment::where('transaction_reference', $reference)
                ->where('status', 'pending')
                ->first();

            if (! $payment) {
                Log::warning('EcoCash webhook for unknown payment', compact('reference'));

                return response()->json(['status' => 'accepted'], Response::HTTP_OK);
            }

            $normalizedStatus = strtolower((string) $status);
            $normalizedAmount = is_numeric($amount) ? (float) $amount : null;

            // ---- Idempotency + crash-safety (EcoCash) ----
            // Use the provider transaction_id as the idempotency key.
            // If we've already stored this transaction_id for the payment, do nothing.
            if ($normalizedStatus === 'success' && $normalizedAmount !== null && $normalizedAmount >= (float) $payment->amount) {
                if ($transactionId) {
                    // Only verify once per payment+provider transaction_id.
                    $alreadyProcessed = Payment::where('id', $payment->id)
                        ->where('transaction_id', $transactionId)
                        ->exists();

                    if (! $alreadyProcessed && $payment->isPending()) {
                        PaymentService::verifyPayment($payment, true);
                        $payment->update(['transaction_id' => $transactionId]);
                    } elseif ($payment->isPending() && ! $alreadyProcessed) {
                        // Pending but transaction_id differs; still treat as a successful callback.
                        $payment->update(['transaction_id' => $transactionId]);
                        PaymentService::verifyPayment($payment, true);
                    }
                } else {
                    // transaction_id missing: still avoid duplicate verification.
                    if ($payment->isPending()) {
                        PaymentService::verifyPayment($payment, true);
                    }
                }

                Log::info('EcoCash payment auto-verified', [
                    'payment_id' => $payment->id,
                    'txn_id' => $transactionId,
                ]);
            } elseif ($normalizedStatus !== 'success') {
                // Payment failed/cancelled. Only reject if still pending.
                if ($payment->isPending()) {
                    $payment->update([
                        'status' => 'rejected',
                        'verification_notes' => 'EcoCash failed: '.($failureReason ?? 'Unknown error'),
                    ]);
                }

                // Persist transaction id for tracking even on failure.
                if ($transactionId) {
                    $payment->update(['transaction_id' => $transactionId]);
                }

                Log::warning('EcoCash payment rejected', [
                    'payment_id' => $payment->id,
                    'status' => $status,
                    'txn_id' => $transactionId,
                ]);
            }

            return response()->json(['status' => 'processed'], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('EcoCash webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json(['status' => 'error'], Response::HTTP_OK);
        }
    }
}
