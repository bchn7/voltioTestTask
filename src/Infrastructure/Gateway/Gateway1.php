<?php

namespace App\Infrastructure\Gateway;

use App\Domain\Gateway\AbstractPaymentGateway;
use App\Domain\Payment\Payment;

class Gateway1 extends AbstractPaymentGateway
{
    public function __construct()
    {
        parent::__construct('Gateway1');
    }

    protected function doProcessPayment(Payment $payment): bool
    {
        return true;
    }
}