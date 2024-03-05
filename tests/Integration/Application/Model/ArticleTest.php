<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use DateTimeImmutable;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ArticleTest extends IntegrationTestCase
{
    private static string $timeFormat = 'Y-m-d H:i:s';
    private static string $defaultTimestamp = '0000-00-00 00:00:00';

    public function setUp(): void
    {
        parent::setUp();

        Registry::getConfig()->init();
        Registry::getConfig()->setConfigParam('blUseStock', false);
    }

    public function testIsVisibleWithInactive(): void
    {
        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(false);

        $this->assertFalse($product->isVisible());
    }

    public function testIsVisibleWithAlwaysActive(): void
    {
        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(true);

        $this->assertTrue($product->isVisible());
    }

    public function testIsVisibleWithValidTimeRestrictionsAndDisabledConfig(): void
    {
        Registry::getConfig()->setConfigParam('blUseTimeCheck', false);
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 day');
        $future = $now->modify('+1 day');
        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(false);
        $product->oxarticles__oxactivefrom = new Field($past->format(self::$timeFormat));
        $product->oxarticles__oxactiveto = new Field($future->format(self::$timeFormat));

        $this->assertFalse($product->isVisible());
    }

    #[DataProvider('validTimeRestrictionsDataProvider')]
    public function testIsVisibleWithValidTimeRestrictions(string $activeFrom, string $activeTo): void
    {
        Registry::getConfig()->setConfigParam('blUseTimeCheck', true);

        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(false);
        $product->oxarticles__oxactivefrom = new Field($activeFrom);
        $product->oxarticles__oxactiveto = new Field($activeTo);

        $this->assertTrue($product->isVisible());
    }

    public static function validTimeRestrictionsDataProvider(): array
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 day');
        $future = $now->modify('+1 day');

        return [
            [$past->format(self::$timeFormat), $future->format(self::$timeFormat)],
            [self::$defaultTimestamp, $future->format(self::$timeFormat)],
            [$now->format(self::$timeFormat), $future->format(self::$timeFormat)]
        ];
    }

    #[DataProvider('invalidTimeRestrictionsDataProvider')]
    public function testIsVisibleWithInvalidTimeRestrictions(string $activeFrom, string $activeTo): void
    {
        Registry::getConfig()->setConfigParam('blUseTimeCheck', true);

        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(false);
        $product->oxarticles__oxactivefrom = new Field($activeFrom);
        $product->oxarticles__oxactiveto = new Field($activeTo);

        $this->assertFalse($product->isVisible());
    }

    public static function invalidTimeRestrictionsDataProvider(): array
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 day');
        $future = $now->modify('+1 day');

        return [
            [self::$defaultTimestamp, self::$defaultTimestamp],
            [$future->format(self::$timeFormat), self::$defaultTimestamp],
            [$future->format(self::$timeFormat), $past->format(self::$timeFormat)],
            [self::$defaultTimestamp, $past->format(self::$timeFormat)]
        ];
    }

    #[DataProvider('productActiveFieldStatesDataProvider')]
    public function testIsProductAlwaysActive(?bool $active, bool $result): void
    {
        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field($active);

        $this->assertEquals($result, $product->isProductAlwaysActive());
    }

    public static function productActiveFieldStatesDataProvider(): array
    {
        return [
            'NULL value' => [null, false],
            'false value' => [false, false],
            'true value' => [true, true],
        ];
    }

    #[DataProvider('validityTimeRangesDataProvider')]
    public function testHasProductValidTimeRange(string $activeFrom, string $activeTo, bool $result): void
    {
        $product = oxNew(Article::class);
        $product->oxarticles__oxactivefrom = new Field($activeFrom);
        $product->oxarticles__oxactiveto = new Field($activeTo);

        $this->assertEquals($result, $product->hasProductValidTimeRange());
    }

    public static function validityTimeRangesDataProvider(): array
    {
        $now = new DateTimeImmutable();
        return [
            'Empty active From/To' => [self::$defaultTimestamp, self::$defaultTimestamp, false],
            'Empty active From' => [self::$defaultTimestamp, $now->format(self::$timeFormat), true],
            'Empty active To' => [$now->format(self::$timeFormat), self::$defaultTimestamp, true],
            'With active From/to' => [$now->format(self::$timeFormat), $now->format(self::$timeFormat), true],
        ];
    }

    #[DataProvider('visibilityTimeRangesDataProvider')]
    public function testIsProductActive(string $activeFrom, string $activeTo, bool $result): void
    {
        $product = oxNew(Article::class);
        $product->oxarticles__oxactivefrom = new Field($activeFrom);
        $product->oxarticles__oxactiveto = new Field($activeTo);

        $this->assertEquals($result, $product->hasActiveTimeRange());
    }

    public static function visibilityTimeRangesDataProvider(): array
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 day')->format(self::$timeFormat);
        $future = $now->modify('+1 day')->format(self::$timeFormat);
        return [
            'Empty active From/To' => [self::$defaultTimestamp, self::$defaultTimestamp, false],
            'Empty activeFrom valid activeTo' => [self::$defaultTimestamp, $future, true],
            'Empty activeFrom invalid activeTo' => [self::$defaultTimestamp, $past, false],
            'Empty activeTo valid activeFrom' => [$past, self::$defaultTimestamp, true],
            'Empty activeTo invalid activeFrom' => [$future, self::$defaultTimestamp, false],
            'With valid From/to' => [$past, $future, true],
            'With invalid From/to' => [$future, $past, false],
        ];
    }

    public static function productLowStockDataProvider(): array
    {
        return [
            'Product in low stock: Shop limit reached, Product limit undefined' => [5, 0.0, 10, false],
            'Product in low stock: Shop limit exceeded, Product limit ignored' => [11, 20.0, 10, true],
            'Product in low stock: Product limit reached' => [5, 10.0, 0, true]
        ];
    }

    #[DataProvider('productLowStockDataProvider')]
    public function testProductLowStock(
        int $productStock,
        float $productLowStockLimit,
        int $shopLowStockLimit,
        bool $productLowStockActive
    ): void {
        Registry::getConfig()->setConfigParam('blUseStock', true);
        Registry::getConfig()->setConfigParam('sStockWarningLimit', $shopLowStockLimit);

        $product = oxNew(Article::class);
        $product->assign([
            'oxarticles__oxstock' => $productStock,
            'oxarticles__oxremindamount' => $productLowStockLimit,
            'oxarticles__oxlowstockactive' => $productLowStockActive,
            'oxarticles__oxparentid' => '',
            'oxarticles__oxstockflag' => 1,
            'oxarticles__oxshopid' => 1,
            'oxarticles__oxvarstock' => $productStock,
            'oxarticles__oxvarcount' => 0
        ]);

        $this->assertEquals(1, $product->getStockStatus());
    }

    public function testProductInStock(): void
    {
        Registry::getConfig()->setConfigParam('blUseStock', true);
        Registry::getConfig()->setConfigParam('sStockWarningLimit', 3);

        $product = oxNew(Article::class);
        $product->assign([
            'oxarticles__oxstock' => 5,
            'oxarticles__oxremindamount' => 0.0,
            'oxarticles__oxlowstockactive' => false,
            'oxarticles__oxparentid' => '',
            'oxarticles__oxstockflag' => 1,
            'oxarticles__oxshopid' => 1,
            'oxarticles__oxvarstock' => 5,
            'oxarticles__oxvarcount' => 0
        ]);

        $this->assertEquals(0, $product->getStockStatus());
    }

    public function testProductOutStock(): void
    {
        Registry::getConfig()->setConfigParam('blUseStock', true);
        Registry::getConfig()->setConfigParam('sStockWarningLimit', 0);

        $product = oxNew(Article::class);
        $product->assign([
            'oxarticles__oxstock' => -1,
            'oxarticles__oxremindamount' => 0.0,
            'oxarticles__oxlowstockactive' => false,
            'oxarticles__oxparentid' => '',
            'oxarticles__oxstockflag' => 1,
            'oxarticles__oxshopid' => 1,
            'oxarticles__oxvarstock' => -1,
            'oxarticles__oxvarcount' => 0
        ]);

        $this->assertEquals(-1, $product->getStockStatus());
    }
}
