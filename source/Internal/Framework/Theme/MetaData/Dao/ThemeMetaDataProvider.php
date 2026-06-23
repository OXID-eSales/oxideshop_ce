<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use Symfony\Component\Filesystem\Path;

readonly class ThemeMetaDataProvider implements ThemeMetaDataProviderInterface
{
    public const METADATA_FILE_NAME = 'theme.yaml';

    public function __construct(
        private FileStorageFactoryInterface $fileStorageFactory
    ) {
    }

    public function getData(string $themePath): array
    {
        $filePath = $this->getMetaDataFilePath($themePath);

        if (!file_exists($filePath)) {
            throw new InvalidThemeMetaDataException('Theme metadata file was not found in ' . $themePath);
        }

        $data = $this->fileStorageFactory->create($filePath)->get();

        if (empty($data['id'])) {
            throw new InvalidThemeMetaDataException('Theme metadata in ' . $filePath . ' must define a non-empty id.');
        }

        return $data;
    }

    public function getMetaDataFilePath(string $themePath): string
    {
        return Path::join($themePath, self::METADATA_FILE_NAME);
    }

    public function isThemePackage(string $themePath): bool
    {
        return file_exists($this->getMetaDataFilePath($themePath));
    }
}
