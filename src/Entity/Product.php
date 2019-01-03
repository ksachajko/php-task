<?php

declare(strict_types=1);

namespace Recruitment\Entity;

use Recruitment\Entity\Exception\InvalidUnitPriceException;

class Product
{
    const MINIMUM_QUANTITY = 1;

    private $id;
    private $name;
    private $unitPrice;
    private $minimumQuantity = self::MINIMUM_QUANTITY;

    public function getId(): int
    {
        return $this->id;
    }

    #TODO needed by test, perhaps set by reflection?
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): self
    {
        if ($unitPrice === 0) {
            throw new InvalidUnitPriceException();
        }

        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getMinimumQuantity(): int
    {
        return $this->minimumQuantity;
    }

    public function setMinimumQuantity(int $minimumQuantity): self
    {
        if ($minimumQuantity < self::MINIMUM_QUANTITY) {
            throw new \InvalidArgumentException();
        }

        $this->minimumQuantity = $minimumQuantity;

        return $this;
    }
}
