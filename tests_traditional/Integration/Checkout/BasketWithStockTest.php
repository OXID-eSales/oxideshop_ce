<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Checkout;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Exception\OutOfStockException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

final class BasketWithStockTest extends UnitTestCase
{
    private const PRODUCT_ID = 'abc';
    private const PRODUCT_STOCK_SIZE = 8.0;
    private const STOCK_FLAG_NON_ORDERABLE = 3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createProduct();
        Registry::getConfig()->setConfigParam('blAllowNegativeStock', false);
        Registry::getConfig()->setConfigParam('blUseStock', true);
    }

    public function testAddToBasketWithinStockWillAddExpectedAmount(): void
    {

        $basket = oxNew(Basket::class);
        $expectedCount = self::PRODUCT_STOCK_SIZE - 1;

        $basket->addToBasket(self::PRODUCT_ID, $expectedCount);
        $basket->calculateBasket(true);
        $basket->onUpdate();
        $count = $basket->getItemsCount();

        $this->assertSame($expectedCount, $count);
    }

    public function testAddToBasketWithStockExceededWillLimitBasketItemAmount(): void
    {
        $basket = oxNew(Basket::class);

        try {
            $basket->addToBasket(self::PRODUCT_ID, 10);
        } catch (OutOfStockException $e) {
            /** stock size was exceeded */
        }
        $basket->calculateBasket(true);
        $basket->onUpdate();
        $count = $basket->getItemsCount();

        $this->assertSame(self::PRODUCT_STOCK_SIZE, $count);
    }

    private function createProduct(): void
    {
        $product = oxNew(Article::class);
        $product->setId(self::PRODUCT_ID);
        $product->oxarticles__oxstock = new Field(self::PRODUCT_STOCK_SIZE);
        $product->oxarticles__oxstockflag = new Field(self::STOCK_FLAG_NON_ORDERABLE);
        $product->save();
    }
}
