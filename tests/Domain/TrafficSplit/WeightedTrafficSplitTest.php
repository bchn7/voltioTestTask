<?php

namespace App\Tests\Domain\TrafficSplit;

use App\Application\TrafficSplit\WeightedTrafficSplit;
use App\Domain\Gateway\GatewayConfiguration;
use App\Domain\Payment\Payment;
use App\Infrastructure\Gateway\Gateway1;
use App\Infrastructure\Gateway\Gateway2;
use App\Infrastructure\Gateway\Gateway3;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class WeightedTrafficSplitTest extends TestCase
{
    public function testHandlePaymentDistributesTrafficByWeight(): void
    {
        $gateway1 = new Gateway1();
        $gateway2 = new Gateway2();
        $gateway3 = new Gateway3();

        $configurations = [
            new GatewayConfiguration($gateway1, 75),
            new GatewayConfiguration($gateway2, 10),
            new GatewayConfiguration($gateway3, 15),
        ];

        $trafficSplit = new WeightedTrafficSplit($configurations);

        echo "\nRunning Weighted Traffic Split Test with 1000 payments...\n";
        echo "Expected distribution: Gateway1: 75%, Gateway2: 10%, Gateway3: 15%\n";

        for ($i = 0; $i < 1000; $i++) {
            $payment = new Payment(
                Uuid::v4()->toRfc4122(),
                100.00,
                'USD',
                'Test payment'
            );

            $trafficSplit->handlePayment($payment);

            if ($i > 0 && $i % 200 === 0) {
                echo "Processed {$i} payments...\n";
            }
        }

        $load1 = $gateway1->getTrafficLoad();
        $load2 = $gateway2->getTrafficLoad();
        $load3 = $gateway3->getTrafficLoad();
        $totalLoad = $load1 + $load2 + $load3;

        $percentage1 = round(($load1 / $totalLoad) * 100, 2);
        $percentage2 = round(($load2 / $totalLoad) * 100, 2);
        $percentage3 = round(($load3 / $totalLoad) * 100, 2);

        echo "\nWeighted Traffic Split Results:\n";
        echo "Gateway1: {$load1} payments ({$percentage1}%) - Expected: 75%\n";
        echo "Gateway2: {$load2} payments ({$percentage2}%) - Expected: 10%\n";
        echo "Gateway3: {$load3} payments ({$percentage3}%) - Expected: 15%\n";
        echo "Total: {$totalLoad} payments\n\n";

        $this->assertEqualsWithDelta(750, $gateway1->getTrafficLoad(), 50,
            "Gateway1 should receive approximately 75% of traffic");
        $this->assertEqualsWithDelta(100, $gateway2->getTrafficLoad(), 30,
            "Gateway2 should receive approximately 10% of traffic");
        $this->assertEqualsWithDelta(150, $gateway3->getTrafficLoad(), 30,
            "Gateway3 should receive approximately 15% of traffic");
    }
}