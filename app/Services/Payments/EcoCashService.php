<?php

namespace App\Services\Payments;

use App\Models\Payment;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class EcoCashService
{
    protected $client;

    protected $baseUrl;

    protected $merchantId;

    protected $merchantKey;

    protected $callbackUrl;

    public function __construct()
    {
        $this->client = new Client;

        $environment = config('payments.ecocash.environment', 'sandbox');

        $this->baseUrl = $environment === 'production'
            ? 'https://api.ecocash.co.ls'
            : 'https://sandbox.ecocash.co.ls';

        $this->merchantId = config('payments.ecocash.merchant_id');
        $this->merchantKey = config('payments.ecocash.merchant_key');
        $this->callbackUrl = config('payments.ecocash.callback_url');

        // Fail fast if configuration is missing; prevents null reference crashes later.
        if (empty($this->merchantId) || empty($this->merchantKey) || empty($this->callbackUrl)) {
            Log::critical('EcoCash configuration is missing', [
                'merchant_id_set' => ! empty($this->merchantId),
                'merchant_key_set' => ! empty($this->merchantKey),
                'callback_url_set' => ! empty($this->callbackUrl),
            ]);

            throw new \InvalidArgumentException('EcoCash configuration missing (merchant_id/merchant_key/callback_url).');
        }
    }

    /**
     * Generate signature for EcoCash API requests
     */
    protected function generateSignature(string $reference, string $amount, string $timestamp): string
    {
        return hash_hmac('sha256', $reference.$amount.$timestamp, $this->merchantKey);
    }

    /**
     * Initiate EcoCash mobile payment
     */
    public function initiatePayment(Payment $payment): ?array
    {
        $timestamp = now()->timestamp;
        $signature = $this->generateSignature(
            $payment->transaction_reference,
            $payment->amount,
            $timestamp
        );

        try {
            $response = $this->client->post($this->baseUrl.'/v1/payments/initiate', [
                'headers' => [
                    'Merchant-ID' => $this->merchantId,
                    'X-Signature' => $signature,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'reference' => $payment->transaction_reference,
                    'amount' => (float) $payment->amount,
                    'msisdn' => $payment->membership->user->phone,
                    'description' => $payment->purpose,
                    'callback_url' => $this->callbackUrl,
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info('EcoCash payment initiated', ['payment_id' => $payment->id, 'response' => $result]);

            return $result;

        } catch (\Exception $e) {
            Log::error('EcoCash payment initiation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Query payment status
     */
    public function checkPaymentStatus(string $reference): ?array
    {
        $timestamp = now()->timestamp;
        $signature = $this->generateSignature($reference, '0', $timestamp);

        try {
            $response = $this->client->get($this->baseUrl."/v1/payments/{$reference}", [
                'headers' => [
                    'Merchant-ID' => $this->merchantId,
                    'X-Signature' => $signature,
                ],
            ]);

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            Log::error('EcoCash status check failed', ['reference' => $reference, 'error' => $e->getMessage()]);

            return null;
        }
    }
}
