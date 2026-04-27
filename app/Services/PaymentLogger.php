<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class PaymentLogger
{
    protected LoggerInterface $logger;
    
    /**
     * Create a new PaymentLogger instance.
     */
    public function __construct()
    {
        $this->logger = Log::channel('payment');
    }
    
    /**
     * Log a payment transaction.
     */
    public function logTransaction(array $data): void
    {
        $this->logger->info('Payment transaction', [
            'user_id' => auth()->id(),
            'transaction_id' => $data['transaction_id'],
            'amount' => $data['amount'],
            'status' => $data['status'],
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    /**
     * Log a payment error.
     */
    public function logError(string $message, array $context = []): void
    {
        $this->logger->error($message, array_merge($context, [
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ]));
    }
    
    /**
     * Log suspicious payment activity.
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
