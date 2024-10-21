<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use DirectoryIterator;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\Chain\ClassExtensionsChainDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\Chain\TemplateExtensionChainDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ShopConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use function dirname;
use function in_array;

class ShopConfigurationDao implements ShopConfigurationDaoInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private Filesystem $fileSystem,
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private ClassExtensionsChainDaoInterface $classExtensionsChainDao,
        private TemplateExtensionChainDaoInterface $templateExtensionChainDao
    ) {
    }

    /**
     * @param int $shopId
     *
     * @return ShopConfiguration
     * @throws ShopConfigurationNotFoundException
     */
    public function get(int $shopId): ShopConfiguration
    {
        if (!$this->isShopIdExists($shopId)) {
            throw new ShopConfigurationNotFoundException(
                'Configuration for ShopID ' . $shopId . ' not found'
            );
        }

        $configuration = new ShopConfiguration();
        $configuration->setClassExtensionsChain($this->classExtensionsChainDao->getChain($shopId));
        $configuration->setModuleTemplateExtensionChain($this->templateExtensionChainDao->getChain($shopId));

        foreach ($this->moduleConfigurationDao->getAll($shopId) as $moduleConfiguration) {
            $configuration->addModuleConfiguration($moduleConfiguration);
        }

        return $configuration;
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     * @param int $shopId
     */
    public function save(ShopConfiguration $shopConfiguration, int $shopId): void
    {
        $this->moduleConfigurationDao->deleteAll($shopId);

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            $this->moduleConfigurationDao->save($moduleConfiguration, $shopId);
        }

        $this->classExtensionsChainDao->saveChain($shopId, $shopConfiguration->getClassExtensionsChain());
    }

    /**
     * @return ShopConfiguration[]
     * @throws ShopConfigurationNotFoundException
     */
    public function getAll(): array
    {
        $configurations = [];

        foreach ($this->getShopIds() as $shopId) {
            $configurations[$shopId] = $this->get($shopId);
        }

        return $configurations;
    }

    /**
     * delete all shops configuration
     */
    public function deleteAll(): void
    {
        if ($this->fileSystem->exists($this->getShopsConfigurationDirectory())) {
            $this->fileSystem->remove(
                $this->getShopsConfigurationDirectory()
            );
        }
    }

    /**
     * @return int[]
     */
    private function getShopIds(): array
    {
        $shopIds = [];

        if (file_exists($this->getShopsConfigurationDirectory())) {
            $dir = new DirectoryIterator($this->getShopsConfigurationDirectory());

            foreach ($dir as $fileInfo) {
                if ($fileInfo->isDir() && is_numeric($fileInfo->getFilename())) {
                    $shopIds[] = (int)$fileInfo->getFilename();
                }
            }
        }

        return $shopIds;
    }

    private function getShopsConfigurationDirectory(): string
    {
        return dirname(
            $this->context->getShopConfigurationDirectory(
                $this->context->getDefaultShopId()
            )
        );
    }

    /**
     * @param int $shopId
     *
     * @return bool
     */
    private function isShopIdExists(int $shopId): bool
    {
        return in_array($shopId, $this->getShopIds(), true);
    }
}
