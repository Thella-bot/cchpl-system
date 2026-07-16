<?php

namespace App\Services\Payments;

use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * Create payment gateway instance for the specified provider
     */
    public static function make(string $provider)
    {
        return match (strtolower($provider)) {
            'mpesa' => new MpesaService(),
            'ecocash' => new EcoCashService(),
            default => throw new InvalidArgumentException("Unsupported payment provider: {$provider}"),
        };
    }

    /**
     * Get list of supported payment providers
     */
    public static function supportedProviders(): array
    {
        return ['mpesa', 'ecocash'];
    }

    /**
     * Check if provider is supported
     */
    public static function isSupported(string $provider): bool
    {
        return in_array(strtolower($provider), static::supportedProviders());
    }
}