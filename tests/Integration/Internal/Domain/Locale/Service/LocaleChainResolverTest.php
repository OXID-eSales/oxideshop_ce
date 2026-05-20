<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Locale\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Dao\LocaleDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Exception\LocaleNotFoundException;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\LocaleChainResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class LocaleChainResolverTest extends IntegrationTestCase
{
    private LocaleDaoInterface $localeDao;
    private int $shopId;

    public function setUp(): void
    {
        parent::setUp();
        $this->localeDao = $this->get(LocaleDaoInterface::class);
        $this->shopId = $this->get(ContextInterface::class)->getCurrentShopId();
    }

    public function testReturnsActiveFallbackChain(): void
    {
        $this->addLocaleToShop(new Locale('fc_AC', 'Fallback Chain Active', 'fc_FB'));
        $this->addLocaleToShop(new Locale('fc_FB', 'Fallback Chain Fallback', 'fc_FB'));

        $result = $this->get(LocaleChainResolverInterface::class)->getActiveFallbackChain('fc_AC');

        $this->assertSame(['fc_AC', 'fc_FB', 'de_DE'], $result->getCodes());
    }

    public function testSkipsInactiveFallbackLocales(): void
    {
        $this->localeDao->add(new Locale('if_FB', 'Inactive Fallback', 'if_FB'));
        $this->addLocaleToShop(new Locale('if_AC', 'Inactive Fallback Active', 'if_FB'));

        $result = $this->get(LocaleChainResolverInterface::class)->getActiveFallbackChain('if_AC');

        $this->assertSame(['if_AC', 'de_DE'], $result->getCodes());
    }

    public function testReturnsUniqueLocaleCodes(): void
    {
        $result = $this->get(LocaleChainResolverInterface::class)->getActiveFallbackChain('de_DE');

        $this->assertSame(['de_DE'], $result->getCodes());
    }

    public function testChainIsCachedPerShopAndLocale(): void
    {
        $resolver = $this->get(LocaleChainResolverInterface::class);

        $first = $resolver->getActiveFallbackChain('de_DE');
        $second = $resolver->getActiveFallbackChain('de_DE');

        $this->assertSame($first, $second);
    }

    public function testRequestedLocaleNotInShopIsExcludedFromChain(): void
    {
        $this->localeDao->add(new Locale('os_AC', 'Out of shop', 'os_AC'));

        $result = $this->get(LocaleChainResolverInterface::class)->getActiveFallbackChain('os_AC');

        $this->assertNotContains('os_AC', $result->getCodes());
    }

    public function testThrowsLocaleNotFoundExceptionForUnknownLocale(): void
    {
        $this->expectException(LocaleNotFoundException::class);

        $this->get(LocaleChainResolverInterface::class)->getActiveFallbackChain('nope_XX');
    }

    private function addLocaleToShop(Locale $locale): void
    {
        $this->localeDao->add($locale);
        $this->localeDao->addToShop($locale->getCode(), $this->shopId);
    }
}
