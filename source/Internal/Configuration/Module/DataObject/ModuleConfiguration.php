<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject;

/**
 * @internal
 */
class ModuleConfiguration
{
    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $version;

    /**
     * @var array
     */
    private $settings;

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
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return ModuleConfiguration
     */
    public function setVersion(string $version): ModuleConfiguration
    {
        $this->version = $version;
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
     * @param string        $settingName
     * @param ModuleSetting $moduleSetting
     */
    public function setModuleSetting(string $settingName, ModuleSetting $moduleSetting)
    {
        $this->settings[$settingName] = $moduleSetting;
    }

    /**
     * @param string $settingName
     * @return ModuleSetting
     */
    public function getModuleSetting(string $settingName): ModuleSetting
    {
        return $this->settings[$settingName];
    }
}
