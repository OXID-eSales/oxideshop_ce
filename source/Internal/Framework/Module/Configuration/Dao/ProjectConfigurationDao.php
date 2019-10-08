<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ProjectConfigurationIsEmptyException;
use Symfony\Component\Filesystem\Filesystem;

class ProjectConfigurationDao implements ProjectConfigurationDaoInterface
{
    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * ProjectConfigurationDao constructor.
     * @param ShopConfigurationDaoInterface $shopConfigurationDao
     * @param BasicContextInterface $context
     * @param Filesystem $fileSystem
     */
    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        BasicContextInterface $context,
        Filesystem $fileSystem
    ) {
        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->context = $context;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return ProjectConfiguration
     * @throws ProjectConfigurationIsEmptyException
     */
    public function getConfiguration(): ProjectConfiguration
    {
        if ($this->isConfigurationEmpty()) {
            throw new ProjectConfigurationIsEmptyException('Project configuration cannot be empty.');
        }

        return $this->getConfigurationFromStorage();
    }

    /**
     * @param ProjectConfiguration $configuration
     */
    public function save(ProjectConfiguration $configuration)
    {
        $this->shopConfigurationDao->deleteAll();

        foreach ($configuration->getShopConfigurations() as $shopId => $shopConfiguration) {
            $this->shopConfigurationDao->save($shopConfiguration, $shopId);
        }
    }

    /**
     * @return bool
     */
    public function isConfigurationEmpty(): bool
    {
        return $this->projectConfigurationDirectoryExists() === false;
    }

    /**
     * @return ProjectConfiguration
     */
    private function getConfigurationFromStorage(): ProjectConfiguration
    {
        $projectConfiguration = new ProjectConfiguration();

        foreach ($this->shopConfigurationDao->getAll() as $shopId => $shopConfiguration) {
            $projectConfiguration->addShopConfiguration(
                $shopId,
                $shopConfiguration
            );
        }

        return $projectConfiguration;
    }

    private function projectConfigurationDirectoryExists(): bool
    {
        return $this->fileSystem->exists(
            $this->context->getProjectConfigurationDirectory()
        );
    }
}
