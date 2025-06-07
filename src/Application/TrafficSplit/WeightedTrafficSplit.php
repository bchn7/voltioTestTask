<?php

namespace App\Application\TrafficSplit;

use App\Domain\Gateway\GatewayConfiguration;
use App\Domain\Payment\Payment;
use App\Domain\TrafficSplit\TrafficSplitInterface;

class WeightedTrafficSplit implements TrafficSplitInterface
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
        $this->validateWeights($gatewayConfigurations);
        $this->gatewayConfigurations = $gatewayConfigurations;
    }

    public function handlePayment(Payment $payment): bool
    {
        if (empty($this->gatewayConfigurations)) {
            throw new \RuntimeException('No payment gateways configured');
        }

        $gateway = $this->selectGatewayByWeight();
        return $gateway->processPayment($payment);
    }

    private function selectGatewayByWeight()
    {
        $randomValue = mt_rand(1, 100);
        $cumulativeWeight = 0;

        foreach ($this->gatewayConfigurations as $config) {
            $cumulativeWeight += $config->getWeight();

            if ($randomValue <= $cumulativeWeight) {
                return $config->getGateway();
            }
        }

        return end($this->gatewayConfigurations)->getGateway();
    }

    private function validateGatewayConfigurations(array $gatewayConfigurations): void
    {
        foreach ($gatewayConfigurations as $config) {
            if (!$config instanceof GatewayConfiguration) {
                throw new \InvalidArgumentException('All configurations must be instances of GatewayConfiguration');
            }
        }
    }

    private function validateWeights(array $gatewayConfigurations): void
    {
        $totalWeight = 0;

        foreach ($gatewayConfigurations as $config) {
            $totalWeight += $config->getWeight();
        }

        if ($totalWeight !== 100) {
            throw new \InvalidArgumentException('Total weight must equal 100%');
        }
    }
}