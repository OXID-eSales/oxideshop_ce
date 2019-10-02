<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Template;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Event;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\SmartyPluginDirectory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\TemplateBlock;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassWithoutNamespace;
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
     * @var ClassWithoutNamespace[]
     */
    private $classesWithoutNamespace = [];

    /**
     * @var Setting[]
     */
    private $moduleSettings = [];

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
    public function isConfigured(): bool
    {
        return $this->configured;
    }

    /**
     * @param bool $configured
     * @return ModuleConfiguration
     */
    public function setConfigured(bool $configured): ModuleConfiguration
    {
        $this->configured = $configured;

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
     * @return ClassExtension[]
     */
    public function getClassExtensions(): array
    {
        return $this->classExtensions;
    }

    /**
     * @param ClassExtension $extension
     *
     * @return $this
     */
    public function addClassExtension(ClassExtension $extension)
    {
        $this->classExtensions[] = $extension;

        return $this;
    }

    /**
     * @return bool
     */
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

    /**
     * @return bool
     */
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

    /**
     * @return bool
     */
    public function hasTemplates(): bool
    {
        return !empty($this->templates);
    }

    /**
     * @param string $namespace
     *
     * @return bool
     */
    public function hasClassExtension(string $namespace): bool
    {
        foreach ($this->getClassExtensions() as $classExtension) {
            if ($classExtension->getModuleExtensionClassName() === $namespace) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $shopClassNamespace
     *
     * @return bool
     */
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
     * @param Controller $controller
     *
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

    /**
     * @return bool
     */
    public function hasControllers(): bool
    {
        return !empty($this->controllers);
    }

    /**
     * @param SmartyPluginDirectory $directory
     *
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

    /**
     * @return bool
     */
    public function hasSmartyPluginDirectories(): bool
    {
        return !empty($this->smartyPluginDirectories);
    }

    /**
     * @param Event $event
     *
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

    /**
     * @return bool
     */
    public function hasEvents(): bool
    {
        return !empty($this->events);
    }

    /**
     * @param ClassWithoutNamespace $class
     *
     * @return $this
     */
    public function addClassWithoutNamespace(ClassWithoutNamespace $class)
    {
        $this->classesWithoutNamespace[] = $class;

        return $this;
    }

    /**
     * @return ClassWithoutNamespace[]
     */
    public function getClassesWithoutNamespace(): array
    {
        return $this->classesWithoutNamespace;
    }

    /**
     * @return bool
     */
    public function hasClassWithoutNamespaces(): bool
    {
        return !empty($this->classesWithoutNamespace);
    }

    /**
     * @return Setting[]
     */
    public function getModuleSettings(): array
    {
        return $this->moduleSettings;
    }

    /**
     * @param string $settingName
     *
     * @return bool
     */
    public function hasModuleSetting(string $settingName): bool
    {
        foreach ($this->getModuleSettings() as $setting) {
            if ($setting->getName() === $settingName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasModuleSettings(): bool
    {
        return !empty($this->moduleSettings);
    }

    /**
     * @param string $settingName
     * @return Setting
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

    /**
     * @param Setting $moduleSettings
     * @return ModuleConfiguration
     */
    public function addModuleSetting(Setting $moduleSettings): ModuleConfiguration
    {
        $this->moduleSettings[] = $moduleSettings;
        return $this;
    }

    /**
     * @param Setting[] $moduleSettings
     * @return ModuleConfiguration
     */
    public function setModuleSettings(array $moduleSettings): ModuleConfiguration
    {
        $this->moduleSettings = $moduleSettings;
        return $this;
    }
}
