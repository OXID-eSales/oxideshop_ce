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

final class ArticleTest extends IntegrationTestCase
{
    private string $timeFormat = 'Y-m-d H:i:s';
    private string $defaultTimestamp = '0000-00-00 00:00:00';

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
        $product->oxarticles__oxactivefrom = new Field($past->format($this->timeFormat));
        $product->oxarticles__oxactiveto = new Field($future->format($this->timeFormat));

        $this->assertFalse($product->isVisible());
    }

    /**
     * @dataProvider validTimeRestrictionsDataProvider
     */
    public function testIsVisibleWithValidTimeRestrictions(string $activeFrom, string $activeTo): void
    {
        Registry::getConfig()->setConfigParam('blUseTimeCheck', true);

        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(false);
        $product->oxarticles__oxactivefrom = new Field($activeFrom);
        $product->oxarticles__oxactiveto = new Field($activeTo);

        $this->assertTrue($product->isVisible());
    }

    public function validTimeRestrictionsDataProvider(): array
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 day');
        $future = $now->modify('+1 day');

        return [
            [$past->format($this->timeFormat), $future->format($this->timeFormat)],
            [$this->defaultTimestamp, $future->format($this->timeFormat)],
            [$now->format($this->timeFormat), $future->format($this->timeFormat)]
        ];
    }

    /**
     * @dataProvider invalidTimeRestrictionsDataProvider
     */
    public function testIsVisibleWithInvalidTimeRestrictions(string $activeFrom, string $activeTo): void
    {
        Registry::getConfig()->setConfigParam('blUseTimeCheck', true);

        $product = oxNew(Article::class);
        $product->oxarticles__oxactive = new Field(false);
        $product->oxarticles__oxactivefrom = new Field($activeFrom);
        $product->oxarticles__oxactiveto = new Field($activeTo);

        $this->assertFalse($product->isVisible());
    }

    public function invalidTimeRestrictionsDataProvider(): array
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 day');
        $future = $now->modify('+1 day');

        return [
            [$this->defaultTimestamp, $this->defaultTimestamp],
            [$now->format($this->timeFormat), $this->defaultTimestamp],
            [$future->format($this->timeFormat), $past->format($this->timeFormat)]
        ];
    }
}
