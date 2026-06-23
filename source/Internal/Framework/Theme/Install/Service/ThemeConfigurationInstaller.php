<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Dao\ThemeMetaDataConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ThemeConfigurationInstaller implements ThemeConfigurationInstallerInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private ThemeMetaDataConfigurationDaoInterface $metadataThemeConfigurationDao,
        private ThemeConfigurationDaoInterface $themeConfigurationDao
    ) {
    }

    public function install(string $themeSourcePath): void
    {
        $relativeSource = $this->getThemeSourceRelativePath($themeSourcePath);

        foreach ($this->context->getAllShopIds() as $shopId) {
            $themeConfiguration = $this->metadataThemeConfigurationDao->get($themeSourcePath);
            $themeConfiguration->setThemeSource($relativeSource);

            $this->themeConfigurationDao->save(
                $this->preserveCustomizations($themeConfiguration, $shopId),
                $shopId
            );
        }
    }

    public function uninstall(string $themeSourcePath): void
    {
        $this->uninstallById($this->metadataThemeConfigurationDao->get($themeSourcePath)->getId());
    }

    public function uninstallById(string $themeId): void
    {
        foreach ($this->context->getAllShopIds() as $shopId) {
            if ($this->themeConfigurationDao->exists($themeId, $shopId)) {
                $this->themeConfigurationDao->delete($themeId, $shopId);
            }
        }
    }

    public function isInstalled(string $themeSourcePath): bool
    {
        return $this->themeConfigurationDao->exists(
            $this->metadataThemeConfigurationDao->get($themeSourcePath)->getId(),
            $this->context->getDefaultShopId()
        );
    }

    private function preserveCustomizations(ThemeConfiguration $themeConfiguration, int $shopId): ThemeConfiguration
    {
        if (!$this->themeConfigurationDao->exists($themeConfiguration->getId(), $shopId)) {
            return $themeConfiguration;
        }

        $existing = $this->themeConfigurationDao->get($themeConfiguration->getId(), $shopId);
        $themeConfiguration->setActivated($existing->isActivated());

        foreach ($themeConfiguration->getSettings() as $setting) {
            if ($existing->hasSetting($setting->getName())) {
                $setting->setValue($existing->getSetting($setting->getName())->getValue());
            }
        }

        return $themeConfiguration;
    }

    private function getThemeSourceRelativePath(string $themeSourcePath): string
    {
        return Path::makeRelative($themeSourcePath, $this->context->getShopRootPath());
    }
}
