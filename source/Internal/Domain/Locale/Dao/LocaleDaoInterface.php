<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;

interface LocaleDaoInterface
{
    public function getByCode(string $code): Locale;

    /** @return Locale[] */
    public function getAll(): array;

    /** @return Locale[] */
    public function getByShopId(int $shopId): array;

    public function add(Locale $locale): void;

    public function update(Locale $locale): void;

    public function delete(string $code): void;

    public function addToShop(string $localeCode, int $shopId): void;

    public function removeFromShop(string $localeCode, int $shopId): void;
}
