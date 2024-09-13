<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Directory;

interface DirectoryValidatorInterface
{
    /**
     * @throws NoPermissionDirectoryException
     * @throws NonExistenceDirectoryException
     */
    public function validateDirectory(string $compileDirectory): void;

    /**
     * @throws NotAbsolutePathException
     */
    public function checkPathIsAbsolute(string $compileDirectory): void;
}
