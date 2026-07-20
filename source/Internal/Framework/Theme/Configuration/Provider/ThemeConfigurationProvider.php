<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Provider;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper\ThemeConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use Symfony\Component\Filesystem\Path;

readonly class ThemeConfigurationProvider implements ThemeConfigurationProviderInterface
{
    private const CONFIG_FILE_NAME = 'config.yaml';

    public function __construct(
        private ThemeConfigurationDataMapperInterface $dataMapper,
        private FileStorageFactoryInterface $fileStorageFactory,
    ) {
    }

    public function get(string $themePath): ThemeConfiguration
    {
        $configFilePath = Path::join($themePath, self::CONFIG_FILE_NAME);

        if (!is_readable($configFilePath)) {
            return new ThemeConfiguration();
        }

        return $this->dataMapper->fromData(
            $this->fileStorageFactory->create($configFilePath)->get()
        );
    }
}
