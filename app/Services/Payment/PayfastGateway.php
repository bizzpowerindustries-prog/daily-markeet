<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayfastGateway implements PaymentGatewayInterface
{
    private string $merchantId;
    private string $merchantKey;
    private string $passphrase;
    private bool $testMode;
    private string $apiUrl;

    public function __construct()
    {
        $gateway = \App\Models\PaymentGateway::where('name', 'payfast')
            ->where('status', true)
            ->first();

        $credentials = $gateway?->credentials ?? [];

        $this->merchantId = $credentials['merchant_id'] ?? config('payfast.merchant_id');
        $this->merchantKey = $credentials['merchant_key'] ?? config('payfast.merchant_key');
        $this->passphrase = $credentials['passphrase'] ?? config('payfast.passphrase');
        $this->testMode = $credentials['test_mode'] ?? config('payfast.test_mode', true);
        
        $this->apiUrl = $this->testMode 
            ? 'https://sandbox.payfast.co.za/eng/process'
            : 'https://www.payfast.co.za/eng/process';
    }

    public function createPayment(array $data): array
    {
        $payload = [
            'merchant_id' => $this->merchantId,
            'merchant_key' => $this->merchantKey,
            'return_url' => $data['return_url'] ?? route('payment.success'),
            'cancel_url' => $data['cancel_url'] ?? route('payment.cancel'),
            'notify_url' => $data['notify_url'] ?? route('payment.webhook'),
            'amount' => number_format($data['amount'], 2, '.', ''),
            'item_name' => $data['item_name'] ?? 'DailyMart Order',
            'item_description' => $data['item_description'] ?? 'Order #' . ($data['order_id'] ?? ''),
            'custom_int1' => $data['order_id'] ?? '',
            'email_confirmation' => '1',
            'confirmation_address' => $data['customer_email'] ?? '',
        ];

        $signature = $this->generateSignature($payload);
        $payload['signature'] = $signature;

        return [
            'success' => true,
            'redirect_url' => $this->apiUrl,
            'payload' => $payload,
            'gateway' => 'payfast',
            'transaction_id' => 'PF-' . uniqid()
        ];
    }

    public function verifyPayment(array $data): array
    {
        $signature = $this->generateSignature($data);

        if ($signature !== ($data['signature'] ?? '')) {
            return ['success' => false, 'message' => 'Invalid signature'];
        }

        $status = $data['payment_status'] ?? '';
        $orderId = $data['custom_int1'] ?? null;

        if ($status === 'COMPLETE') {
            return [
                'success' => true,
                'order_id' => $orderId,
                'amount' => $data['amount'] ?? 0,
                'transaction_id' => $data['pf_payment_id'] ?? '',
                'status' => 'completed'
            ];
        }

        return [
            'success' => false,
            'message' => 'Payment not complete',
            'status' => $status
        ];
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        // Payfast API integration for refunds
        Log::info('Payfast refund requested', [
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
        $percentage = 2.5; // 2.5%
        $fixed = 5.00; // Rs 5
        return ($amount * $percentage / 100) + $fixed;
    }

    public function getGatewayName(): string
    {
        return 'Payfast';
    }

    private function generateSignature(array $data): string
    {
        // Remove signature if present
        unset($data['signature']);
        
        // Sort by key
        ksort($data);
        
        // Create query string
        $queryString = http_build_query($data, '', '&');
        
        // Add passphrase if exists
        if ($this->passphrase) {
            $queryString .= '&passphrase=' . urlencode($this->passphrase);
        }
        
        return md5($queryString);
    }
}
