<?php

namespace App\Domain\Payment;

class Payment
{

    public function __construct(
        private string $id,
        private float $amount,
        private string $currency,
        private string $description = ''
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}