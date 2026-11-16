<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service;

use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Exception\AdminUiDirectoryNotFoundException;

interface AdminUiFileLocatorInterface
{
    /**
     * @return string[]
     * @throws AdminUiDirectoryNotFoundException
     */
    public function locate(string $relativePath): array;
}
