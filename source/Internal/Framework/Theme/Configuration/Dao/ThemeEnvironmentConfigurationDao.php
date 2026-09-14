<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Env\EnvUrlFormatter;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeEnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\InvalidThemeConfigurationException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Exception\ParseException;

readonly class ThemeEnvironmentConfigurationDao implements ThemeEnvironmentConfigurationDaoInterface
{
    public function __construct(
        private FileStorageFactoryInterface $fileStorageFactory,
        private BasicContextInterface $context,
        private NodeInterface $node,
    ) {
    }

    public function get(string $themeId, int $shopId): ThemeEnvironmentConfiguration
    {
        $path = $this->getEnvironmentConfigurationFilePath($themeId, $shopId);

        if (!file_exists($path)) {
            return new ThemeEnvironmentConfiguration();
        }

        return new ThemeEnvironmentConfiguration(
            array_map(
                static fn(array $setting): mixed => $setting['value'],
                $this->getProcessedData($path)['themeSettings'] ?? []
            )
        );
    }

    private function getProcessedData(string $path): array
    {
        try {
            return $this->node->finalize(
                $this->node->normalize($this->fileStorageFactory->create($path)->get())
            );
        } catch (InvalidConfigurationException | ParseException $exception) {
            throw new InvalidThemeConfigurationException(
                sprintf(
                    'File %s is broken: %s',
                    $path,
                    $exception->getMessage()
                ),
                previous: $exception
            );
        }
    }

    private function getEnvironmentConfigurationFilePath(string $themeId, int $shopId): string
    {
        return Path::join(
            EnvUrlFormatter::toEnvUrl($this->context->getProjectConfigurationDirectory()),
            'shops',
            (string) $shopId,
            'themes',
            $themeId . '.yaml'
        );
    }
}
