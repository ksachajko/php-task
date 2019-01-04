<?php

declare(strict_types=1);

namespace Recruitment\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Recruitment\Entity\Tax;

class TaxTest extends TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function constructorThrowsExceptionWhenTaxValueIsNotSupported(): void
    {
        $tax = new Tax(1);
    }
}
