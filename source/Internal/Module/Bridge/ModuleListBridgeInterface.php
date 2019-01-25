<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Bridge;

use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotExistentException;
use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotReadableException;

/**
 * @internal
 */
interface ModuleListBridgeInterface
{
    /**
     * @param string $directoryPath
     *
     * @throws DirectoryNotExistentException
     * @throws DirectoryNotReadableException
     *
     * @return array
     */
    public function getModuleDirectoriesRecursively(string $directoryPath): array;
}
