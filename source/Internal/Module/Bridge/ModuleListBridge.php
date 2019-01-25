<?php declare(strict_types=1);

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
class ModuleListBridge implements ModuleListBridgeInterface
{
    /**
     * @param string $directoryPath
     *
     * @throws DirectoryNotExistentException
     * @throws DirectoryNotReadableException
     *
     * @return array
     */
    public function getModuleDirectoriesRecursively(string $directoryPath): array
    {
        if (false === is_dir($directoryPath)) {
            throw new DirectoryNotExistentException('The given directory does not exist: ' . $directoryPath);
        }
        if (false === is_readable($directoryPath)) {
            throw new DirectoryNotReadableException('The given directory is not readable: ' . $directoryPath);
        }

        $directoryIterator = new \RecursiveDirectoryIterator($directoryPath);
        $iterator = new \RecursiveIteratorIterator($directoryIterator);
        $metadataFiles = new \RegexIterator($iterator, '/.*\/metadata\.php$/');

        $moduleDirectories = [];
        foreach ($metadataFiles as $metadataFile) {
            $moduleDirectories[] = $metadataFile->getPath();
        }

        return $moduleDirectories;
    }
}
