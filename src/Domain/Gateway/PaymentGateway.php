<?php

namespace App\Domain\Gateway;

use App\Domain\Payment\Payment;

interface PaymentGateway
{
    public function processPayment(Payment $payment): bool;
    public function getTrafficLoad(): int;
    public function getName(): string;
}