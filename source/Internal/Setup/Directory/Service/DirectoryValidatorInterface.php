<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Directory\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\NonExistenceDirectoryException;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\NoPermissionDirectoryException;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\NotAbsolutePathException;

interface DirectoryValidatorInterface
{
    /**
     * @throws NoPermissionDirectoryException
     * @throws NonExistenceDirectoryException
     */
    public function validateDirectory(string $shopSourcePath, string $compileDirectory): void;

    /**
     * @throws NotAbsolutePathException
     */
    public function checkPathIsAbsolute(string $shopSourcePath, string $compileDirectory): void;
}
