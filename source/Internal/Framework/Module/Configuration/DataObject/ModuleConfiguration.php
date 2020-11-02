<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Event;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\SmartyPluginDirectory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Template;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\TemplateBlock;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleSettingNotFountException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;

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
    private $moduleSource;

    /**
     * @var string
     */
    private $version = '';

    /**
     * @var bool
     */
    private $configured = false;

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
     * @var ClassExtension[]
     */
    private $classExtensions = [];

    /**
     * @var Template[]
     */
    private $templates = [];

    /**
     * @var Controller[]
     */
    private $controllers = [];

    /**
     * @var SmartyPluginDirectory[]
     */
    private $smartyPluginDirectories = [];

    /**
     * @var TemplateBlock[]
     */
    private $templateBlocks = [];

    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * @var Setting[]
     */
    private $moduleSettings = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getModuleSource(): string
    {
        return $this->moduleSource;
    }

    public function setModuleSource(string $moduleSource): self
    {
        $this->moduleSource = $moduleSource;

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

    public function getLang(): string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

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

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    public function setConfigured(bool $configured): self
    {
        $this->configured = $configured;

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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return ClassExtension[]
     */
    public function getClassExtensions(): array
    {
        return $this->classExtensions;
    }

    /**
     * @return $this
     */
    public function addClassExtension(ClassExtension $extension)
    {
        $this->classExtensions[] = $extension;

        return $this;
    }

    public function hasClassExtensions(): bool
    {
        return !empty($this->classExtensions);
    }

    /**
     * @return TemplateBlock[]
     */
    public function getTemplateBlocks(): array
    {
        return $this->templateBlocks;
    }

    public function addTemplateBlock(TemplateBlock $templateBlock)
    {
        $this->templateBlocks[] = $templateBlock;

        return $this;
    }

    public function hasTemplateBlocks(): bool
    {
        return !empty($this->templateBlocks);
    }

    /**
     * @return Template[]
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function addTemplate(Template $template)
    {
        $this->templates[] = $template;

        return $this;
    }

    public function hasTemplates(): bool
    {
        return !empty($this->templates);
    }

    public function hasClassExtension(string $namespace): bool
    {
        foreach ($this->getClassExtensions() as $classExtension) {
            if ($classExtension->getModuleExtensionClassName() === $namespace) {
                return true;
            }
        }

        return false;
    }

    public function isExtendingShopClass(string $shopClassNamespace): bool
    {
        foreach ($this->getClassExtensions() as $classExtension) {
            if ($classExtension->getShopClassName() === $shopClassNamespace) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return $this
     */
    public function addController(Controller $controller)
    {
        $this->controllers[] = $controller;

        return $this;
    }

    /**
     * @return Controller[]
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    public function hasControllers(): bool
    {
        return !empty($this->controllers);
    }

    /**
     * @return $this
     */
    public function addSmartyPluginDirectory(SmartyPluginDirectory $directory)
    {
        $this->smartyPluginDirectories[] = $directory;

        return $this;
    }

    /**
     * @return SmartyPluginDirectory[]
     */
    public function getSmartyPluginDirectories(): array
    {
        return $this->smartyPluginDirectories;
    }

    public function hasSmartyPluginDirectories(): bool
    {
        return !empty($this->smartyPluginDirectories);
    }

    /**
     * @return $this
     */
    public function addEvent(Event $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function hasEvents(): bool
    {
        return !empty($this->events);
    }

    /**
     * @return Setting[]
     */
    public function getModuleSettings(): array
    {
        return $this->moduleSettings;
    }

    public function hasModuleSetting(string $settingName): bool
    {
        foreach ($this->getModuleSettings() as $setting) {
            if ($setting->getName() === $settingName) {
                return true;
            }
        }

        return false;
    }

    public function hasModuleSettings(): bool
    {
        return !empty($this->moduleSettings);
    }

    /**
     * @throws ModuleSettingNotFountException
     */
    public function getModuleSetting(string $settingName): Setting
    {
        foreach ($this->getModuleSettings() as $setting) {
            if ($setting->getName() === $settingName) {
                return $setting;
            }
        }
        throw new ModuleSettingNotFountException("Module setting \"$settingName\" was not found in configuration.");
    }

    public function addModuleSetting(Setting $moduleSettings): self
    {
        $this->moduleSettings[] = $moduleSettings;

        return $this;
    }

    /**
     * @param Setting[] $moduleSettings
     */
    public function setModuleSettings(array $moduleSettings): self
    {
        $this->moduleSettings = $moduleSettings;

        return $this;
    }
}
