<?php

namespace App\Domain\Gateway;

class GatewayConfiguration
{
    public function __construct(
        private PaymentGateway $gateway,
        private int $weight)
    {
        if ($weight < 0 || $weight > 100) {
            throw new \InvalidArgumentException('Weight must be between 0 and 100');
        }
    }

    public function getGateway(): PaymentGateway
    {
        return $this->gateway;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }
}