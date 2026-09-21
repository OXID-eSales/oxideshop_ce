<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSettingCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Exception\ParseException;

readonly class ThemeMetaDataProvider implements ThemeMetaDataProviderInterface
{
    private const METADATA_FILE_NAME = 'metadata.yaml';

    public function __construct(
        private FileStorageFactoryInterface $fileStorageFactory,
        private NodeInterface $node,
        private ThemeSettingCacheInterface $processedDataCache,
    ) {
    }

    public function get(string $themePath): ThemeMetaData
    {
        $metadataFilePath = Path::join($themePath, self::METADATA_FILE_NAME);

        if (!is_readable($metadataFilePath)) {
            throw new InvalidThemeMetaDataException(
                "Theme metadata file not readable at $metadataFilePath"
            );
        }

        $data = $this->getProcessedData($metadataFilePath);

        return (new ThemeMetaData())
            ->setId($data['id'])
            ->setVersion($data['version'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setThumbnail($data['thumbnail'])
            ->setAuthor($data['author'])
            ->setParentTheme($data['parentTheme'])
            ->setParentVersions($data['parentVersions']);
    }

    private function getProcessedData(string $metadataFilePath): array
    {
        $cacheKey = 'theme-processed-meta-' . md5($metadataFilePath) . '-' . (@filemtime($metadataFilePath) ?: 0);

        try {
            return $this->processedDataCache->get($cacheKey);
        } catch (CacheItemNotFoundException) {
        }

        try {
            $data = $this->node->finalize(
                $this->node->normalize($this->fileStorageFactory->create($metadataFilePath)->get())
            );
            $this->processedDataCache->put($cacheKey, $data);

            return $data;
        } catch (InvalidConfigurationException | ParseException $exception) {
            throw new InvalidThemeMetaDataException(
                "metadata.yaml at $metadataFilePath is invalid: {$exception->getMessage()}",
                previous: $exception
            );
        }
    }
}
