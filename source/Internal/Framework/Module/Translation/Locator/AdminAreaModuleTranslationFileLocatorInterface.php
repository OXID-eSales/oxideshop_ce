<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\Locator;

interface AdminAreaModuleTranslationFileLocatorInterface
{
    /**
     * @param string $lang
     *
     * @return array
     */
    public function locate(string $lang): array;
}
