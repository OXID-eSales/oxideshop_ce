<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataMapper\MetaDataToModuleConfigurationDataMapperInterface;

class ModuleConfigurationDao implements ModuleConfigurationDaoInterface
{
    /**
     * @var string
     */
    private $metadataFileName = 'metadata.php';

    /**
     * @var MetaDataProviderInterface
     */
    private $metadataProvider;

    /**
     * @var MetaDataToModuleConfigurationDataMapperInterface
     */
    private $metadataMapper;

    /**
     * ModuleConfigurationDao constructor.
     */
    public function __construct(
        MetaDataProviderInterface $metadataProvider,
        MetaDataToModuleConfigurationDataMapperInterface $metadataMapper
    ) {
        $this->metadataProvider = $metadataProvider;
        $this->metadataMapper = $metadataMapper;
    }

    /**
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException
     */
    public function get(string $modulePath): ModuleConfiguration
    {
        $metadata = $this->metadataProvider->getData($this->getMetadataFilePath($modulePath));

        return $this->metadataMapper->fromData($metadata);
    }

    private function getMetadataFilePath(string $moduleFullPath): string
    {
        return $moduleFullPath . \DIRECTORY_SEPARATOR . $this->metadataFileName;
    }
}
