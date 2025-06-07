<?php

namespace App\Infrastructure\Gateway;

use App\Domain\Gateway\AbstractPaymentGateway;
use App\Domain\Payment\Payment;

class Gateway3 extends AbstractPaymentGateway
{
    public function __construct()
    {
        parent::__construct('Gateway3');
    }

    protected function doProcessPayment(Payment $payment): bool
    {
        return true;
    }
}