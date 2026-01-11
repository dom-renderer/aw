<?php

namespace App\PaymentGateways;

interface PaymentGatewayInterface
{
    public function processPayment(array $data): array;
    public function verifyPayment(string $transactionId): array;
    public function refundPayment(string $transactionId, float $amount): array;
    public function getName(): string;
    public function isAvailable(): bool;
}

