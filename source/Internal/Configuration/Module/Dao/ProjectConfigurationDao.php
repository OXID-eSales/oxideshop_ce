<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Dao;

use OxidEsales\EshopCommunity\Internal\Common\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ProjectConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ProjectConfiguration;

/**
 * @internal
 */
class ProjectConfigurationDao implements ProjectConfigurationDaoInterface
{
    /**
     * @var ArrayStorageInterface
     */
    private $arrayStorage;

    /**
     * @var ProjectConfigurationDataMapperInterface
     */
    private $projectConfigurationDataMapper;

    /**
     * ProjectConfigurationDao constructor.
     * @param ArrayStorageInterface                   $arrayStorage
     * @param ProjectConfigurationDataMapperInterface $projectConfigurationDataMapper
     */
    public function __construct(
        ArrayStorageInterface                   $arrayStorage,
        ProjectConfigurationDataMapperInterface $projectConfigurationDataMapper
    ) {
        $this->arrayStorage = $arrayStorage;
        $this->projectConfigurationDataMapper = $projectConfigurationDataMapper;
    }

    /**
     * @return ProjectConfiguration
     */
    public function getConfiguration(): ProjectConfiguration
    {
        return $this->projectConfigurationDataMapper->fromData(
            $this->arrayStorage->get()
        );
    }

    /**
     * @param ProjectConfiguration $configuration
     */
    public function persistConfiguration(ProjectConfiguration $configuration)
    {
        $this->arrayStorage->save(
            $this->projectConfigurationDataMapper->toData($configuration)
        );
    }
}
