<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\PriceList;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class BasketTest extends IntegrationTestCase
{
    private mixed $originalMinOrderPrice;

    public function setUp(): void
    {
        parent::setUp();
        $this->originalMinOrderPrice = Registry::getConfig()->getConfigParam('iMinOrderPrice');
    }

    public function tearDown(): void
    {
        Registry::getConfig()->setConfigParam('iMinOrderPrice', $this->originalMinOrderPrice);
        parent::tearDown();
    }

    public function testRemainingAmountAndBelowMinimumStatus(): void
    {
        Registry::getConfig()->setConfigParam('iMinOrderPrice', 100);
        $basket = $this->createBasket(1, 50, 20);

        $this->assertEquals(Price::getPriceInActCurrency(100) - 70, $basket->getRemainingAmountToReachMinOrderPrice());
        $this->assertTrue($basket->isBelowMinOrderPrice());
    }

    public function testRemainingAmountIsZeroWhenMinimumIsReachedOrExceeded(): void
    {
        Registry::getConfig()->setConfigParam('iMinOrderPrice', 100);
        $basket = $this->createBasket(1, 120, 0);

        $this->assertSame(0, $basket->getRemainingAmountToReachMinOrderPrice());
        $this->assertFalse($basket->isBelowMinOrderPrice());
    }

    public function testRemainingAmountIsZeroWhenBasketIsEmpty(): void
    {
        Registry::getConfig()->setConfigParam('iMinOrderPrice', 100);
        $basket = $this->createBasket(0, 0, 0);

        $this->assertSame(0, $basket->getRemainingAmountToReachMinOrderPrice());
        $this->assertFalse($basket->isBelowMinOrderPrice());
    }

    private function createBasket(int $productsCount, float $discountedProductsPrice, float $notDiscountedProductsPrice): Basket
    {
        $notDiscountedPriceList = $this->createStub(PriceList::class);
        $notDiscountedPriceList->method('getBruttoSum')->willReturn($notDiscountedProductsPrice);

        $basket = $this->getMockBuilder(Basket::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProductsCount', 'getNotDiscountProductsPrice', 'getDiscountedProductsBruttoPrice'])
            ->getMock();
        $basket->expects($this->exactly(2))->method('getProductsCount')->willReturn($productsCount);
        $calculationCalls = $productsCount ? 2 : 0;
        $basket->expects($this->exactly($calculationCalls))
            ->method('getNotDiscountProductsPrice')
            ->willReturn($notDiscountedPriceList);
        $basket->expects($this->exactly($calculationCalls))
            ->method('getDiscountedProductsBruttoPrice')
            ->willReturn($discountedProductsPrice);

        return $basket;
    }
}
