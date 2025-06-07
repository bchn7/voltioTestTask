<?php

namespace App\Domain\Gateway;

use App\Domain\Payment\Payment;

abstract class AbstractPaymentGateway implements PaymentGateway
{
    private int $trafficLoad = 0;

    public function __construct(private string $name)
    {
    }

    public function processPayment(Payment $payment): bool
    {
        $this->trafficLoad++;
        return $this->doProcessPayment($payment);
    }

    public function getTrafficLoad(): int
    {
        return $this->trafficLoad;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract protected function doProcessPayment(Payment $payment): bool;
}