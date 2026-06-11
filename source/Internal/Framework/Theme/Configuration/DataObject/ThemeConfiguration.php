<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;

class ThemeConfiguration
{
    private string $id = '';
    private string $source = '';
    private bool $activated = false;
    private array $themeSettings = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): self
    {
        $this->activated = $activated;
        return $this;
    }

    /** @return Setting[] */
    public function getThemeSettings(): array
    {
        return $this->themeSettings;
    }

    public function hasThemeSettings(): bool
    {
        return !empty($this->themeSettings);
    }

    public function addThemeSetting(Setting $setting): self
    {
        $this->themeSettings[] = $setting;
        return $this;
    }

    public function getSettingByName(string $name): ?Setting
    {
        foreach ($this->themeSettings as $setting) {
            if ($setting->getName() === $name) {
                return $setting;
            }
        }

        return null;
    }

    public function __clone()
    {
        $this->themeSettings = array_map(fn(Setting $setting) => clone $setting, $this->themeSettings);
    }
}
