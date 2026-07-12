<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\App;

class PaymentService
{
    private ?PaymentGatewayInterface $gateway = null;
    private ?PaymentGateway $gatewayConfig = null;

    public function __construct()
    {
        $this->loadActiveGateway();
    }

    private function loadActiveGateway(): void
    {
        $gatewayConfig = PaymentGateway::where('status', true)
            ->orderBy('priority', 'asc')
            ->first();

        if (!$gatewayConfig) {
            throw new \Exception('No active payment gateway found');
        }

        $this->gatewayConfig = $gatewayConfig;
        
        $gatewayClass = match($gatewayConfig->name) {
            'payfast' => PayfastGateway::class,
            'rapidgateway' => RapidGateway::class,
            'simpaisa' => SimpaisaGateway::class,
            default => throw new \Exception('Unknown gateway: ' . $gatewayConfig->name)
        };

        $this->gateway = App::make($gatewayClass);
    }

    public function processPayment(array $data): array
    {
        if (!$this->gateway) {
            throw new \Exception('Payment gateway not initialized');
        }

        return $this->gateway->createPayment($data);
    }

    public function verifyPayment(array $data): array
    {
        if (!$this->gateway) {
            throw new \Exception('Payment gateway not initialized');
        }

        return $this->gateway->verifyPayment($data);
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        if (!$this->gateway) {
            throw new \Exception('Payment gateway not initialized');
        }

        return $this->gateway->refundPayment($transactionId, $amount);
    }

    public function getFee(float $amount): float
    {
        if (!$this->gateway) {
            return 0;
        }

        return $this->gateway->getFee($amount);
    }

    public function getActiveGateway(): array
    {
        return [
            'name' => $this->gateway->getGatewayName(),
            'config' => $this->gatewayConfig?->toArray() ?? []
        ];
    }

    public function switchGateway(string $gatewayName): bool
    {
        \DB::transaction(function () use ($gatewayName) {
            PaymentGateway::where('status', true)->update(['status' => false]);
            
            $gateway = PaymentGateway::where('name', $gatewayName)->first();
            if ($gateway) {
                $gateway->update(['status' => true, 'priority' => 1]);
            }
        });

        $this->loadActiveGateway();
        return true;
    }
}
