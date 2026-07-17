<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Provider\ThemeConfigurationProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Exception\ThemeConfigurationInstallException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;
use Throwable;

readonly class ThemeConfigurationInstaller implements ThemeConfigurationInstallerInterface
{
    public function __construct(
        private ThemeMetaDataProviderInterface $metaDataProvider,
        private ThemeConfigurationProviderInterface $configurationProvider,
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeConfigurationMergerInterface $merger,
        private BasicContextInterface $context,
    ) {
    }

    public function install(string $themePath): void
    {
        $metadata = $this->metaDataProvider->get($themePath);
        $defaultConfiguration = $this->configurationProvider->get($themePath);
        $defaultConfiguration->setId($metadata->getId());
        $defaultConfiguration->setTitle($metadata->getTitle());
        $defaultConfiguration->setSource(Path::makeRelative($themePath, $this->context->getShopRootPath()));

        foreach ($this->context->getAllShopIds() as $shopId) {
            try {
                $configuration = clone $defaultConfiguration;

                if ($this->themeConfigurationDao->exists($configuration->getId(), $shopId)) {
                    $configuration = $this->merger->merge(
                        $configuration,
                        $this->themeConfigurationDao->get($configuration->getId(), $shopId)
                    );
                }

                $this->themeConfigurationDao->save($configuration, $shopId);
            } catch (Throwable $e) {
                throw new ThemeConfigurationInstallException(
                    sprintf('Failed to install theme configuration for shop %d: %s', $shopId, $e->getMessage()),
                    previous: $e
                );
            }
        }
    }

    public function uninstall(string $themePath): void
    {
        $themeId = $this->metaDataProvider->get($themePath)->getId();

        foreach ($this->context->getAllShopIds() as $shopId) {
            try {
                $this->themeConfigurationDao->delete($themeId, $shopId);
            } catch (Throwable $e) {
                throw new ThemeConfigurationInstallException(
                    sprintf('Failed to uninstall theme configuration for shop %d: %s', $shopId, $e->getMessage()),
                    previous: $e
                );
            }
        }
    }

    public function isInstalled(string $themePath): bool
    {
        $shopIds = $this->context->getAllShopIds();
        if (empty($shopIds)) {
            return false;
        }

        $themeId = $this->metaDataProvider->get($themePath)->getId();

        foreach ($shopIds as $shopId) {
            if (!$this->themeConfigurationDao->exists($themeId, $shopId)) {
                return false;
            }
        }

        return true;
    }
}
