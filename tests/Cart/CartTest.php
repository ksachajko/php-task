<?php

declare(strict_types=1);

namespace Recruitment\Tests\Cart;

use PHPUnit\Framework\TestCase;
use Recruitment\Cart\Cart;
use Recruitment\Entity\Order;
use Recruitment\Entity\Product;
use Recruitment\Entity\Tax;

class CartTest extends TestCase
{
    /**
     * @test
     */
    public function itAddsOneProduct(): void
    {
        $product = $this->buildTestProduct(1, 15000)->setTax(new Tax(0));

        $cart = new Cart();
        $cart->addProduct($product, 1);

        $this->assertCount(1, $cart->getItems());
        $this->assertEquals(15000, $cart->getTotalPrice());
        $this->assertEquals(15000, $cart->getTotalPriceGross());
        $this->assertEquals($product, $cart->getItem(0)->getProduct());
    }

    /**
     * @test
     */
    public function itRemovesExistingProduct(): void
    {
        $product1 = $this->buildTestProduct(1, 15000)->setTax(new Tax(5));
        $product2 = $this->buildTestProduct(2, 10000)->setTax(new Tax(8));

        $cart = new Cart();
        $cart->addProduct($product1, 1)
            ->addProduct($product2, 1);
        $cart->removeProduct($product1);

        $this->assertCount(1, $cart->getItems());
        $this->assertEquals(10000, $cart->getTotalPrice());
        $this->assertEquals(10800, $cart->getTotalPriceGross());

        $this->assertEquals($product2, $cart->getItem(0)->getProduct());
    }

    /**
     * @test
     */
    public function itIncreasesQuantityWhenAddingAnExistingProduct(): void
    {
        $product = $this->buildTestProduct(1, 15000)->setTax(new Tax(23));

        $cart = new Cart();
        $cart->addProduct($product, 1)
            ->addProduct($product, 2);

        $this->assertCount(1, $cart->getItems());
        $this->assertEquals(45000, $cart->getTotalPrice());
        $this->assertEquals(55350, $cart->getTotalPriceGross());
    }

    /**
     * @test
     */
    public function itUpdatesQuantityOfAnExistingItem(): void
    {
        $product = $this->buildTestProduct(1, 15000)->setTax(new Tax(5));

        $cart = new Cart();
        $cart->addProduct($product, 1)
            ->setQuantity($product, 2);

        $this->assertEquals(30000, $cart->getTotalPrice());
        $this->assertEquals(2, $cart->getItem(0)->getQuantity());
        $this->assertEquals(31500, $cart->getTotalPriceGross());
    }

    /**
     * @test
     */
    public function itAddsANewItemWhileSettingQuantityForNonExistentItem(): void
    {
        $product = $this->buildTestProduct(1, 15000)->setTax(new Tax(8));

        $cart = new Cart();
        $cart->setQuantity($product, 1);

        $this->assertEquals(15000, $cart->getTotalPrice());
        $this->assertCount(1, $cart->getItems());
        $this->assertEquals(16200, $cart->getTotalPriceGross());
    }

    /**
     * @test
     * @dataProvider getNonExistentItemIndexes
     * @expectedException \OutOfBoundsException
     */
    public function itThrowsExceptionWhileGettingNonExistentItem(int $index): void
    {
        $product = $this->buildTestProduct(1, 15000);

        $cart = new Cart();
        $cart->addProduct($product, 1);
        $cart->getItem($index);
    }

    /**
     * @test
     */
    public function removingNonExistentItemDoesNotRaiseException(): void
    {
        $cart = new Cart();
        $cart->addProduct($this->buildTestProduct(1, 15000));
        $cart->removeProduct(new Product());

        $this->assertCount(1, $cart->getItems());
    }

    /**
     * @test
     */
    public function itClearsCartAfterCheckout(): void
    {
        $cart = new Cart();
        $cart->addProduct($this->buildTestProduct(1, 15000)->setTax(new Tax(23)));
        $cart->addProduct($this->buildTestProduct(2, 10000)->setTax(new Tax(8)), 2);

        $order = $cart->checkout(7);

        $this->assertCount(0, $cart->getItems());
        $this->assertEquals(0, $cart->getTotalPrice());
        $this->assertInstanceOf(Order::class, $order);

        $expectedOrderViewData = [
            'id' => 7,
            'items' => [
                [
                    'id' => 1,
                    'quantity' => 1,
                    'total_price' => 15000,
                    'total_price_gross' => 18450
                ],
                [
                    'id' => 2,
                    'quantity' => 2,
                    'total_price' => 20000,
                    'total_price_gross' => 21600
                ],
            ],
            'total_price' => 35000,
            'total_price_gross' => 40050
        ];

        $this->assertEquals($expectedOrderViewData, $order->getDataForView());
    }

    public function getNonExistentItemIndexes(): array
    {
        return [
            [PHP_INT_MIN],
            [-1],
            [1],
            [PHP_INT_MAX],
        ];
    }

    private function buildTestProduct(int $id, int $price): Product
    {
        return (new Product())->setId($id)->setUnitPrice($price);
    }
}
