<?php

namespace App\Controller;

use App\Application\TrafficSplit\EqualTrafficSplit;
use App\Application\TrafficSplit\WeightedTrafficSplit;
use App\Domain\Gateway\GatewayConfiguration;
use App\Domain\Payment\Payment;
use App\Infrastructure\Gateway\Gateway1;
use App\Infrastructure\Gateway\Gateway2;
use App\Infrastructure\Gateway\Gateway3;
use App\Infrastructure\Gateway\Gateway4;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class PaymentController extends AbstractController
{
    #[Route('/api/payment/equal-test', methods: ['GET'])]
    public function testEqualDistribution(): JsonResponse
    {
        $gateway1 = new Gateway1();
        $gateway2 = new Gateway2();
        $gateway3 = new Gateway3();
        $gateway4 = new Gateway4();

        $configurations = [
            new GatewayConfiguration($gateway1, 25),
            new GatewayConfiguration($gateway2, 25),
            new GatewayConfiguration($gateway3, 25),
            new GatewayConfiguration($gateway4, 25),
        ];

        $trafficSplit = new EqualTrafficSplit($configurations);

        for ($i = 0; $i < 1000; $i++) {
            $payment = new Payment(
                Uuid::v4()->toRfc4122(),
                mt_rand(100, 10000) / 100,
                'USD',
                'Test payment'
            );

            $trafficSplit->handlePayment($payment);
        }

        return new JsonResponse([
            'Gateway1' => $gateway1->getTrafficLoad(),
            'Gateway2' => $gateway2->getTrafficLoad(),
            'Gateway3' => $gateway3->getTrafficLoad(),
            'Gateway4' => $gateway4->getTrafficLoad(),
        ]);
    }

    #[Route('/api/payment/weighted-test', methods: ['GET'])]
    public function testWeightedDistribution(): JsonResponse
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

        for ($i = 0; $i < 1000; $i++) {
            $payment = new Payment(
                Uuid::v4()->toRfc4122(),
                mt_rand(100, 10000) / 100,
                'USD',
                'Test payment'
            );

            $trafficSplit->handlePayment($payment);
        }

        return new JsonResponse([
            'Gateway1' => $gateway1->getTrafficLoad(),
            'Gateway2' => $gateway2->getTrafficLoad(),
            'Gateway3' => $gateway3->getTrafficLoad(),
        ]);
    }
}