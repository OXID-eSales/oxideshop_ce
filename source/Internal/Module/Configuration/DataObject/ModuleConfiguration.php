<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject;

use function in_array;

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
    private $path;

    /**
     * @var string
     */
    private $version = '';

    /**
     * @var bool
     */
    private $autoActive = false;

    /**
     * @var array
     */
    private $title = [];
    /**
     * @var array
     */
    private $description = [];
    /**
     * @var string
     */
    private $lang = '';
    /**
     * @var string
     */
    private $thumbnail = '';
    /**
     * @var string
     */
    private $author = '';
    /**
     * @var string
     */
    private $url = '';
    /**
     * @var string
     */
    private $email = '';
    /**
     * @var array
     */
    private $settings = [];
    /**
     * @var string
     */
    private $metaDataChecksum;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
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
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return ModuleConfiguration
     */
    public function setPath(string $path): ModuleConfiguration
    {
        $this->path = $path;
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
     *
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
    public function getTitle(): array
    {
        return $this->title;
    }

    /**
     * @param array $title
     *
     * @return ModuleConfiguration
     */
    public function setTitle(array $title): ModuleConfiguration
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return array
     */
    public function getDescription(): array
    {
        return $this->description;
    }

    /**
     * @param array $description
     *
     * @return ModuleConfiguration
     */
    public function setDescription(array $description): ModuleConfiguration
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     *
     * @return ModuleConfiguration
     */
    public function setLang(string $lang): ModuleConfiguration
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     *
     * @return ModuleConfiguration
     */
    public function setThumbnail(string $thumbnail): ModuleConfiguration
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoActive(): bool
    {
        return $this->autoActive;
    }

    /**
     * @param bool $autoActive
     * @return ModuleConfiguration
     */
    public function setAutoActive(bool $autoActive): ModuleConfiguration
    {
        $this->autoActive = $autoActive;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     *
     * @return ModuleConfiguration
     */
    public function setAuthor(string $author): ModuleConfiguration
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return ModuleConfiguration
     */
    public function setUrl(string $url): ModuleConfiguration
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return ModuleConfiguration
     */
    public function setEmail(string $email): ModuleConfiguration
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $metaDataChecksum
     *
     * @return ModuleConfiguration
     */
    public function setMetaDataCheckSum(string $metaDataChecksum)
    {
        $this->metaDataChecksum = $metaDataChecksum;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDataCheckSum()
    {
        return $this->metaDataChecksum;
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
     *
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
    public function addSetting(ModuleSetting $moduleSetting): ModuleConfiguration
    {
        $this->settings[$moduleSetting->getName()] = $moduleSetting;

        return $this;
    }

    /**
     * @param string $settingName
     *
     * @return bool
     */
    public function hasSetting(string $settingName): bool
    {
        return isset($this->settings[$settingName]);
    }

    /**
     * @param string $settingName
     *
     * @return ModuleSetting
     */
    public function getSetting(string $settingName): ModuleSetting
    {
        return $this->settings[$settingName];
    }

    /**
     * @param string $namespace
     *
     * @return bool
     */
    public function hasClassExtension(string $namespace): bool
    {
        $hasClassExtension = false;

        if ($this->hasSetting(ModuleSetting::CLASS_EXTENSIONS)) {
            $classExtensions = $this
                ->getSetting(ModuleSetting::CLASS_EXTENSIONS)
                ->getValue();

            $hasClassExtension = in_array($namespace, $classExtensions, true);
        }

        return $hasClassExtension;
    }
}
