<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Cache\ThemeConfigurationCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper\ThemeConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\InvalidThemeConfigurationException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use DirectoryIterator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Exception\ParseException;

readonly class ThemeConfigurationDao implements ThemeConfigurationDaoInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private ThemeConfigurationDataMapperInterface $dataMapper,
        private FileStorageFactoryInterface $fileStorageFactory,
        private Filesystem $filesystem,
        private ThemeConfigurationCacheInterface $cache,
        private EventDispatcherInterface $eventDispatcher,
        private NodeInterface $node,
    ) {
    }

    public function get(string $themeId, int $shopId): ThemeConfiguration
    {
        if (!$this->cache->exists($themeId, $shopId)) {
            if (!$this->filesystem->exists($this->getThemeConfigurationFilePath($themeId, $shopId))) {
                throw new ThemeConfigurationNotFoundException(
                    "No theme configuration found for id '$themeId' in shop $shopId"
                );
            }

            $configuration = $this->dataMapper->fromData(
                $this->getProcessedData($themeId, $shopId)
            );
            $configuration->setId($themeId);

            $this->cache->put($shopId, $configuration);
        }

        return clone $this->cache->get($themeId, $shopId);
    }

    public function save(ThemeConfiguration $configuration, int $shopId): void
    {
        $this->cache->evict($configuration->getId(), $shopId);

        $this->getStorage($configuration->getId(), $shopId)->save(
            $this->dataMapper->toData($configuration)
        );

        $this->eventDispatcher->dispatch(new ThemeConfigurationChangedEvent($configuration->getId(), $shopId));
    }

    public function getAll(int $shopId): array
    {
        $configurations = [];

        foreach ($this->getThemeIds($shopId) as $id) {
            try {
                $configurations[$id] = $this->get($id, $shopId);
            } catch (InvalidThemeConfigurationException) {
                continue;
            }
        }

        return $configurations;
    }

    public function delete(string $themeId, int $shopId): void
    {
        if (!$this->exists($themeId, $shopId)) {
            return;
        }

        $this->cache->evict($themeId, $shopId);
        $this->filesystem->remove($this->getThemeConfigurationFilePath($themeId, $shopId));

        $this->eventDispatcher->dispatch(new ThemeConfigurationChangedEvent($themeId, $shopId));
    }

    public function deleteAll(int $shopId): void
    {
        foreach ($this->getThemeIds($shopId) as $themeId) {
            $this->cache->evict($themeId, $shopId);
        }

        $this->filesystem->remove($this->getThemesConfigurationDirectory($shopId));
    }

    public function exists(string $themeId, int $shopId): bool
    {
        if ($this->cache->exists($themeId, $shopId)) {
            return true;
        }

        return $this->filesystem->exists($this->getThemeConfigurationFilePath($themeId, $shopId));
    }

    private function getProcessedData(string $themeId, int $shopId): array
    {
        try {
            return $this->node->finalize(
                $this->node->normalize($this->getStorage($themeId, $shopId)->get())
            );
        } catch (InvalidConfigurationException | ParseException $exception) {
            throw new InvalidThemeConfigurationException(
                sprintf(
                    'File %s is broken: %s',
                    $this->getThemeConfigurationFilePath($themeId, $shopId),
                    $exception->getMessage()
                ),
                previous: $exception
            );
        }
    }

    private function getStorage(string $themeId, int $shopId): ArrayStorageInterface
    {
        return $this->fileStorageFactory->create(
            $this->getThemeConfigurationFilePath($themeId, $shopId)
        );
    }

    private function getThemesConfigurationDirectory(int $shopId): string
    {
        return Path::join($this->context->getShopConfigurationDirectory($shopId), 'themes');
    }

    private function getThemeConfigurationFilePath(string $themeId, int $shopId): string
    {
        return Path::join($this->getThemesConfigurationDirectory($shopId), $themeId . '.yaml');
    }

    private function getThemeIds(int $shopId): array
    {
        $ids = [];
        $directory = $this->getThemesConfigurationDirectory($shopId);

        if ($this->filesystem->exists($directory)) {
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
