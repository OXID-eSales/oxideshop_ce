<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;

interface LocaleDataMapperInterface
{
    public function toData(Locale $locale): array;

    public function fromData(array $data): Locale;
}
