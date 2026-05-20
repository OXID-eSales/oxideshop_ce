<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\LocaleChain;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttribute;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface MediaAttributeDaoInterface
{
    public function getAttributes(Id $mediaId, LocaleChain $chain, int $shopId): MediaAttributes;

    public function save(MediaAttribute $attribute): void;

    public function delete(string $name, Id $mediaId, string $localeCode, int $shopId): void;
}
