<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;

interface ActiveLocaleProviderInterface
{
    public function getActiveLocale(): Locale;
}
