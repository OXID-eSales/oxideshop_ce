<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject;

/**
 * @internal
 */
class ModuleConfiguration
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $state;

    /**
     * @var array
     */
    private $settings = [];

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return ModuleConfiguration
     */
    public function setId(string $id): ModuleConfiguration
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return ModuleConfiguration
     */
    public function setState(string $state): ModuleConfiguration
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return ModuleConfiguration
     */
    public function setSettings(array $settings): ModuleConfiguration
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @param ModuleSetting $moduleSetting
     *
     * @return $this
     */
    public function setSetting(ModuleSetting $moduleSetting): ModuleConfiguration
    {
        $this->settings[$moduleSetting->getName()] = $moduleSetting;
        return $this;
    }

    /**
     * @param string $settingName
     * @return bool
     */
    public function hasSetting(string $settingName): bool
    {
        return isset($this->settings[$settingName]);
    }

    /**
     * @param string $settingName
     * @return ModuleSetting
     */
    public function getSetting(string $settingName): ModuleSetting
    {
        return $this->settings[$settingName];
    }
}
