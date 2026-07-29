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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use DirectoryIterator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

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
        private Processor $processor,
    ) {
    }

    public function get(string $themeId, int $shopId): ThemeConfiguration
    {
        if (!$this->cache->exists($themeId, $shopId)) {
            $path = $this->getThemeConfigurationFilePath($shopId, $themeId);

            if (!$this->filesystem->exists($path)) {
                throw new ThemeConfigurationNotFoundException(
                    "No theme configuration found for id '$themeId' in shop $shopId"
                );
            }

            $configuration = $this->dataMapper->fromData(
                $this->getProcessedData($shopId, $themeId)
            );
            $configuration->setId($themeId);

            $this->cache->put($shopId, $configuration);
        }

        return clone $this->cache->get($themeId, $shopId);
    }

    public function save(ThemeConfiguration $configuration, int $shopId): void
    {
        $this->cache->evict($configuration->getId(), $shopId);

        $this->getStorage($shopId, $configuration->getId())->save(
            $this->dataMapper->toData($configuration)
        );

        $this->eventDispatcher->dispatch(new ThemeConfigurationChangedEvent($configuration, $shopId));
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
        try {
            $configuration = $this->get($themeId, $shopId);
        } catch (ThemeConfigurationNotFoundException) {
            return;
        }

        $this->cache->evict($themeId, $shopId);
        $this->filesystem->remove($this->getThemeConfigurationFilePath($shopId, $themeId));

        $this->eventDispatcher->dispatch(new ThemeConfigurationChangedEvent($configuration, $shopId));
    }

    public function exists(string $themeId, int $shopId): bool
    {
        if ($this->cache->exists($themeId, $shopId)) {
            return true;
        }

        return $this->filesystem->exists($this->getThemeConfigurationFilePath($shopId, $themeId));
    }

    private function getProcessedData(int $shopId, string $themeId): array
    {
        try {
            return $this->processor->process(
                $this->node,
                [$this->getStorage($shopId, $themeId)->get()]
            );
        } catch (InvalidConfigurationException $exception) {
            throw new InvalidConfigurationException(
                sprintf(
                    'File %s is broken: %s',
                    $this->getThemeConfigurationFilePath($shopId, $themeId),
                    $exception->getMessage()
                ),
                $exception->getCode(),
                $exception
            );
        }
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
