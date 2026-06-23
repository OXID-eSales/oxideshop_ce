<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Env\EnvUrlFormatter;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class ThemeEnvironmentConfigurationDao implements ThemeEnvironmentConfigurationDaoInterface
{
    public function __construct(
        private FileStorageFactoryInterface $fileStorageFactory,
        private Filesystem $fileSystem,
        private NodeInterface $node,
        private BasicContextInterface $context
    ) {
    }

    public function get(string $themeId, int $shopId): array
    {
        $data = [];

        $configurationFilePath = $this->getEnvironmentConfigurationFilePath($themeId, $shopId);

        if ($this->fileSystem->exists($configurationFilePath)) {
            $storage = $this->fileStorageFactory->create($configurationFilePath);

            try {
                $data = $this->node->normalize($storage->get());
            } catch (InvalidConfigurationException $exception) {
                throw new InvalidConfigurationException(
                    'File ' . $configurationFilePath . ' is broken: ' . $exception->getMessage()
                );
            }
        }

        return $data;
    }

    public function remove(string $themeId, int $shopId): void
    {
        $path = $this->getEnvironmentConfigurationFilePath($themeId, $shopId);

        if ($this->fileSystem->exists($path)) {
            $this->fileSystem->rename($path, $path . '.bak', true);
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
