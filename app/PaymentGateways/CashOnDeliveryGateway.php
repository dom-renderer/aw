<?php

namespace App\PaymentGateways;

class CashOnDeliveryGateway extends BasePaymentGateway
{
    public function processPayment(array $data): array
    {
        return [
            'success' => true,
            'transaction_id' => 'COD-' . time() . '-' . uniqid(),
            'payment_status' => 'pending',
            'message' => 'Order placed successfully. Payment will be collected on delivery.'
        ];
    }

    public function getName(): string
    {
        return 'Cash on Delivery';
    }
}

