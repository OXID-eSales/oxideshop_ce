<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao;

// phpcs:disable
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataMapper\MetaDataToModuleConfigurationDataMapperInterface;
// phpcs:enable

class ModuleConfigurationDao implements ModuleConfigurationDaoInterface
{
    /**
     * @var string
     */
    private $metadataFileName = 'metadata.php';

    public function __construct(
        private MetaDataProviderInterface $metadataProvider,
        private MetaDataToModuleConfigurationDataMapperInterface $metadataMapper
    ) {
    }

    /**
     * @param string $modulePath
     *
     * @return ModuleConfiguration
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException
     */
    public function get(string $modulePath): ModuleConfiguration
    {
        $metadata = $this->metadataProvider->getData($this->getMetadataFilePath($modulePath));
        return $this->metadataMapper->fromData($metadata);
    }

    /**
     * @param string $moduleFullPath
     * @return string
     */
    private function getMetadataFilePath(string $moduleFullPath): string
    {
        return $moduleFullPath . DIRECTORY_SEPARATOR . $this->metadataFileName;
    }
}
