<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ProjectConfigurationIsEmptyException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
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
     * @throws ProjectConfigurationIsEmptyException
     */
    public function getConfiguration(): ProjectConfiguration
    {
        if ($this->isConfigurationEmpty()) {
            throw new ProjectConfigurationIsEmptyException('Project configuration cannot be empty.');
        }

        return $this->getConfigurationFromStorage();
    }

    public function save(ProjectConfiguration $configuration): void
    {
        $this->shopConfigurationDao->deleteAll();

        foreach ($configuration->getShopConfigurations() as $shopId => $shopConfiguration) {
            $this->shopConfigurationDao->save($shopConfiguration, $shopId);
        }
    }

    public function isConfigurationEmpty(): bool
    {
        return false === $this->projectConfigurationDirectoryExists();
    }

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
