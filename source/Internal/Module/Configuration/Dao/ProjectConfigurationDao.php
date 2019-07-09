<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Exception\ProjectConfigurationIsEmptyException;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
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
        $this->deleteAllEnvironments();

        foreach ($configuration->getEnvironmentConfigurations() as $environment => $environmentConfiguration) {
            $this->createEnvironmentDirectory($environment);
            foreach ($environmentConfiguration->getShopConfigurations() as $shopId => $shopConfiguration) {
                $this->shopConfigurationDao->save($shopConfiguration, $shopId, $environment);
            }
        }
    }

    /**
     * @return bool
     */
    public function isConfigurationEmpty(): bool
    {
        return $this->projectConfigurationDirectoryExists() === false
            || empty($this->getEnvironments());
    }

    /**
     * @return ProjectConfiguration
     */
    private function getConfigurationFromStorage(): ProjectConfiguration
    {
        $projectConfiguration = new ProjectConfiguration();

        foreach ($this->getEnvironments() as $environment) {
            $environmentConfiguration = new EnvironmentConfiguration();

            foreach ($this->shopConfigurationDao->getAll($environment) as $shopId => $shopConfiguration) {
                $environmentConfiguration->addShopConfiguration(
                    $shopId,
                    $shopConfiguration
                );
            }

            $projectConfiguration->addEnvironmentConfiguration($environment, $environmentConfiguration);
        }

        return $projectConfiguration;
    }

    /**
     * @return string[]
     */
    private function getEnvironments(): array
    {
        $environments = [];

        $dir = new \DirectoryIterator($this->context->getProjectConfigurationDirectory());

        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                $environments[] = $fileinfo->getFilename();
            }
        }

        return $environments;
    }

    /**
     * @param string $environment
     */
    private function createEnvironmentDirectory(string $environment): void
    {
        $this->fileSystem->mkdir(
            Path::join($this->context->getProjectConfigurationDirectory(), $environment)
        );
    }

    private function deleteAllEnvironments(): void
    {
        $this->fileSystem->remove(
            $this->context->getProjectConfigurationDirectory()
        );
    }

    private function projectConfigurationDirectoryExists(): bool
    {
        return $this->fileSystem->exists(
            $this->context->getProjectConfigurationDirectory()
        );
    }
}
