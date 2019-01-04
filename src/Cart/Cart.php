<?php

declare(strict_types=1);

namespace Recruitment\Cart;

use Recruitment\Entity\Order;
use Recruitment\Entity\Product;

class Cart
{
    private $items = [];

    public function addProduct(Product $product, int $quantity = null): self
    {
        $quantity = $quantity ?? $product->getMinimumQuantity();

        try {
            $item = new Item($product, $quantity);
        } catch (\InvalidArgumentException $e) {
            #TODO what should happen when item can not be created?
        }

        $index = $this->getIndex($product);

        if ($index === null) {
            array_push($this->items, $item);
        } else {
            $this->items[$index]->setQuantity($this->items[$index]->getQuantity() + $quantity);
        }

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalPrice(): int
    {
        $totalPrice = 0;

        foreach ($this->items as $item) {
            $totalPrice += $item->getTotalPrice();
        }

        return $totalPrice;
    }

    public function getTotalPriceGross(): int
    {
        $totalPriceGross = 0;

        foreach ($this->items as $item) {
            $totalPriceGross += $item->getTotalPriceGross();
        }

        return $totalPriceGross;
    }

    public function getItem(int $index): Item
    {
        if (!array_key_exists($index, $this->items)) {
            throw new \OutOfBoundsException();
        }

        return $this->items[$index];
    }

    public function removeProduct(Product $product): void
    {
        $filtered = array_filter($this->items, function ($item) use ($product) {
            return $item->getProduct() !== $product;
        });

        $this->items = array_values($filtered);
    }

    public function setQuantity(Product $product, int $quantity): void
    {
        $index = $this->getIndex($product);

        if ($index === null) {
            $this->addProduct($product, $quantity);
        } else {
            $this->items[$index]->setQuantity($quantity);
        }
    }

    #TODO perhaps move to separate class
    public function checkout(int $id): Order
    {
        $order = new Order($id, clone $this);

        $this->items = [];

        return $order;
    }

    private function getIndex(Product $product): ?int
    {
        foreach ($this->items as $key => $item) {
            if ($item->getProduct() == $product) {
                return $key;
            }
        }

        return null;
    }
}
