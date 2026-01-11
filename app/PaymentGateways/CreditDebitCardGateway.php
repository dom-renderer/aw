<?php

namespace App\PaymentGateways;

class CreditDebitCardGateway extends BasePaymentGateway
{
    public function processPayment(array $data): array
    {
        $cardNumber = $data['card_number'] ?? '';
        $expiryDate = $data['expiry_date'] ?? '';
        $cvv = $data['cvv'] ?? '';
        $cardholderName = $data['cardholder_name'] ?? '';

        if (empty($cardNumber) || empty($expiryDate) || empty($cvv) || empty($cardholderName)) {
            return [
                'success' => false,
                'message' => 'All card details are required'
            ];
        }

        $cardNumber = preg_replace('/\s+/', '', $cardNumber);
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return [
                'success' => false,
                'message' => 'Invalid card number'
            ];
        }

        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            return [
                'success' => false,
                'message' => 'Invalid CVV'
            ];
        }

        $expiry = explode('-', $expiryDate);
        if (count($expiry) !== 2) {
            return [
                'success' => false,
                'message' => 'Invalid expiry date format'
            ];
        }

        $expiryYear = (int) $expiry[0];
        $expiryMonth = (int) $expiry[1];
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        if ($expiryYear < $currentYear || ($expiryYear == $currentYear && $expiryMonth < $currentMonth)) {
            return [
                'success' => false,
                'message' => 'Card has expired'
            ];
        }

        $lastFour = substr($cardNumber, -4);
        $transactionId = 'CARD-' . time() . '-' . $lastFour;

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'payment_status' => 'completed',
            'message' => 'Payment processed successfully',
            'card_last_four' => $lastFour
        ];
    }

    public function getName(): string
    {
        return 'Credit/Debit Card';
    }
}

