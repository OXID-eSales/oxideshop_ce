<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Event\ProjectYamlChangedEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;
use UnitEnum;

use function array_key_exists;

readonly class ParameterDao implements ParameterDaoInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private Filesystem $filesystem,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function add(string $name, UnitEnum|float|int|bool|array|string|null $value, int $shopId): void
    {
        $this->saveParameterIntoFile($name, $value, $this->getShopParameterFilePath($shopId));
    }

    public function remove(string $name, int $shopId): void
    {
        $this->removeParameterFromFile($name, $this->getShopParameterFilePath($shopId));
    }

    public function has(string $name, int $shopId): bool
    {
        return array_key_exists(
            $name,
            $this->getParameters($this->getShopParameterFilePath($shopId))
        );
    }

    private function getParameters(string $filePath): array
    {
        if (file_exists($filePath)) {
            return Yaml::parse(file_get_contents($filePath), Yaml::PARSE_CUSTOM_TAGS)['parameters'] ?? [];
        }

        return [];
    }

    private function saveParameters(array $parameters, string $filePath): void
    {
        $this->filesystem->dumpFile(
            $filePath,
            Yaml::dump(['parameters' => $parameters], 3, 2)
        );

        $this->eventDispatcher->dispatch(new ProjectYamlChangedEvent());
    }

    private function saveParameterIntoFile(string $name, mixed $value, string $filePath): void
    {
        $parameters = $this->getParameters($filePath);
        $parameters[$name] = $value;
        $this->saveParameters($parameters, $filePath);
    }

    private function removeParameterFromFile(string $name, string $filePath): void
    {
        $parameters = $this->getParameters($filePath);
        unset($parameters[$name]);
        $this->saveParameters($parameters, $filePath);
    }

    private function getShopParameterFilePath(int $shopId): string
    {
        return Path::join($this->context->getShopConfigurationDirectory($shopId), 'parameters.yaml');
    }
}
