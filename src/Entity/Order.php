<?php

declare(strict_types=1);

namespace Recruitment\Entity;

use Recruitment\Cart\Cart;

class Order
{
    private $id;
    private $cart;

    public function __construct(int $id, Cart $cart)
    {
        $this->id = $id;
        $this->cart = $cart;
    }

    public function getDataForView(): array
    {
        $items = [];

        foreach ($this->cart->getItems() as $item) {
            $items[] = [
                'id' => $item->getProduct()->getId(),
                'quantity' => $item->getQuantity(),
                'total_price' => $item->getTotalPrice(),
                'total_price_gross' => $item->getTotalPriceGross()
            ];
        }

        return [
            'id' => $this->id,
            'items' => $items,
            'total_price' => $this->cart->getTotalPrice(),
            'total_price_gross' => $this->cart->getTotalPriceGross()
        ];
    }
}
