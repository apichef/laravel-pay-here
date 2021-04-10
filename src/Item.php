<?php declare(strict_types=1);

namespace ApiChef\PayHere;

class Item
{
    private string $id;
    private string $name;
    private float $amount;
    private int $quantity;

    public function __construct(string $id, string $name, float $amount, int $quantity)
    {
        $this->id = $id;
        $this->name = $name;
        $this->amount = $amount;
        $this->quantity = $quantity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
