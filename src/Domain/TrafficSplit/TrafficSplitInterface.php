<?php

namespace App\Domain\TrafficSplit;

use App\Domain\Gateway\GatewayConfiguration;
use App\Domain\Payment\Payment;

interface TrafficSplitInterface
{
    /**
     * @param GatewayConfiguration[] $gatewayConfigurations
     */
    public function __construct(array $gatewayConfigurations);

    public function handlePayment(Payment $payment): bool;
}