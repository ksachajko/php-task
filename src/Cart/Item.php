<?php

declare(strict_types=1);

namespace Recruitment\Cart;

use Recruitment\Cart\Exception\QuantityTooLowException;
use Recruitment\Entity\Product;

class Item
{
    private $product;
    private $quantity;

    public function __construct(Product $product, int $quantity)
    {
        if ($quantity < $product->getMinimumQuantity()) {
            throw new \InvalidArgumentException();
        }

        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        if ($quantity < $this->product->getMinimumQuantity()) {
            throw new QuantityTooLowException();
        }

        $this->quantity = $quantity;

        return $this;
    }

    public function getTotalPrice(): int
    {
        return $this->quantity * $this->product->getUnitPrice();
    }

    public function getTotalPriceGross(): int
    {
        $netPrice = $this->getTotalPrice();
        #TODO perhaps refactor tax to already have decimal values, like 23% -> 0.23
        #TODO is this valid float->int conversion in terms of money handling? perhaps some library
        $taxValue =  (int) round($netPrice*($this->getProduct()->getTax()->getValue()/100));

        return $netPrice + $taxValue;
    }
}
