<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Exception\LocaleAlreadyExistsException;

interface LocaleServiceInterface
{
    public function getByCode(string $code): Locale;

    /** @return Locale[] */
    public function getAll(): array;

    /** @return Locale[] */
    public function getForShop(int $shopId): array;

    /** @throws LocaleAlreadyExistsException */
    public function add(Locale $locale): void;

    public function update(Locale $locale): void;

    public function delete(string $code): void;

    public function addToShop(string $localeCode, int $shopId): void;

    public function removeFromShop(string $localeCode, int $shopId): void;
}
