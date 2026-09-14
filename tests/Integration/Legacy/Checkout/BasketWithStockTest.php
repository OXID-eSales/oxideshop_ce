<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Checkout;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Exception\OutOfStockException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class BasketWithStockTest extends IntegrationTestCase
{
    private const PRODUCT_ID = 'abc';

    private const PRODUCT_STOCK_SIZE = 8.0;

    private const STOCK_FLAG_NON_ORDERABLE = 3;

    public function setUp(): void
    {
        parent::setUp();

        $themeSource = Path::makeRelative(
            __DIR__ . '/Fixtures/testTheme',
            $this->get(ContextInterface::class)->getShopRootPath()
        );
        $configuration = (new ThemeConfiguration())
            ->setId('testTheme')
            ->setSource($themeSource)
            ->setActivated(true);
        $configuration->addThemeSetting((new Setting())->setName('showVouchers')->setType('bool')->setValue(true));
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $shopId);

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
        } catch (OutOfStockException) {
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
