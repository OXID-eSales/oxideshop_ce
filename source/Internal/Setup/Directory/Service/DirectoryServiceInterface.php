<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Directory\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\DirectoryException;

interface DirectoryServiceInterface
{
    /**
     * @throws DirectoryException
     */
    public function checkDirectoriesExistent(): void;

    /**
     * @throws DirectoryException
     */
    public function checkDirectoriesPermission(): void;
}
