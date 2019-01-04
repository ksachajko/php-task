<?php

declare(strict_types=1);

namespace Recruitment\Tests\Cart;

use PHPUnit\Framework\TestCase;
use Recruitment\Cart\Item;
use Recruitment\Entity\Product;
use Recruitment\Entity\Tax;

class ItemTest extends TestCase
{
    /**
     * @test
     * @dataProvider getItemsData
     */
    public function itAcceptsConstructorArgumentsAndReturnsData(
        int $unitPrice,
        int $quantity,
        int $taxValue,
        int $expectedTotalPrice,
        int $expectedTotalPriceGross
    ): void {
        $product = (new Product())->setUnitPrice($unitPrice)->setTax(new Tax($taxValue));

        $item = new Item($product, $quantity);

        $this->assertEquals($product, $item->getProduct());
        $this->assertEquals($quantity, $item->getQuantity());
        $this->assertEquals($expectedTotalPrice, $item->getTotalPrice());
        $this->assertEquals($expectedTotalPriceGross, $item->getTotalPriceGross());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function constructorThrowsExceptionWhenQuantityIsTooLow(): void
    {
        $product = (new Product())->setMinimumQuantity(10);

        new Item($product, 9);
    }

    /**
     * @test
     * @expectedException \Recruitment\Cart\Exception\QuantityTooLowException
     */
    public function itThrowsExceptionWhenSettingTooLowQuantity(): void
    {
        $product = (new Product())->setMinimumQuantity(10);

        $item = new Item($product, 10);
        $item->setQuantity(9);
    }

    public function getItemsData(): array
    {
        return [
            // $unitPrice, $quantity, $taxValue, $expectedTotalPrice, $expectedTotalPriceGross
            [10000, 10, 0, 100000, 100000],
            [143, 2, 5, 286, 300],
            [500, 1, 8, 500, 540],
        ];
    }
}
