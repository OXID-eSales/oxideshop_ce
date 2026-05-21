<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Locale\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Dao\LocaleDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Exception\LocaleAlreadyExistsException;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Exception\LocaleNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class LocaleDaoTest extends TestCase
{
    use DatabaseTrait;
    use ContainerTrait;

    private LocaleDaoInterface $localeDao;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginTransaction($this->get(ConnectionFactoryInterface::class)->create());
        $this->localeDao = $this->get(LocaleDaoInterface::class);
    }

    public function tearDown(): void
    {
        $this->rollBackTransaction($this->get(ConnectionFactoryInterface::class)->create());
        parent::tearDown();
    }

    public function testAddAndGetByCode(): void
    {
        $locale = new Locale(code: 'te_ST', name: 'Test Locale', fallbackCode: 'te_ST');

        $this->localeDao->add($locale);
        $fetched = $this->localeDao->getByCode('te_ST');

        $this->assertSame('te_ST', $fetched->getCode());
        $this->assertSame('Test Locale', $fetched->getName());
    }

    public function testAddThrowsForDuplicateCode(): void
    {
        $this->localeDao->add(new Locale(code: 'du_PL', name: 'Duplicate', fallbackCode: 'du_PL'));

        $this->expectException(LocaleAlreadyExistsException::class);

        $this->localeDao->add(new Locale(code: 'du_PL', name: 'Duplicate Again', fallbackCode: 'du_PL'));
    }

    public function testGetByCodeThrowsForNonExistent(): void
    {
        $this->expectException(LocaleNotFoundException::class);

        $this->localeDao->getByCode('xx_XX');
    }

    public function testGetAll(): void
    {
        $this->localeDao->add(new Locale(code: 'ga_GA', name: 'GetAll First', fallbackCode: 'ga_GA'));
        $this->localeDao->add(new Locale(code: 'gi_GI', name: 'GetAll Second', fallbackCode: 'gi_GI'));

        $codes = array_map(fn(Locale $l) => $l->getCode(), $this->localeDao->getAll());

        $this->assertContains('ga_GA', $codes);
        $this->assertContains('gi_GI', $codes);
    }

    public function testUpdate(): void
    {
        $this->localeDao->add(new Locale(code: 'up_UP', name: 'Original', fallbackCode: 'up_UP'));

        $this->localeDao->update(new Locale(code: 'up_UP', name: 'Updated', fallbackCode: 'up_UP'));

        $fetched = $this->localeDao->getByCode('up_UP');
        $this->assertSame('Updated', $fetched->getName());
    }

    public function testDelete(): void
    {
        $this->localeDao->add(new Locale(code: 'pa_DL', name: 'Parent Delete', fallbackCode: 'pa_DL'));
        $this->localeDao->add(new Locale(code: 'dl_DL', name: 'To Delete', fallbackCode: 'pa_DL'));

        $this->localeDao->delete('dl_DL');

        $this->expectException(LocaleNotFoundException::class);
        $this->localeDao->getByCode('dl_DL');
    }

    public function testFallbackReference(): void
    {
        $this->localeDao->add(new Locale(code: 'pa_PA', name: 'Parent', fallbackCode: 'pa_PA'));
        $this->localeDao->add(new Locale(code: 'ch_CH', name: 'Child', fallbackCode: 'pa_PA'));

        $fetched = $this->localeDao->getByCode('ch_CH');
        $this->assertSame('pa_PA', $fetched->getFallbackCode());
    }

    public function testDeleteRemovesShopLocales(): void
    {
        $this->localeDao->add(new Locale(code: 'sd_SD', name: 'Shop Delete', fallbackCode: 'sd_SD'));
        $this->localeDao->addToShop('sd_SD', 1);

        $this->localeDao->delete('sd_SD');

        $codes = array_map(fn(Locale $l) => $l->getCode(), $this->localeDao->getByShopId(1));
        $this->assertNotContains('sd_SD', $codes);
    }

    public function testDeleteResetsChildFallbackToSelf(): void
    {
        $this->localeDao->add(new Locale(code: 'pr_PR', name: 'Parent', fallbackCode: 'pr_PR'));
        $this->localeDao->add(new Locale(code: 'cd_CD', name: 'Child', fallbackCode: 'pr_PR'));

        $this->localeDao->delete('pr_PR');

        $child = $this->localeDao->getByCode('cd_CD');
        $this->assertSame('cd_CD', $child->getFallbackCode());
    }

    public function testAddToShopAndGetByShopId(): void
    {
        $this->localeDao->add(new Locale(code: 'sh_SH', name: 'Shop Locale', fallbackCode: 'sh_SH'));
        $this->localeDao->addToShop('sh_SH', 1);

        $codes = array_map(fn(Locale $l) => $l->getCode(), $this->localeDao->getByShopId(1));

        $this->assertContains('sh_SH', $codes);
    }

    public function testRemoveFromShop(): void
    {
        $this->localeDao->add(new Locale(code: 'rs_RS', name: 'Remove Shop', fallbackCode: 'rs_RS'));
        $this->localeDao->addToShop('rs_RS', 1);

        $codesBefore = array_map(fn(Locale $l) => $l->getCode(), $this->localeDao->getByShopId(1));
        $this->assertContains('rs_RS', $codesBefore);

        $this->localeDao->removeFromShop('rs_RS', 1);

        $codesAfter = array_map(fn(Locale $l) => $l->getCode(), $this->localeDao->getByShopId(1));
        $this->assertNotContains('rs_RS', $codesAfter);
    }
}
