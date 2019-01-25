<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Bridge;

use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotExistentException;
use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotReadableException;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataProvider;

/**
 * @internal
 */
class ModuleBridge implements ModuleBridgeInterface
{
    /**
     * @var MetaDataProvider
     */
    private $metaDataDataProvider;

    /**
     * @param MetaDataProvider $metaDataDataProvider
     */
    public function __construct(MetaDataProvider $metaDataDataProvider)
    {
        $this->metaDataDataProvider = $metaDataDataProvider;
    }

    /**
     * @param string $directoryPath
     *
     * @throws DirectoryNotExistentException
     * @throws DirectoryNotReadableException
     *
     * @return string
     */
    public function getModuleIdFromDirectory(string $directoryPath): string
    {
        if (false === is_dir($directoryPath)) {
            throw new DirectoryNotExistentException('The given directory does not exist: ' . $directoryPath);
        }

        if (false === is_readable($directoryPath)) {
            throw new DirectoryNotReadableException('The given directory is not readable: ' . $directoryPath);
        }

        $metaDataPath = rtrim($directoryPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'metadata.php';
        $data = $this->metaDataDataProvider->getData($metaDataPath);

        return $data[MetaDataProvider::METADATA_MODULE_DATA][MetaDataProvider::METADATA_ID];
    }
}
