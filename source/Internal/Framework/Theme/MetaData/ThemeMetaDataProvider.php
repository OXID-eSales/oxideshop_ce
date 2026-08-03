<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use Symfony\Component\Filesystem\Path;

readonly class ThemeMetaDataProvider implements ThemeMetaDataProviderInterface
{
    private const METADATA_FILE_NAME = 'metadata.yaml';

    public function __construct(
        private FileStorageFactoryInterface $fileStorageFactory,
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

        try {
            $data = $this->fileStorageFactory->create($metadataFilePath)->get();

            if (empty($data['id'])) {
                throw new InvalidThemeMetaDataException(
                    "metadata.yaml is missing required 'id' field at $metadataFilePath"
                );
            }

            return (new ThemeMetaData())
                ->setId($data['id'])
                ->setVersion($data['version'] ?? '')
                ->setTitle($data['title'] ?? '')
                ->setDescription($data['description'] ?? '')
                ->setThumbnail($data['thumbnail'] ?? '')
                ->setAuthor($data['author'] ?? '')
                ->setParentTheme($data['parentTheme'] ?? '')
                ->setParentVersions($data['parentVersions'] ?? []);
        } catch (InvalidThemeMetaDataException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new InvalidThemeMetaDataException(
                "metadata.yaml at $metadataFilePath is invalid: {$exception->getMessage()}",
                previous: $exception
            );
        }
    }
}
