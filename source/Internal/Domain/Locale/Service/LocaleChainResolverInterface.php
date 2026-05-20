<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\LocaleChain;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Exception\LocaleNotFoundException;

interface LocaleChainResolverInterface
{
    /** @throws LocaleNotFoundException */
    public function getActiveFallbackChain(string $localeCode): LocaleChain;
}
