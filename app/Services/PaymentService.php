<?php

namespace App\Services;

use App\PaymentGateways\PaymentGatewayInterface;
use App\PaymentGateways\CashOnDeliveryGateway;
use App\PaymentGateways\CreditDebitCardGateway;
use App\Models\Payment;
use App\Models\Order;

class PaymentService
{
    protected $gateways = [];

    public function __construct()
    {
        $this->registerGateways();
    }

    protected function registerGateways()
    {
        $this->gateways['cod'] = new CashOnDeliveryGateway();
        $this->gateways['card'] = new CreditDebitCardGateway();
    }

    public function getGateway(string $method): ?PaymentGatewayInterface
    {
        $methodMap = [
            'cash_on_delivery' => 'cod',
            'credit_debit_card' => 'card',
        ];

        $gatewayKey = $methodMap[$method] ?? $method;

        return $this->gateways[$gatewayKey] ?? null;
    }

    public function processPayment(Order $order, string $method, array $paymentData): array
    {
        $gateway = $this->getGateway($method);

        if (!$gateway) {
            return [
                'success' => false,
                'message' => 'Payment method not available'
            ];
        }

        $paymentData['amount'] = $order->total_amount;
        $paymentData['currency'] = $order->currency ?? 'USD';
        $paymentData['order_id'] = $order->id;
        $paymentData['customer_id'] = $order->customer_id;

        $result = $gateway->processPayment($paymentData);

        if ($result['success']) {
            $paymentMethod = $method === 'cash_on_delivery' ? 'cod' : 'credit_card';
            
            $payment = Payment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'payment_method' => $paymentMethod,
                'payment_gateway' => $gateway->getName(),
                'amount' => $order->total_amount,
                'currency' => $order->currency ?? 'USD',
                'transaction_id' => $result['transaction_id'] ?? null,
                'status' => $result['payment_status'] ?? 'pending',
                'gateway_response' => $result,
                'payment_date' => now(),
            ]);

            if ($result['payment_status'] === 'completed') {
                $payment->completed_at = now();
                $payment->save();

                $order->payment_status = 'paid';
                $order->paid_amount = $order->total_amount;
                $order->due_amount = 0;
                $order->save();
            } else {
                $order->payment_status = 'pending';
                $order->paid_amount = 0;
                $order->due_amount = $order->total_amount;
                $order->save();
            }

            $result['payment_id'] = $payment->id;
        }

        return $result;
    }

    protected function generatePaymentNumber(): string
    {
        $year = date('Y');
        $lastPayment = Payment::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastPayment ? (int) substr($lastPayment->payment_number, -6) + 1 : 1;
        
        return 'PAY-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    public function getAvailableGateways(): array
    {
        $available = [];
        foreach ($this->gateways as $key => $gateway) {
            if ($gateway->isAvailable()) {
                $available[$key] = $gateway->getName();
            }
        }
        return $available;
    }
}

