<?php

namespace App\Services\Payments;

use App\Models\Payment;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected $client;
    protected $baseUrl;
    protected $consumerKey;
    protected $consumerSecret;
    protected $shortcode;
    protected $passkey;
    protected $callbackUrl;

    public function __construct()
    {
        $this->client = new Client();
        $environment = config('payments.mpesa.environment', 'sandbox');
        
        $this->baseUrl = $environment === 'production' 
            ? 'https://api.safaricom.co.ke' 
            : 'https://sandbox.safaricom.co.ke';
            
        $this->consumerKey = config('payments.mpesa.consumer_key');
        $this->consumerSecret = config('payments.mpesa.consumer_secret');
        $this->shortcode = config('payments.mpesa.shortcode');
        $this->passkey = config('payments.mpesa.passkey');
        $this->callbackUrl = config('payments.mpesa.callback_url');
    }

    /**
     * Get OAuth access token from M-Pesa
     */
    public function getAccessToken(): ?string
    {
        try {
            $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
            
            $response = $this->client->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials', [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['access_token'] ?? null;
            
        } catch (\Exception $e) {
            Log::error('M-Pesa token generation failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Initiate STK push payment request
     */
    public function initiateStkPush(Payment $payment): ?array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        try {
            $response = $this->client->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'BusinessShortCode' => $this->shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,

                    // M-Pesa “Customer Pay on 3rd Party App” integration
                    // (STK push for PayBill: PartyA=payer MSISDN, PartyB=PayBill shortcode)
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => (int) $payment->amount,
                    'PartyA' => $payment->membership->user->phone, // payer MSISDN
                    'PartyB' => $this->shortcode, // PayBill shortcode
                    'PhoneNumber' => $payment->membership->user->phone, // payer MSISDN

                    'CallBackURL' => $this->callbackUrl,
                    'AccountReference' => $payment->transaction_reference,
                    'TransactionDesc' => $payment->purpose,
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info('M-Pesa STK push initiated', ['payment_id' => $payment->id, 'response' => $result]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('M-Pesa STK push failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Query transaction status
     */
    public function queryTransaction(string $checkoutRequestId): ?array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        try {
            $response = $this->client->post($this->baseUrl . '/mpesa/stkpushquery/v1/query', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'BusinessShortCode' => $this->shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'CheckoutRequestID' => $checkoutRequestId,
                ],
            ]);

            return json_decode($response->getBody(), true);
            
        } catch (\Exception $e) {
            Log::error('M-Pesa transaction query failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}