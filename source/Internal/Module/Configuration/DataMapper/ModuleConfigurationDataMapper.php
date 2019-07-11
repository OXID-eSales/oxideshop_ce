<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;

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
            'id'          => $configuration->getId(),
            'path'        => $configuration->getPath(),
            'version'     => $configuration->getVersion(),
            'autoActive'  => $configuration->isAutoActive(),
            'title'       => $configuration->getTitle(),
            'description' => $configuration->getDescription(),
            'lang'        => $configuration->getLang(),
            'thumbnail'   => $configuration->getThumbnail(),
            'author'      => $configuration->getAuthor(),
            'url'         => $configuration->getUrl(),
            'email'       => $configuration->getEmail(),
            'settings'    => $this->getSettingsData($configuration),
            ModuleConfigurationMappingKeys::CLASS_EXTENSIONS => $this->getClassExtension($configuration)
        ];

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

        if (isset($data['settings'])) {
            $this->setSettings($moduleConfiguration, $data['settings']);
        }

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $settingsData
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
     * @param array               $extension
     */
    private function setClassExtension(ModuleConfiguration $moduleConfiguration, array $extension): void
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
}
