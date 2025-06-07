<?php

namespace App\Tests\Domain\TrafficSplit;

use App\Application\TrafficSplit\EqualTrafficSplit;
use App\Domain\Gateway\GatewayConfiguration;
use App\Domain\Payment\Payment;
use App\Infrastructure\Gateway\Gateway1;
use App\Infrastructure\Gateway\Gateway2;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class EqualTrafficSplitTest extends TestCase
{
    public function testHandlePaymentDistributesTrafficEqually(): void
    {
        $gateway1 = new Gateway1();
        $gateway2 = new Gateway2();

        $configurations = [
            new GatewayConfiguration($gateway1, 50),
            new GatewayConfiguration($gateway2, 50),
        ];

        $trafficSplit = new EqualTrafficSplit($configurations);

        echo "\nRunning Equal Traffic Split Test with 100 payments...\n";

        for ($i = 0; $i < 100; $i++) {
            $payment = new Payment(
                Uuid::v4()->toRfc4122(),
                100.00,
                'USD',
                'Test payment'
            );

            $trafficSplit->handlePayment($payment);

            if ($i > 0 && $i % 20 === 0) {
                echo "Processed {$i} payments...\n";
            }
        }

        $load1 = $gateway1->getTrafficLoad();
        $load2 = $gateway2->getTrafficLoad();
        $totalLoad = $load1 + $load2;

        $percentage1 = round(($load1 / $totalLoad) * 100, 2);
        $percentage2 = round(($load2 / $totalLoad) * 100, 2);

        echo "\nEqual Traffic Split Results:\n";
        echo "Gateway1: {$load1} payments ({$percentage1}%)\n";
        echo "Gateway2: {$load2} payments ({$percentage2}%)\n";
        echo "Total: {$totalLoad} payments\n\n";

        $this->assertEqualsWithDelta(50, $gateway1->getTrafficLoad(), 15,
            "Gateway1 should receive approximately 50% of traffic");
        $this->assertEqualsWithDelta(50, $gateway2->getTrafficLoad(), 15,
            "Gateway2 should receive approximately 50% of traffic");
    }
}