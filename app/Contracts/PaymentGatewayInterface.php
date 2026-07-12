<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    public function createPayment(array $data): array;
    public function verifyPayment(array $data): array;
    public function refundPayment(string $transactionId, float $amount): array;
    public function getFee(float $amount): float;
    public function getGatewayName(): string;
}
