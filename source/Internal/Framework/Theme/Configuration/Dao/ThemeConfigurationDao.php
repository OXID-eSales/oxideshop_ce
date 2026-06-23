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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper\ThemeConfiguration\ThemeSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper\ThemeConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Event\ThemeConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Dao\ThemeMetaDataConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class ThemeConfigurationDao implements ThemeConfigurationDaoInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private ThemeConfigurationDataMapperInterface $themeConfigurationDataMapper,
        private FileStorageFactoryInterface $fileStorageFactory,
        private ThemeConfigurationCacheInterface $cache,
        private ThemeConfigurationExtenderInterface $themeConfigurationExtender,
        private NodeInterface $node,
        private Filesystem $filesystem,
        private EventDispatcherInterface $eventDispatcher,
        private ThemeMetaDataConfigurationDaoInterface $metadataConfigurationDao
    ) {
    }

    public function get(string $themeId, int $shopId): ThemeConfiguration
    {
        if (!$this->cache->exists($themeId, $shopId)) {
            if (!file_exists($this->getThemeConfigurationFilePath($shopId, $themeId))) {
                throw new ThemeConfigurationNotFoundException('There is no theme configuration with id ' . $themeId);
            }

            $themeConfiguration = $this->buildConfiguration($this->getNormalizedData($shopId, $themeId));
            $themeConfiguration = $this->themeConfigurationExtender->extend($themeConfiguration, $shopId);

            $this->cache->put($shopId, $themeConfiguration);
        }

        return $this->cache->get($themeId, $shopId);
    }

    private function buildConfiguration(array $data): ThemeConfiguration
    {
        $themeSource = (string) ($data['themeSource'] ?? '');

        $themeConfiguration = $this->metadataConfigurationDao->get(
            Path::join($this->context->getShopRootPath(), $themeSource)
        );

        $themeConfiguration
            ->setThemeSource($themeSource)
            ->setActivated((bool) ($data['activated'] ?? false));

        if (isset($data['parentTheme'])) {
            $themeConfiguration->setParentTheme($data['parentTheme']);
        }

        foreach ($data[ThemeSettingsDataMapper::MAPPING_KEY] ?? [] as $name => $settingData) {
            if (array_key_exists('value', $settingData) && $themeConfiguration->hasSetting($name)) {
                $themeConfiguration->getSetting($name)->setValue($settingData['value']);
            }
        }

        return $themeConfiguration;
    }

    public function save(ThemeConfiguration $themeConfiguration, int $shopId): void
    {
        $this->cache->evict($themeConfiguration->getId(), $shopId);

        $this->getStorage($shopId, $themeConfiguration->getId())->save(
            $this->themeConfigurationDataMapper->toData($themeConfiguration)
        );

        $this->eventDispatcher->dispatch(new ThemeConfigurationChangedEvent($themeConfiguration, $shopId));
    }

    /**
     * @return ThemeConfiguration[]
     */
    public function getAll(int $shopId): array
    {
        $themeConfigurations = [];

        foreach ($this->getThemeIds($shopId) as $id) {
            $themeConfigurations[$id] = $this->get($id, $shopId);
        }

        return $themeConfigurations;
    }

    public function delete(string $themeId, int $shopId): void
    {
        $this->cache->evict($themeId, $shopId);
        $this->filesystem->remove($this->getThemeConfigurationFilePath($shopId, $themeId));
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

    /**
     * @return string[]
     */
    private function getThemeIds(int $shopId): array
    {
        $themeIds = [];

        if (file_exists($this->getThemesConfigurationDirectory($shopId))) {
            $dir = new \DirectoryIterator($this->getThemesConfigurationDirectory($shopId));

            foreach ($dir as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $themeIds[] = $fileInfo->getBasename('.' . $fileInfo->getExtension());
                }
            }
        }

        sort($themeIds);

        return $themeIds;
    }

    private function getThemeConfigurationFilePath(int $shopId, string $themeId): string
    {
        return Path::join($this->getThemesConfigurationDirectory($shopId), $themeId . '.yaml');
    }

    private function getNormalizedData(int $shopId, string $themeId): mixed
    {
        try {
            $data = $this->node->normalize($this->getStorage($shopId, $themeId)->get());
        } catch (InvalidConfigurationException $exception) {
            throw new InvalidConfigurationException(
                'File ' . $this->getThemeConfigurationFilePath($shopId, $themeId)
                . ' is broken: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }

        return $data;
    }
}
