<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfiguration\TemplateBlocksMappingKeys;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\TemplateBlock;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\Template;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\SmartyPluginDirectory;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\Event;

/**
 * @internal
 */
class ModuleConfigurationDataMapper implements ModuleConfigurationDataMapperInterface
{
    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [
            'id' => $configuration->getId(),
            'path' => $configuration->getPath(),
            'version' => $configuration->getVersion(),
            'autoActive' => $configuration->isAutoActive(),
            'title' => $configuration->getTitle(),
            'description' => $configuration->getDescription(),
            'lang' => $configuration->getLang(),
            'thumbnail' => $configuration->getThumbnail(),
            'author' => $configuration->getAuthor(),
            'url' => $configuration->getUrl(),
            'email' => $configuration->getEmail(),
            'settings' => $this->getSettingsData($configuration),
        ];
        if ($configuration->hasTemplates()) {
            $data[ModuleConfigurationMappingKeys::TEMPLATES] =  $this->getTemplates($configuration);
        }
        if ($configuration->hasClassExtensions()) {
            $data[ModuleConfigurationMappingKeys::CLASS_EXTENSIONS] = $this->getClassExtension($configuration);
        }
        if ($configuration->hasControllers()) {
            $data[ModuleConfigurationMappingKeys::CONTROLLERS] = $this->getController($configuration);
        }
        if ($configuration->hasSmartyPluginDirectories()) {
            $data[ModuleConfigurationMappingKeys::SMARTY_PLUGIN_DIRECTORIES] =
                $this->getSmartyPluginDirectory($configuration);
        }
        if ($configuration->hasTemplateBlocks()) {
            $data[ModuleConfigurationMappingKeys::TEMPLATE_BLOCKS] = $this->getTemplateBlocks($configuration);
        }
        if ($configuration->hasEvents()) {
            $data[ModuleConfigurationMappingKeys::EVENTS] =
                $this->getEvent($configuration);
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return ModuleConfiguration
     */
    public function fromData(array $data): ModuleConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($data['id'])
            ->setPath($data['path'])
            ->setVersion($data['version'])
            ->setAutoActive($data['autoActive'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setLang($data['lang'])
            ->setThumbnail($data['thumbnail'])
            ->setAuthor($data['author'])
            ->setUrl($data['url'])
            ->setEmail($data['email']);

        if (isset($data[ModuleConfigurationMappingKeys::CLASS_EXTENSIONS])) {
            $this->setClassExtension($moduleConfiguration, $data[ModuleConfigurationMappingKeys::CLASS_EXTENSIONS]);
        }
        if (isset($data[ModuleConfigurationMappingKeys::TEMPLATES])) {
            $this->setTemplates($moduleConfiguration, $data[ModuleConfigurationMappingKeys::TEMPLATES]);
        }

        if (isset($data[ModuleConfigurationMappingKeys::CONTROLLERS])) {
            $this->setController($moduleConfiguration, $data[ModuleConfigurationMappingKeys::CONTROLLERS]);
        }

        if (isset($data[ModuleConfigurationMappingKeys::SMARTY_PLUGIN_DIRECTORIES])) {
            $this->setSmartyPluginDirectory(
                $moduleConfiguration,
                $data[ModuleConfigurationMappingKeys::SMARTY_PLUGIN_DIRECTORIES]
            );
        }

        if (isset($data[ModuleConfigurationMappingKeys::TEMPLATE_BLOCKS])) {
            $this->setTemplateBlocks($moduleConfiguration, $data[ModuleConfigurationMappingKeys::TEMPLATE_BLOCKS]);
        }

        if (isset($data[ModuleConfigurationMappingKeys::EVENTS])) {
            $this->setEvent($moduleConfiguration, $data[ModuleConfigurationMappingKeys::EVENTS]);
        }

        if (isset($data['settings'])) {
            $this->setSettings($moduleConfiguration, $data['settings']);
        }

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array $settingsData
     */
    private function setSettings(ModuleConfiguration $moduleConfiguration, array $settingsData): void
    {
        $settings = $this->getMappedSettings($settingsData);

        foreach ($settings as $setting) {
            $moduleConfiguration->addSetting(
                $setting
            );
        }
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return array
     */
    private function getSettingsData(ModuleConfiguration $moduleConfiguration): array
    {
        $data = [];

        foreach ($moduleConfiguration->getSettings() as $setting) {
            $data[$setting->getName()] = $setting->getValue();
        }

        return $data;
    }

    /**
     * @param array $settingsData
     *
     * @return array
     */
    private function getMappedSettings(array $settingsData): array
    {
        $settings = [];
        foreach ($settingsData as $settingName => $settingValue) {
            $settings[] = new ModuleSetting($settingName, $settingValue);
        }

        return $settings;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array $extension
     */
    private function setClassExtension(ModuleConfiguration $moduleConfiguration, array $extension)
    {
        foreach ($extension as $shopClass => $moduleClass) {
            $moduleConfiguration->addClassExtension(new ClassExtension(
                $shopClass,
                $moduleClass
            ));
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    private function getClassExtension(ModuleConfiguration $configuration): array
    {
        $extensions = [];

        if ($configuration->hasClassExtensions()) {
            foreach ($configuration->getClassExtensions() as $extension) {
                $extensions[$extension->getShopClassNamespace()] = $extension->getModuleExtensionClassNamespace();
            }
        }

        return $extensions;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array $template
     */
    private function setTemplates(ModuleConfiguration $moduleConfiguration, array $template): void
    {
        foreach ($template as $templateKey => $templatePath) {
            $moduleConfiguration->addTemplate(new Template(
                $templateKey,
                $templatePath
            ));
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    private function getTemplates(ModuleConfiguration $configuration): array
    {
        $templates = [];

        if ($configuration->hasTemplates()) {
            foreach ($configuration->getTemplates() as $template) {
                $templates[$template->getTemplateKey()] = $template->getTemplatePath();
            }
        }

        return $templates;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $controllers
     */
    private function setController(ModuleConfiguration $moduleConfiguration, array $controllers)
    {
        foreach ($controllers as $id => $controllerClassNamespace) {
            $moduleConfiguration->addController(new Controller(
                $id,
                $controllerClassNamespace
            ));
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    private function getController(ModuleConfiguration $configuration): array
    {
        $controllers = [];

        if ($configuration->hasControllers()) {
            foreach ($configuration->getControllers() as $controller) {
                $controllers[$controller->getId()] = $controller->getControllerClassNameSpace();
            }
        }

        return $controllers;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $directories
     */
    private function setSmartyPluginDirectory(ModuleConfiguration $moduleConfiguration, array $directories): void
    {
        foreach ($directories as $directory) {
            $moduleConfiguration->addSmartyPluginDirectory(new SmartyPluginDirectory(
                $directory
            ));
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    private function getSmartyPluginDirectory(ModuleConfiguration $configuration): array
    {
        $directories = [];

        if ($configuration->hasSmartyPluginDirectories()) {
            foreach ($configuration->getSmartyPluginDirectories() as $directory) {
                $directories[] = $directory->getDirectory();
            }
        }

        return $directories;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @return array
     */
    private function getTemplateBlocks(ModuleConfiguration $moduleConfiguration): array
    {
        $data = [];

        foreach ($moduleConfiguration->getTemplateBlocks() as $key => $templateBlock) {
            $data[$key] = [
                TemplateBlocksMappingKeys::SHOP_TEMPLATE_PATH => $templateBlock->getShopTemplatePath(),
                TemplateBlocksMappingKeys::BLOCK_NAME => $templateBlock->getBlockName(),
                TemplateBlocksMappingKeys::MODULE_TEMPLATE_PATH => $templateBlock->getModuleTemplatePath(),
            ];
            if ($templateBlock->getPosition() !== 0) {
                $data[$key][TemplateBlocksMappingKeys::POSITION] = $templateBlock->getPosition();
            }
            if ($templateBlock->getTheme() !== '') {
                $data[$key][TemplateBlocksMappingKeys::THEME] = $templateBlock->getTheme();
            }
        }

        return $data;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array $templateBlocks
     */
    private function setTemplateBlocks(ModuleConfiguration $moduleConfiguration, array $templateBlocks): void
    {
        foreach ($templateBlocks as $templateBlockData) {
            $templateBlock = new TemplateBlock(
                $templateBlockData[TemplateBlocksMappingKeys::SHOP_TEMPLATE_PATH],
                $templateBlockData[TemplateBlocksMappingKeys::BLOCK_NAME],
                $templateBlockData[TemplateBlocksMappingKeys::MODULE_TEMPLATE_PATH]
            );
            if (isset($templateBlockData[TemplateBlocksMappingKeys::POSITION])) {
                $templateBlock->setPosition($templateBlockData[TemplateBlocksMappingKeys::POSITION]);
            }
            if (isset($templateBlockData[TemplateBlocksMappingKeys::THEME])) {
                $templateBlock->setTheme($templateBlockData[TemplateBlocksMappingKeys::THEME]);
            }
            $moduleConfiguration->addTemplateBlock($templateBlock);
        }
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $event
     */
    private function setEvent(ModuleConfiguration $moduleConfiguration, array $event)
    {
        foreach ($event as $action => $method) {
            $moduleConfiguration->addEvent(new Event(
                $action,
                $method
            ));
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    private function getEvent(ModuleConfiguration $configuration): array
    {
        $events = [];

        if ($configuration->hasEvents()) {
            foreach ($configuration->getEvents() as $event) {
                $events[$event->getAction()] = $event->getMethod();
            }
        }

        return $events;
    }
}
