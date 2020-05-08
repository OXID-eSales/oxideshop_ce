<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Directory\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\DirectoryValidatorException;

interface DirectoryValidatorInterface
{
    /**
     * @param string $shopSourcePath
     * @param string $compileDirectory
     *
     * @throws DirectoryValidatorException
     */
    public function validateDirectory(string $shopSourcePath, string $compileDirectory): void;
}
