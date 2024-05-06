<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleSettingNotFountException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event\SettingChangedEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\String\UnicodeString;

class ModuleSettingService implements ModuleSettingServiceInterface
{
    public function __construct(
        private readonly ContextInterface $context,
        private readonly ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private readonly ModuleCacheInterface $moduleCache,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getInteger(string $name, string $moduleId): int
    {
        return $this->getValue($moduleId, $name);
    }

    public function getFloat(string $name, string $moduleId): float
    {
        return $this->getValue($moduleId, $name);
    }

    public function getString(string $name, string $moduleId): UnicodeString
    {
        return new UnicodeString($this->getValue($moduleId, $name));
    }

    public function getBoolean(string $name, string $moduleId): bool
    {
        return $this->getValue($moduleId, $name);
    }

    public function getCollection(string $name, string $moduleId): array
    {
        return $this->getValue($moduleId, $name);
    }

    public function saveInteger(string $name, int $value, string $moduleId): void
    {
        $this->saveSettingToModuleConfiguration($moduleId, $name, $value);
    }

    public function saveFloat(string $name, float $value, string $moduleId): void
    {
        $this->saveSettingToModuleConfiguration($moduleId, $name, $value);
    }

    public function saveString(string $name, string $value, string $moduleId): void
    {
        $this->saveSettingToModuleConfiguration($moduleId, $name, $value);
    }

    public function saveBoolean(string $name, bool $value, string $moduleId): void
    {
        $this->saveSettingToModuleConfiguration($moduleId, $name, $value);
    }

    public function saveCollection(string $name, array $value, string $moduleId): void
    {
        $this->saveSettingToModuleConfiguration($moduleId, $name, $value);
    }

    public function exists(string $name, string $moduleId): bool
    {
        try {
            $this->getValue($moduleId, $name);
        } catch (ModuleSettingNotFountException) {
            return false;
        }

        return true;
    }

    private function saveSettingToModuleConfiguration(string $moduleId, string $name, mixed $value): void
    {
        $shopId = $this->context->getCurrentShopId();

        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);
        $setting = $moduleConfiguration->getModuleSetting($name);
        $setting->setValue($value);
        $this->moduleConfigurationDao->save($moduleConfiguration, $shopId);

        $this->eventDispatcher->dispatch(new SettingChangedEvent($name, $shopId, $moduleId));
    }

    private function getValue(string $moduleId, string $name): mixed
    {
        $shopId = $this->context->getCurrentShopId();
        $cacheKey = $this->getCacheKey($moduleId, $name);

        if (!$this->moduleCache->exists($cacheKey)) {
            $this->moduleCache->put(
                $cacheKey,
                ['value' => $this->moduleConfigurationDao->get($moduleId, $shopId)->getModuleSetting($name)->getValue()]
            );
        }

        return $this->moduleCache->get($cacheKey, $shopId)['value'];
    }

    private function getCacheKey(string $moduleId, string $name): string
    {
        return $moduleId . '-setting-' . $name;
    }
}
