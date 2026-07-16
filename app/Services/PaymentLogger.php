<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class PaymentLogger
{
    /**
     * Logger instance for the payment channel.
     */
    protected LoggerInterface $logger;

    /**
     * Constructor initializes the payment logger channel.
     */
    public function __construct()
    {
        $this->logger = Log::channel('payment');
    }

    /**
     * Log a successful payment transaction.
     *
     * @param array $data Transaction data (transaction_id, amount, status, etc).
     */
    public function logTransaction(array $data): void
    {
        $this->logger->info('Payment transaction', [
            'user_id' => auth()->id(),
            'transaction_id' => $data['transaction_id'] ?? null,
            'amount' => $data['amount'] ?? null,
            'status' => $data['status'] ?? null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log an error related to payment processing.
     *
     * @param string $message Error message.
     * @param array $context Additional context for the error.
     */
    public function logError(string $message, array $context = []): void
    {
        $this->logger->error($message, array_merge($context, [
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ]));
    }

    /**
     * Log suspicious payment activity for security monitoring.
     *
     * @param array $data Details of the suspicious activity.
     */
    public function logSuspiciousActivity(array $data): void
    {
        $this->logger->warning('Suspicious payment activity detected', [
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'details' => $data,
        ]);
    }
}

