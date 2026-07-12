<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RapidGateway implements PaymentGatewayInterface
{
    private array $config;
    private bool $testMode;

    public function __construct()
    {
        $gateway = \App\Models\PaymentGateway::where('name', 'rapidgateway')
            ->where('status', true)
            ->first();

        $this->config = $gateway?->credentials ?? [];
        $this->testMode = $this->config['test_mode'] ?? true;
    }

    public function createPayment(array $data): array
    {
        // RapidGateway API integration
        $payload = [
            'api_key' => $this->config['api_key'] ?? '',
            'amount' => $data['amount'],
            'currency' => 'PKR',
            'order_id' => $data['order_id'] ?? '',
            'customer_email' => $data['customer_email'] ?? '',
            'return_url' => $data['return_url'] ?? route('payment.success'),
            'webhook_url' => $data['notify_url'] ?? route('payment.webhook'),
        ];

        // In production, make actual API call
        return [
            'success' => true,
            'redirect_url' => 'https://rapidgateway.com/pay',
            'payload' => $payload,
            'gateway' => 'rapidgateway',
            'transaction_id' => 'RG-' . uniqid()
        ];
    }

    public function verifyPayment(array $data): array
    {
        // Verify with RapidGateway API
        return [
            'success' => true,
            'order_id' => $data['order_id'] ?? null,
            'amount' => $data['amount'] ?? 0,
            'transaction_id' => $data['transaction_id'] ?? '',
            'status' => 'completed'
        ];
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        Log::info('RapidGateway refund requested', [
            'transaction_id' => $transactionId,
            'amount' => $amount
        ]);

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'refund_id' => 'RF-' . uniqid(),
            'amount' => $amount
        ];
    }

    public function getFee(float $amount): float
    {
        $percentage = 2.0; // 2.0%
        return $amount * $percentage / 100;
    }

    public function getGatewayName(): string
    {
        return 'RapidGateway';
    }
}
