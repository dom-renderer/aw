<?php

namespace App\PaymentGateways;

abstract class BasePaymentGateway implements PaymentGatewayInterface
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    abstract public function processPayment(array $data): array;
    abstract public function getName(): string;

    public function verifyPayment(string $transactionId): array
    {
        return [
            'success' => false,
            'message' => 'Verification not implemented for this gateway'
        ];
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        return [
            'success' => false,
            'message' => 'Refund not implemented for this gateway'
        ];
    }

    public function isAvailable(): bool
    {
        return true;
    }
}

