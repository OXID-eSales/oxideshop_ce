<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Dao\LocaleDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;

class LocaleService implements LocaleServiceInterface
{
    public function __construct(
        private readonly LocaleDaoInterface $localeDao,
    ) {
    }

    public function getByCode(string $code): Locale
    {
        return $this->localeDao->getByCode($code);
    }

    public function getAll(): array
    {
        return $this->localeDao->getAll();
    }

    public function getForShop(int $shopId): array
    {
        return $this->localeDao->getByShopId($shopId);
    }

    public function add(Locale $locale): void
    {
        $this->localeDao->add($locale);
    }

    public function update(Locale $locale): void
    {
        $this->localeDao->update($locale);
    }

    public function delete(string $code): void
    {
        $this->localeDao->delete($code);
    }

    public function addToShop(string $localeCode, int $shopId): void
    {
        $this->localeDao->addToShop($localeCode, $shopId);
    }

    public function removeFromShop(string $localeCode, int $shopId): void
    {
        $this->localeDao->removeFromShop($localeCode, $shopId);
    }
}
