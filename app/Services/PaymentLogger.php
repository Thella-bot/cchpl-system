<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class PaymentLogger
{
    protected LoggerInterface $logger;

public function __construct()
    {
        $this->logger = Log::channel('payment');
    }

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

public function logError(string $message, array $context = []): void
    {
        $this->logger->error($message, array_merge($context, [
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ]));
    }

public function logSuspiciousActivity(array $data): void
    {
        $this->logger->warning('Suspicious payment activity detected', [
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'details' => $data,
        ]);
    }
}
