<?php

namespace App\Application\TrafficSplit;

use App\Domain\Gateway\GatewayConfiguration;
use App\Domain\Payment\Payment;
use App\Domain\TrafficSplit\TrafficSplitInterface;

class EqualTrafficSplit implements TrafficSplitInterface
{
    /**
     * @var GatewayConfiguration[]
     */
    private array $gatewayConfigurations;

    /**
     * @param GatewayConfiguration[] $gatewayConfigurations
     */
    public function __construct(array $gatewayConfigurations)
    {
        $this->validateGatewayConfigurations($gatewayConfigurations);
        $this->gatewayConfigurations = $gatewayConfigurations;
    }

    public function handlePayment(Payment $payment): bool
    {
        $totalGateways = count($this->gatewayConfigurations);

        if ($totalGateways === 0) {
            throw new \RuntimeException('No payment gateways configured');
        }

        $gatewayIndex = $this->determineGatewayIndex($totalGateways);
        $gateway = $this->gatewayConfigurations[$gatewayIndex]->getGateway();

        return $gateway->processPayment($payment);
    }

    private function determineGatewayIndex(int $totalGateways): int
    {
        $minLoad = PHP_INT_MAX;
        $selectedIndex = 0;

        foreach ($this->gatewayConfigurations as $index => $config) {
            $load = $config->getGateway()->getTrafficLoad();
            if ($load < $minLoad) {
                $minLoad = $load;
                $selectedIndex = $index;
            }
        }

        return $selectedIndex;
    }

    private function validateGatewayConfigurations(array $gatewayConfigurations): void
    {
        foreach ($gatewayConfigurations as $config) {
            if (!$config instanceof GatewayConfiguration) {
                throw new \InvalidArgumentException('All configurations must be instances of GatewayConfiguration');
            }
        }
    }
}