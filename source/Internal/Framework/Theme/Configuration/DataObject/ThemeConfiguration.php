<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeSettingNotFoundException;

class ThemeConfiguration
{
    private string $id;
    private string $themeSource = '';
    private string $version = '';
    private bool $activated = false;
    private string $parentTheme = '';
    private array $title = [];
    private array $description = [];
    private string $thumbnail = '';
    private string $author = '';

    /**
     * @var Setting[]
     */
    private array $settings = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getThemeSource(): string
    {
        return $this->themeSource;
    }

    public function setThemeSource(string $themeSource): self
    {
        $this->themeSource = $themeSource;
        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;
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

    public function getParentTheme(): string
    {
        return $this->parentTheme;
    }

    public function setParentTheme(string $parentTheme): self
    {
        $this->parentTheme = $parentTheme;
        return $this;
    }

    public function hasParentTheme(): bool
    {
        return $this->parentTheme !== '';
    }

    public function getTitle(): array
    {
        return $this->title;
    }

    public function setTitle(array $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function setDescription(array $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return Setting[]
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    public function hasSettings(): bool
    {
        return !empty($this->settings);
    }

    public function hasSetting(string $settingName): bool
    {
        foreach ($this->settings as $setting) {
            if ($setting->getName() === $settingName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws ThemeSettingNotFoundException
     */
    public function getSetting(string $settingName): Setting
    {
        foreach ($this->settings as $setting) {
            if ($setting->getName() === $settingName) {
                return $setting;
            }
        }

        throw new ThemeSettingNotFoundException("Theme setting \"$settingName\" was not found in configuration.");
    }

    public function addSetting(Setting $setting): self
    {
        $this->settings[] = $setting;
        return $this;
    }

    /**
     * @param Setting[] $settings
     */
    public function setSettings(array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }
}
