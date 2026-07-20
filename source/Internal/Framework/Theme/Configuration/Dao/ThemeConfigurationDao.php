<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper\ThemeConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use DirectoryIterator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class ThemeConfigurationDao implements ThemeConfigurationDaoInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private ThemeConfigurationDataMapperInterface $dataMapper,
        private FileStorageFactoryInterface $fileStorageFactory,
        private Filesystem $filesystem,
    ) {
    }

    public function get(string $themeId, int $shopId): ThemeConfiguration
    {
        $path = $this->getThemeConfigurationFilePath($shopId, $themeId);

        if (!file_exists($path)) {
            throw new ThemeConfigurationNotFoundException(
                "No theme configuration found for id '$themeId' in shop $shopId"
            );
        }

        $configuration = $this->dataMapper->fromData(
            $this->getStorage($shopId, $themeId)->get()
        );
        $configuration->setId($themeId);

        return $configuration;
    }

    public function save(ThemeConfiguration $configuration, int $shopId): void
    {
        $this->getStorage($shopId, $configuration->getId())->save(
            $this->dataMapper->toData($configuration)
        );
    }

    public function getAll(int $shopId): array
    {
        $configurations = [];

        foreach ($this->getThemeIds($shopId) as $id) {
            $configurations[$id] = $this->get($id, $shopId);
        }

        return $configurations;
    }

    public function delete(string $themeId, int $shopId): void
    {
        $this->filesystem->remove(
            $this->getThemeConfigurationFilePath($shopId, $themeId)
        );
    }

    public function exists(string $themeId, int $shopId): bool
    {
        return in_array($themeId, $this->getThemeIds($shopId), true);
    }

    private function getStorage(int $shopId, string $themeId): ArrayStorageInterface
    {
        return $this->fileStorageFactory->create(
            $this->getThemeConfigurationFilePath($shopId, $themeId)
        );
    }

    private function getThemesConfigurationDirectory(int $shopId): string
    {
        return Path::join($this->context->getShopConfigurationDirectory($shopId), 'themes');
    }

    private function getThemeConfigurationFilePath(int $shopId, string $themeId): string
    {
        return Path::join($this->getThemesConfigurationDirectory($shopId), $themeId . '.yaml');
    }

    private function getThemeIds(int $shopId): array
    {
        $ids = [];
        $directory = $this->getThemesConfigurationDirectory($shopId);

        if (file_exists($directory)) {
            $dir = new DirectoryIterator($directory);

            foreach ($dir as $fileInfo) {
                if ($fileInfo->isFile() && $fileInfo->getExtension() === 'yaml') {
                    $ids[] = $fileInfo->getBasename('.yaml');
                }
            }
        }

        sort($ids);

        return $ids;
    }
}
